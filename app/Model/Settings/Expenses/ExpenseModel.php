<?php
namespace ERP\Model\Settings\Expenses;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExpenseModel extends Model
{
	protected $table = 'expense_type_mst';
	
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
		
		$getExpenseData = array();
		$getExpenseKey = array();
		$getExpenseData = func_get_arg(0);
		$getExpenseKey = func_get_arg(1);
		$expenseData="";
		$keyName = "";
		for($data=0;$data<count($getExpenseData);$data++)
		{
			if($data == (count($getExpenseData)-1))
			{
				$expenseData = $expenseData."'".$getExpenseData[$data]."'";
				$keyName =$keyName.$getExpenseKey[$data];
			}
			else
			{
				$expenseData = $expenseData."'".$getExpenseData[$data]."',";
				$keyName =$keyName.$getExpenseKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into expense_type_mst(".$keyName.",created_at) 
		values(".$expenseData.",'".$mytime."')");
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
	 * @param  expense-data,key of expense-data,expense-id
	 * returns the status
	*/
	public function updateData($expenseData,$key,$expenseId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($expenseData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$expenseData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update expense_type_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where expense_id = '".$expenseId."' and 
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
		expense_id,
		expense_name,
		expense_type,
		expense_value,
		updated_at,
		created_at,
		company_id
		from expense_type_mst 
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
	 * get data as per given Expense Id
	 * @param $expenseId
	 * returns the status
	*/
	public function getData($expenseId)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		expense_id,
		expense_name,
		expense_type,
		expense_value,
		updated_at,
		created_at,
		company_id
		from expense_type_mst 
		where expense_id ='".$expenseId."' and 
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
	
	//delete
	public function deleteData($expenseId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$expenseDeleteResult = DB::connection($databaseName)->statement("update expense_type_mst 
		set deleted_at='".$mytime."' 
		where expense_id=".$expenseId);
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($expenseDeleteResult==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}
