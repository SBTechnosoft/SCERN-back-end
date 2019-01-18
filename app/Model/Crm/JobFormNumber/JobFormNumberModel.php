<?php
namespace ERP\Model\Crm\JobFormNumber;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumberModel extends Model
{
	protected $table = 'job_card_number_dtl';
	
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
		
		$getJobFormNumberData = array();
		$getJobFormNumberKey = array();
		$getJobFormNumberData = func_get_arg(0);
		$getJobFormNumberKey = func_get_arg(1);
		$jobFormNumberData="";
		$keyName = "";
		for($data=0;$data<count($getJobFormNumberData);$data++)
		{
			if($data == (count($getJobFormNumberData)-1))
			{
				$jobFormNumberData = $jobFormNumberData."'".$getJobFormNumberData[$data]."'";
				$keyName =$keyName.$getJobFormNumberKey[$data];
			}
			else
			{
				$jobFormNumberData = $jobFormNumberData."'".$getJobFormNumberData[$data]."',";
				$keyName =$keyName.$getJobFormNumberKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into job_card_number_dtl(".$keyName.",created_at) 
		values(".$jobFormNumberData.",'".$mytime."')");
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
		job_card_number_id,
		job_card_number_label,
		job_card_number_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id			
		from job_card_number_dtl 
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
	 * get latest data 
	 * returns the status
	*/
	public function getLatestJobFormNumberData($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();	
		$raw = DB::connection($databaseName)->select("SELECT 
		job_card_number_id,
		job_card_number_label,
		job_card_number_type,
		start_at,
		end_at,
		created_at,
		updated_at,
		company_id		
		FROM job_card_number_dtl
		where company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00' 
		group by job_card_number_id desc limit 1");
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
	 * update job-card-no
	 * returns the error-message/status
	*/
	public function updateJobCardNo($companyId,$endAt)
	{
		$mytime = Carbon\Carbon::now();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();	
		$raw = DB::connection($databaseName)->statement("update
		job_card_number_dtl
		set end_at='".$endAt."',
		updated_at='".$mytime."' 
		where company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw!=1)
		{
			return $exceptionArray['204'];
		}
		else
		{
			return $exceptionArray['200'];
		}
	}
}
