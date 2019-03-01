<?php
namespace ERP\Model\Users\Commissions;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CommissionModel extends Model
{
	protected $table = 'staff_commission_mst';
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
		
		$getCommissionData = array();
		$getCommissionKey = array();
		$getCommissionData = func_get_arg(0);
		$getCommissionKey = func_get_arg(1);
		$commissionData="";
		$keyName = "";
		for($data=0;$data<count($getCommissionData);$data++)
		{
			if($data == (count($getCommissionData)-1))
			{
				$commissionData = $commissionData."'".$getCommissionData[$data]."'";
				$keyName =$keyName.$getCommissionKey[$data];
			}
			else
			{
				$commissionData = $commissionData."'".$getCommissionData[$data]."',";
				$keyName =$keyName.$getCommissionKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into staff_commission_mst(".$keyName.",created_at) 
		values(".$commissionData.",'".$mytime."')");
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
	}/**
	 * insert itemwise data 
	 * @param  array
	 * returns the status
	*/
	public function insertItemwiseData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$getCommissionData = array();
		$getCommissionKey = array();
		$getCommissionData = func_get_arg(0);
		$getCommissionKey = func_get_arg(1);
		$commissionData="";
		$keyName = "";
		for($data=0;$data<count($getCommissionData);$data++)
		{
			if($data == (count($getCommissionData)-1))
			{
				$commissionData = $commissionData."'".$getCommissionData[$data]."'";
				$keyName =$keyName.$getCommissionKey[$data];
			}
			else
			{
				$commissionData = $commissionData."'".$getCommissionData[$data]."',";
				$keyName =$keyName.$getCommissionKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into product_commission_dtl(".$keyName.",created_at) 
		values(".$commissionData.",'".$mytime."')");
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
	public function getItemwiseData($condition = array())
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$whereStr = "product_commission_dtl.deleted_at = '0000-00-00 00:00:00' AND pmst.deleted_at = '0000-00-00 00:00:00'";
		if (count($condition) > 0) {
			foreach ($condition as $key => $value) {
				$whereStr .= " AND product_commission_dtl.$key = '$value'";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT
		product_commission_dtl.product_commission_id,
		product_commission_dtl.product_id,
		product_commission_dtl.company_id,
		product_commission_dtl.commission_from_qty,
		product_commission_dtl.commission_to_qty,
		product_commission_dtl.commission_rate,
		product_commission_dtl.commission_rate_type,
		product_commission_dtl.commission_calc_on,
		product_commission_dtl.created_at,
		product_commission_dtl.updated_at,
		pmst.product_name,
		pmst.mrp
		FROM product_commission_dtl
		JOIN product_mst as pmst ON pmst.product_id = product_commission_dtl.product_id
		WHERE $whereStr ORDER BY product_commission_dtl.updated_at DESC");
		DB::commit();
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
	 * update data 
	 * @param  commission-data,key of commission-data,user-id
	 * returns the status
	*/
	public function updateData($commissionData,$key,$userId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($commissionData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$commissionData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update staff_commission_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where user_id = '".$userId."' and 
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
	 * update data 
	 * @param  commission-data,key of commission-data,user-id
	 * returns the status
	*/
	public function updateItemwiseData($commissionData,$key,$commissionId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($commissionData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$commissionData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update product_commission_dtl 
		set ".$keyValueString."updated_at='".$mytime."'
		where product_commission_id = '".$commissionId."' and 
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
	 * update data 
	 * @param  commission-data,key of commission-data,user-id
	 * returns the status
	*/
	public function deleteItemwiseData($commissionId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update product_commission_dtl 
		set deleted_at='".$mytime."'
		where product_commission_id = '".$commissionId."'");
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
	 * get data as per given user Id
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
		commission_id,
		user_id,
		commission_status,
		commission_rate,
		commission_rate_type,
		commission_type,
		commission_calc_on,
		commission_for,
		created_at,
		updated_at
		from staff_commission_mst 
		where user_id ='".$userId."' and 
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
	public function getAllData()
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		commission_id,
		user_id,
		commission_status,
		commission_rate,
		commission_rate_type,
		commission_type,
		commission_calc_on,
		commission_for,
		created_at,
		updated_at
		from staff_commission_mst 
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
}