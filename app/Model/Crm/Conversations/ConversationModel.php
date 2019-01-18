<?php
namespace ERP\Model\Crm\Conversations;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Clients\ClientModel;
use ERP\Model\Authenticate\AuthenticateModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ConversationModel extends Model
{
	protected $table = 'conversation_dtl';
	
	/**
	 * insert data 
	 * @param  array
	 * returns the status
	*/
	public function insertEmailData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$getDataArray = func_get_arg(0);
		$getKeyData = func_get_arg(1);
		$document = func_get_arg(2);
		$userId = func_get_arg(3);
		$conversationData='';
		$keyName = "";
		$queryArrayCount = count($getDataArray);
		for($insertQueryIndex=0;$insertQueryIndex<$queryArrayCount;$insertQueryIndex++)
		{
			$conversationData="";
			$keyName = "";
			
			$innerArrayCount = count($getDataArray[$insertQueryIndex]);
			for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
			{
				$keyName = $keyName.$getKeyData[$insertQueryIndex][$innerArray].',';
				$conversationData = $conversationData."'".$getDataArray[$insertQueryIndex][$innerArray]."',";
			}
			$documentKey='';
			$documentData='';
			if(count($document)!=0)
			{
				$documentKey = "attachment_name,attachment_format,attachment_size,attachment_path";
				$documentData = "'".$document[0][0]."','".$document[0][2]."','".$document[0][1]."','".$document[0][3]."'";
			}
			else
			{
				$keyName = rtrim($keyName,',');
				$conversationData = rtrim($conversationData,',');
			}
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into conversation_dtl(".$keyName."".$documentKey.",user_id,created_at)
			values(".$conversationData."".$documentData.",'".$userId."','".$mytime."')");
			DB::commit();
		}
		if($raw==1)
		{
			return $exceptionArray['200'];
		}
	}
	
	/**
	 * insert data 
	 * @param  array
	 * returns the status
	*/
	public function insertSmsData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$getDataArray = func_get_arg(0);
		$getKeyData = func_get_arg(1);
		$document = func_get_arg(2);
		$userId = func_get_arg(3);
		$conversationData='';
		$keyName = "";
		$queryArrayCount = count($getDataArray);
		for($insertQueryIndex=0;$insertQueryIndex<$queryArrayCount;$insertQueryIndex++)
		{
			$conversationData="";
			$keyName = "";
			
			$innerArrayCount = count($getDataArray[$insertQueryIndex]);
			for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
			{
				$keyName = $keyName.$getKeyData[$insertQueryIndex][$innerArray].',';
				$conversationData = $conversationData."'".$getDataArray[$insertQueryIndex][$innerArray]."',";
			}
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into conversation_dtl(".$keyName."user_id,created_at)
			values(".$conversationData."".$userId.",'".$mytime."')");
			DB::commit();
		}
		if($raw==1)
		{
			return $exceptionArray['200'];
		}
	}
	
	/**
	 * insert bill-mail data 
	 * @param  array
	 * returns the status
	*/
	public function saveMailDataFromBill($emailId,$subject,$conversationType,$conversation,$documentName,$documentFormat,$documentPath,$comment,$companyId,$clientId,$headerData)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$headerData['authenticationtoken'][0] = $_SERVER['HTTP_AUTHENTICATIONTOKEN'];
		//get user-id
		$authenticationModel = new AuthenticateModel();
		$userData = $authenticationModel->getActiveUser($headerData);
		if(!is_array($userData))
		{
			if(strcmp($exceptionArray['userLogin'],$userData)==0)
			{
				return $exceptionArray['userLogin'];
			}
		}
		$userId = $userData[0]->user_id;
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into conversation_dtl
		(email_id,subject,conversation,conversation_type,attachment_name,attachment_format,attachment_path,comment,client_id,
		company_id,user_id,created_at)values('".$emailId."','".$subject."','".$conversation."','".$conversationType."','".$documentName."','".$documentFormat."','".$documentPath."',
		'".$comment."','".$clientId."','".$companyId."','".$userId."','".$mytime."')");
		DB::commit();
		
		if($raw==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}

	/**
	 * insert bill-mail data 
	 * @param  array
	 * returns the status
	*/
	public function getExistingConversationData($clientId,$commentMessage,$type)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();

		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		//get today date
		$afterDate = date("Y-m-d", time() + 86400);
		$beforeDate = date("Y-m-d", time() - 86400);
		DB::beginTransaction();
		$conversationData = DB::connection($databaseName)->select("select
        conversation_id,
        email_id,
        conversation,
        conversation_type,
        comment,
        created_at,
        deleted_at,
        company_id,
        client_id
        from conversation_dtl
        where deleted_at='0000-00-00 00:00:00' and 
        client_id='".$clientId."' and 
        conversation_type='".$type."' and 
        comment='".$commentMessage."' and
        DATE_FORMAT(created_at, '%m-%d-&Y') >= DATE_FORMAT('".$beforeDate."', '%m-%d-&Y') and
        DATE_FORMAT(created_at, '%m-%d-&Y') <= DATE_FORMAT('".$afterDate."', '%m-%d-&Y')");
        DB::commit();
        if(count($conversationData)!=0)
        {
        	return $exceptionArray['500'];
        }
        else
        {
        	return $exceptionArray['200'];
        }
	}
}
