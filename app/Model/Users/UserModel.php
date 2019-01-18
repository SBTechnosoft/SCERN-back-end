<?php
namespace ERP\Model\Users;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Http\Requests;
use Illuminate\Http\Request;
use ERP\Core\Users\Entities\UserArray;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserModel extends Model
{
	protected $table = 'user_mst';
	
	/**
	 * insert data 
	 * @param  array
	 * returns the status
	*/
	public function insertData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$getUserData = array();
		$getUserKey = array();
		$getUserData = func_get_arg(0);
		$getUserKey = func_get_arg(1);
		$userData="";
		$keyName = "";
		for($data=0;$data<count($getUserData);$data++)
		{
			if($data == (count($getUserData)-1))
			{
				$userData = $userData."'".$getUserData[$data]."'";
				$keyName =$keyName.$getUserKey[$data];
			}
			else
			{
				$userData = $userData."'".$getUserData[$data]."',";
				$keyName =$keyName.$getUserKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into user_mst(".$keyName.",created_at) 
		values(".$userData.",'".$mytime."')");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
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
	 * update data 
	 * @param user-id,user-data and key of user-data
	 * returns the status
	*/
	public function updateData($userData,$key,$userId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($userData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$userData[$data]."',";
		}
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update user_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where user_id = '".$userId."' and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
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
	 * get All data 
	 * returns the status
	*/
	public function getAllData(Request $request)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		if(array_key_exists('companyid',$request->header()) || array_key_exists('branchid',$request->header()))
		{
			$userArray = new UserArray();
			$userArrayData = $userArray->userSearching();
			$querySet = "";
			for($arrayData=0;$arrayData<count($userArrayData);$arrayData++)
			{
				if(array_key_exists(array_keys($userArrayData)[$arrayData],$request->header()))
				{
					$querySet = $querySet.$userArrayData[array_keys($userArrayData)[$arrayData]]." = ".$request->header()[array_keys($userArrayData)[$arrayData]][0]." and ";
				}
			}
			DB::beginTransaction();	
			$raw = DB::connection($databaseName)->select("select 
			user_id,
			user_name,
			user_type,
			email_id,
			password,
			contact_no,
			address,
			pincode,
			state_abb,
			city_id,
			company_id,
			branch_id,
			permission_array,
			default_company_id,
			created_at,
			updated_at
			from user_mst where ".$querySet." deleted_at='0000-00-00 00:00:00' ");
			DB::commit();
		}
		else
		{
			DB::beginTransaction();		
			$raw = DB::connection($databaseName)->select("select 
			user_id,
			user_name,
			user_type,
			email_id,
			password,
			contact_no,
			address,
			pincode,
			state_abb,
			city_id,
			company_id,
			branch_id,
			permission_array,
			default_company_id,
			created_at,
			updated_at
			from user_mst where deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$enocodedData = json_encode($raw);
			return $enocodedData;
		}
	}
	
	/**
	 * get data as per given user_id
	 * @param $userId
	 * returns the status
	*/
	public function getData($userId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		user_id,
		user_name,
		user_type,
		email_id,
		password,
		contact_no,
		address,
		pincode,
		state_abb,
		city_id,
		company_id,
		branch_id,
		permission_array,
		default_company_id,
		created_at,
		updated_at
		from user_mst where user_id = '".$userId."' and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$enocodedData = json_encode($raw,true); 
			return $enocodedData;
		}
	}
	
	//delete
	public function deleteData($userId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$raw = DB::connection($databaseName)->statement("update user_mst 
		set deleted_at='".$mytime."'
		where user_id = '".$userId."'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			$activeSession = DB::statement("delete 
			from active_session 
			where user_id = '".$userId."'");
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}
