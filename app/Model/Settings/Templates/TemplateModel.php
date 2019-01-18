<?php
namespace ERP\Model\Settings\Templates;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TemplateModel extends Model
{
	protected $table = 'template_mst';
	
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
		
		$getTemplateData = array();
		$getTemplateKey = array();
		$getTemplateData = func_get_arg(0);
		$getTemplateKey = func_get_arg(1);
		$templateData="";
		$keyName = "";
		for($data=0;$data<count($getTemplateData);$data++)
		{
			if($data == (count($getTemplateData)-1))
			{
				$templateData = $templateData."'".$getTemplateData[$data]."'";
				$keyName =$keyName.$getTemplateKey[$data];
			}
			else
			{
				$templateData = $templateData."'".$getTemplateData[$data]."',";
				$keyName =$keyName.$getTemplateKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into template_mst(".$keyName.",created_at) 
		values(".$templateData.",'".$mytime."')");
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
	 * @param  template-data,key of template-data,template-id
	 * returns the status
	*/
	public function updateData($templateData,$key,$templateId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		date_default_timezone_set("Asia/Calcutta");
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($templateData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$templateData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update template_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where template_id = '".$templateId."' and 
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
		template_id,
		template_name,
		template_body,
		template_type,
		updated_at,
		created_at,
		company_id
		from template_mst 
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
	 * get data as per given Template Id
	 * @param $templateId
	 * returns the status
	*/
	public function getData($templateId)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		template_id,
		template_name,
		template_body,
		template_type,
		updated_at,
		created_at,
		company_id
		from template_mst 
		where template_id ='".$templateId."' and 
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
	 * get data as per given Company Id
	 * @param $companyId
	 * returns the status
	*/
	public function getAllTemplateData($companyId,$templateType)
	{		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		if(strcmp($templateType,"all")==0)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("select 
			template_id,
			template_body,
			template_name,
			template_type,
			created_at,
			updated_at,
			company_id
			from template_mst 
			where company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("select 
			template_id,
			template_body,
			template_name,
			template_type,
			created_at,
			updated_at,
			company_id
			from template_mst 
			where template_type ='".$templateType."' and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			
		}
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
}
