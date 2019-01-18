<?php
namespace ERP\Model\Settings\InvoiceNumbers;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InvoiceModel extends Model
{
	protected $table = 'invoice_dtl';
	
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
		$getInvoiceData = array();
		$getInvoiceKey = array();
		$getInvoiceData = func_get_arg(0);
		$getInvoiceKey = func_get_arg(1);
		$invoiceData="";
		$keyName = "";
		for($data=0;$data<count($getInvoiceData);$data++)
		{
			if($data == (count($getInvoiceData)-1))
			{
				$invoiceData = $invoiceData."'".$getInvoiceData[$data]."'";
				$keyName =$keyName.$getInvoiceKey[$data];
			}
			else
			{
				$invoiceData = $invoiceData."'".$getInvoiceData[$data]."',";
				$keyName =$keyName.$getInvoiceKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into invoice_dtl(".$keyName.",created_at) 
		values(".$invoiceData.",'".$mytime."')");
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
	 * @param  ledger-data,key of ledger-data,ledger-id
	 * returns the status
	*/
	public function updateData($invoiceData,$key,$invoiceId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($invoiceData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$invoiceData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update invoice_dtl 
		set ".$keyValueString."updated_at='".$mytime."'
		where invoice_id = '".$invoiceId."' and 
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
		invoice_id,
		invoice_label,
		invoice_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id			
		from invoice_dtl 
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
	 * get data as per given Invoice Id
	 * @param $invoiceId
	 * returns the status
	*/
	public function getData($invoiceId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		invoice_id,
		invoice_label,
		invoice_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id
		from invoice_dtl 
		where invoice_id = ".$invoiceId." and 
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
	public function getAllInvoiceData($companyId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();	
		$raw = DB::connection($databaseName)->select("select 
		invoice_id,
		invoice_label,
		invoice_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id
		from invoice_dtl 
		where company_id=".$companyId." and 
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
	public function getLatestInvoiceData($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();	
		$raw = DB::connection($databaseName)->select("SELECT 
		invoice_id,
		invoice_label,
		invoice_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id		
		FROM invoice_dtl 
		where company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00' 
		group by invoice_id desc limit 1");
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
