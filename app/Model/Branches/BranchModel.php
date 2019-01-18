<?php
namespace ERP\Model\Branches;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\EnumClasses\IsDefaultEnum;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BranchModel extends Model
{
	protected $table = 'branch_mst';
	
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
		
		$getBranchData = array();
		$getBranchKey = array();
		$getBranchData = func_get_arg(0);
		$getBranchKey = func_get_arg(1);
		$branchData="";
		$keyName = "";
		for($data=0;$data<count($getBranchData);$data++)
		{
			if($data == (count($getBranchData)-1))
			{
				$branchData = $branchData."'".$getBranchData[$data]."'";
				$keyName =$keyName.$getBranchKey[$data];
			}
			else
			{
				$branchData = $branchData."'".$getBranchData[$data]."',";
				$keyName =$keyName.$getBranchKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into branch_mst(".$keyName.",created_at) 
		values(".$branchData.",'".$mytime."')");
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
	 * @param  branch-data,key of branch-data,branch-id
	 * returns the status
	*/
	public function updateData($branchData,$key,$branchId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//only one branch is checked by default
		$enumIsDefArray = array();
		$isDefEnum = new IsDefaultEnum();
		$enumIsDefArray = $isDefEnum->enumArrays();
		for($keyData=0;$keyData<count($key);$keyData++)
		{
		    if(strcmp($key[array_keys($key)[$keyData]],"is_default")==0)
			{
				if(strcmp($branchData[$keyData],$enumIsDefArray['default'])==0)
				{
					$raw  = DB::connection($databaseName)->statement("update branch_mst 
					set is_default='".$enumIsDefArray['notDefault']."',updated_at='".$mytime."' 
					where deleted_at = '0000-00-00 00:00:00'");
					if($raw==0)
					{
						return $exceptionArray['500'];
					}
				}
			}	
		}
		
		for($data=0;$data<count($branchData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$branchData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update branch_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where branch_id = '".$branchId."' and deleted_at='0000-00-00 00:00:00'");
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
		branch_id,
		branch_name,
		address1,
		address2,
		pincode,
		is_display,
		is_default,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		company_id			
		from branch_mst where deleted_at='0000-00-00 00:00:00'");
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
	 * get data as per given Branch Id
	 * @param $branchId
	 * returns the status
	*/
	public function getData($branchId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		branch_id,
		branch_name,
		address1,
		address2,
		pincode,
		is_display,
		is_default,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		company_id	
		from branch_mst where branch_id = ".$branchId." and deleted_at='0000-00-00 00:00:00'");
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
	public function getAllBranchData($companyId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		branch_id,
		branch_name,
		address1,
		address2,
		pincode,
		is_display,
		is_default,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		company_id
		from branch_mst where company_id ='".$companyId."' and  deleted_at='0000-00-00 00:00:00'");
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
	 * get data as per given branch-name
	 * returns the error-message/branchId
	*/
	public function getBranchId($convertedBranchName)
	{
		$flag=0;
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$branchResult = DB::connection($databaseName)->select("SELECT 
		branch_id,
		branch_name
		from branch_mst 
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		for($dataArray=0;$dataArray<count($branchResult);$dataArray++)
		{
			$convertedBranchString = strtoupper($branchResult[$dataArray]->branch_name);
			//replace string of db group-name
			$convertedDbBrachString = preg_replace('/[^A-Za-z0-9]/', '',$convertedBranchString);
			if(strcmp($convertedDbBrachString,$convertedBranchName)==0)
			{
				$flag=1;
				$branchId = $branchResult[$dataArray]->branch_id;
				break;
			}
		}
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($flag==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			return $branchId;
		}
	}
	
	//delete
	public function deleteData($branchId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$raw = DB::connection($databaseName)->statement("update product_mst 
		set deleted_at='".$mytime."' 
		where branch_id=".$branchId);
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			$product = DB::connection($databaseName)->statement("update branch_mst 
			set deleted_at='".$mytime."' 
			where branch_id=".$branchId);
			if($product==1)
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
