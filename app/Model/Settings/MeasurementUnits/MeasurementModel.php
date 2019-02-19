<?php
namespace ERP\Model\Settings\MeasurementUnits;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class MeasurementModel extends Model
{
	protected $table = 'measurement_unit_mst';
	
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
		
		$getMeasurementData = array();
		$getMeasurementKey = array();
		$getMeasurementData = func_get_arg(0);
		$getMeasurementKey = func_get_arg(1);
		$measurementData="";
		$keyName = "";
		for($data=0;$data<count($getMeasurementData);$data++)
		{
			if($data == (count($getMeasurementData)-1))
			{
				$measurementData = $measurementData."'".$getMeasurementData[$data]."'";
				$keyName =$keyName.$getMeasurementKey[$data];
			}
			else
			{
				$measurementData = $measurementData."'".$getMeasurementData[$data]."',";
				$keyName =$keyName.$getMeasurementKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into measurement_unit_mst(".$keyName.",created_at) 
		values(".$measurementData.",'".$mytime."')");
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
	 * @param  measurement-data,key of measurement-data,measurement-id
	 * returns the status
	*/
	public function updateData($measurementData,$key,$measurementId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($measurementData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$measurementData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update measurement_unit_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where measurement_unit_id = '".$measurementId."' and 
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
		measurement_unit_id,
		unit_name,
		length_status,
		width_status,
		height_status,
		updated_at,
		created_at
		from measurement_unit_mst 
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
	 * get data as per given Measurement Id
	 * @param $measurementId
	 * returns the status
	*/
	public function getData($measurementId)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		measurement_unit_id,
		unit_name,
		length_status,
		width_status,
		height_status,
		updated_at,
		created_at
		from measurement_unit_mst 
		where measurement_unit_id ='".$measurementId."' and 
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
	public function deleteData($measurementId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$expenseDeleteResult = DB::connection($databaseName)->statement("update measurement_unit_mst 
		set deleted_at='".$mytime."' 
		where measurement_unit_id=".$measurementId);
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
