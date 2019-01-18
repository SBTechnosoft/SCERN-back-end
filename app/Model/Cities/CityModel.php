<?php
namespace ERP\Model\Cities;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityModel extends Model
{
	protected $table = 'city_mst';
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
		
		$getCityData = array();
		$getCityKey = array();
		$getCityData = func_get_arg(0);
		$getCityKey = func_get_arg(1);
		$cityData="";
		$keyName = "";
		for($data=0;$data<count($getCityData);$data++)
		{
			if($data == (count($getCityData)-1))
			{
				$cityData = $cityData."'".$getCityData[$data]."'";
				$keyName =$keyName.$getCityKey[$data];
			}
			else
			{
				$cityData = $cityData."'".$getCityData[$data]."',";
				$keyName =$keyName.$getCityKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into city_mst(".$keyName.",created_at) 
		values(".$cityData.",'".$mytime."')");
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
	 * @param city-data,key of city-data  and city-id
	 * returns the status
	*/
	public function updateData($cityData,$key,$cityId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($cityData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$cityData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update city_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where city_id = '".$cityId."' and deleted_at='0000-00-00 00:00:00'");
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
		city_id,
		city_name,
		is_display,
		created_at,
		updated_at,
		deleted_at,
		state_abb
		from city_mst where deleted_at='0000-00-00 00:00:00'");
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
	 * get data as per given city_id
	 * @param $cityId
	 * returns the status
	*/
	public function getData($cityId)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		city_id,
		city_name,
		is_display,
		created_at,
		updated_at,
		deleted_at,
		state_abb
		from city_mst where city_id='".$cityId."' and deleted_at='0000-00-00 00:00:00'");
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
	 * get data as per given city_id
	 * @param $cityId
	 * returns the status
	*/
	public function getAllCityData($stateAbb)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		city_id,
		city_name,
		is_display,
		created_at,
		updated_at,
		deleted_at,
		state_abb
		from city_mst where state_abb='".$stateAbb."' and deleted_at='0000-00-00 00:00:00'");
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
	public function deleteData($cityId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$raw = DB::connection($databaseName)->statement("update city_mst 
		set deleted_at='".$mytime."'
		where city_id = '".$cityId."'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			$branch = DB::connection($databaseName)->statement("update branch_mst 
			set deleted_at='".$mytime."'
			where city_id = '".$cityId."'");
			$company = DB::connection($databaseName)->statement("update company_mst 
			set deleted_at='".$mytime."'
			where city_id = '".$cityId."'");
			if($branch==1 && $company==1)
			{
				return $exceptionArray['200'];
			}
			else
			{
				return $exceptionArray['500'];
			}
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}
