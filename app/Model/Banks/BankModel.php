<?php
namespace ERP\Model\Banks;

use Illuminate\Database\Eloquent\Model;
use DB;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BankModel extends Model
{
	protected $table = 'bank_mst';
	
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
		bank_id,
		bank_name
		from bank_mst where deleted_at='0000-00-00 00:00:00'");
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
			return json_encode($raw);
		}
	}
	
	/**
	 * get data as per given Bank Id
	 * @param $bankId
	 * returns the status
	*/
	public function getData($bankId)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		bank_id,
		bank_name
		from bank_mst where bank_id = ".$bankId." and deleted_at='0000-00-00 00:00:00'");
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
			return json_encode($raw);
		}
	}
	
	/**
	 * get All data 
	 * returns the status
	*/
	public function getAllBranchData()
	{	
		// database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		ini_set('memory_limit', '256M');
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		bank_dtl_id,
		bank_id,
		branch_name,
		ifsc_code,
		is_default
		from bank_dtl");
		DB::commit();
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			return json_encode($raw);
		}
	}
	
	/**
	 * get data as per given Bank Id
	 * @param $bankId
	 * returns the status
	*/
	public function getBranchData($bankId)
	{		
		// database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		bank_dtl_id,
		bank_id,
		branch_name,
		ifsc_code,
		is_default
		from bank_dtl where bank_id = ".$bankId);
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			return json_encode($raw);
		}
	}
}
