<?php
namespace ERP\Model\Settings\Professions;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfessionModel extends Model
{
	protected $table = 'profession_mst';
	
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
		
		$getProfessionData = array();
		$getProfessionKey = array();
		$getProfessionData = func_get_arg(0);
		$getProfessionKey = func_get_arg(1);
		$professionData="";
		$keyName = "";
		for($data=0;$data<count($getProfessionData);$data++)
		{
			if($data == (count($getProfessionData)-1))
			{
				$professionData = $professionData."'".$getProfessionData[$data]."'";
				$keyName =$keyName.$getProfessionKey[$data];
			}
			else
			{
				$professionData = $professionData."'".$getProfessionData[$data]."',";
				$keyName =$keyName.$getProfessionKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into profession_mst(".$keyName.",created_at) 
		values(".$professionData.",'".$mytime."')");
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
	 * @param  profession-data,key of profession-data,profession-id
	 * returns the status
	*/
	public function updateData($professionData,$key,$professionId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($professionData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$professionData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update profession_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where profession_id = '".$professionId."' and 
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
		profession_id,
		profession_name,
		description,
		profession_parent_id,
		updated_at,
		created_at
		from profession_mst 
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
	 * get data as per given Profession Id
	 * @param $professionId
	 * returns the status
	*/
	public function getData($professionId)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		profession_id,
		profession_name,
		description,
		profession_parent_id,
		updated_at,
		created_at
		from profession_mst 
		where profession_id ='".$professionId."' and 
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
	public function deleteData($professionId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$professionDeleteResult = DB::connection($databaseName)->statement("update profession_mst 
		set deleted_at='".$mytime."' 
		where profession_id=".$professionId." OR profession_parent_id='".$professionId."'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($professionDeleteResult==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}
