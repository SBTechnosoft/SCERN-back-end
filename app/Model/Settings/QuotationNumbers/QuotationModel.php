<?php
namespace ERP\Model\Settings\QuotationNumbers;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationModel extends Model
{
	protected $table = 'quotation_dtl';
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
		
		// date_default_timezone_set("Asia/Calcutta");
		$getQuotationData = array();
		$getQuotationKey = array();
		$getQuotationData = func_get_arg(0);
		$getQuotationKey = func_get_arg(1);
		$quotationData="";
		$keyName = "";
		for($data=0;$data<count($getQuotationData);$data++)
		{
			if($data == (count($getQuotationData)-1))
			{
				$quotationData = $quotationData."'".$getQuotationData[$data]."'";
				$keyName =$keyName.$getQuotationKey[$data];
			}
			else
			{
				$quotationData = $quotationData."'".$getQuotationData[$data]."',";
				$keyName =$keyName.$getQuotationKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into quotation_dtl(".$keyName.",created_at) 
		values(".$quotationData.",'".$mytime."')");
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
	 * update data 
	 * @param  quotation-data,key of quotation-data,quotation-id
	 * returns the status
	*/
	public function updateData($quotationData,$key,$quotationId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($quotationData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$quotationData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update quotation_dtl 
		set ".$keyValueString."updated_at='".$mytime."'
		where quotation_id = '".$quotationId."' and 
		deleted_at='0000-00-00 00:00:00'");
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
	public function getAllData()
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		quotation_id,
		quotation_label,
		quotation_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id			
		from quotation_dtl 
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
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
	 * get data as per given quotation Id
	 * @param $quotationId
	 * returns the status
	*/
	public function getData($quotationId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		quotation_id,
		quotation_label,
		quotation_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id
		from quotation_dtl 
		where quotation_id = ".$quotationId." and 
		deleted_at='0000-00-00 00:00:00'");
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
	/**
	 * get All data 
	 * returns the status
	*/
	public function getAllQuotationData($companyId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		quotation_id,
		quotation_label,
		quotation_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id
		from quotation_dtl 
		where company_id =".$companyId." and 
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
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
	 * get latest data 
	 * returns the status
	*/
	public function getLatestQuotationData($companyId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();	
		$raw = DB::connection($databaseName)->select("SELECT 
		quotation_id,
		quotation_label,
		quotation_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id		
		FROM quotation_dtl 
		where company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00' 
		group by quotation_id desc limit 1");
		DB::commit();
		
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
}
