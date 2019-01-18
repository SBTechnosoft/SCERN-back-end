<?php
namespace ERP\Core\Crm\JobFormNumber\Services;

use ERP\Core\Crm\JobFormNumber\Persistables\JobFormNumberPersistable;
use ERP\Model\Crm\JobFormNumber\JobFormNumberModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\Crm\JobFormNumber\Entities\EncodeData;
use ERP\Core\Crm\JobFormNumber\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumberService extends AbstractService
{
    /**
     * @var jobFormNumberService
	 * $var jobFormNumberModel
     */
    private $jobFormNumberService;
    private $jobFormNumberModel;
	
    /**
     * @param JobFormNumberService $jobFormNumberService
     */
    public function initialize(JobFormNumberService $jobFormNumberService)
    {		
		echo "init";
    }
	
    /**
     * @param JobFormNumberPersistable $persistable
     */
    public function create(JobFormNumberPersistable $persistable)
    {
		return "create method of JobFormNumberService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param JobFormNumberPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$jobFormNumberArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$jobFormNumberArray = func_get_arg(0);
		for($data=0;$data<count($jobFormNumberArray);$data++)
		{
			$funcName[$data] = $jobFormNumberArray[$data][0]->getName();
			$getData[$data] = $jobFormNumberArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $jobFormNumberArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$jobFormNumberModel = new JobFormNumberModel();
		$status = $jobFormNumberModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllJobFormNumberData()
	{
		$jobFormNumberModel = new JobFormNumberModel();
		$status = $jobFormNumberModel->getAllData();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get the latest data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getLatestJobFormNumberData($companyId)
	{
		$jobFormNumberModel = new JobFormNumberModel();
		$status = $jobFormNumberModel->getLatestJobFormNumberData($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($status);
			return $encodeData;
		}
	}
	
    /**
     * get and invoke method is of Container Interface method
     * @param int $id,$name
     */
    public function get($id,$name)
    {
		echo "get";		
    }   
	public function invoke(callable $method)
	{
		echo "invoke";
	}
}