<?php
namespace ERP\Core\Users\Commissions\Services;

use ERP\Core\Users\Commissions\Persistables\CommissionPersistable;
use ERP\Model\Users\Commissions\CommissionModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\Users\Commissions\Entities\EncodeData;
use ERP\Core\Users\Commissions\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CommissionService extends AbstractService
{
	 /**
     * @var CommissionService
	 * $var commissionModel
     */
    private $commissionService;
    private $commissionModel;
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
    /**
     * @param CommissionService $commissionService
     */
    public function initialize(CommissionService $commissionService)
    {		
		echo "init";
    }
	
    /**
     * @param CommissionPersistable $persistable
     */
    public function create(CommissionPersistable $persistable)
    {
		return "create method of CommissionService";
		
    }
    /**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllCommissionData()
	{
		$commissionModel = new CommissionModel();
		$status = $commissionModel->getAllData();
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['204'])==0)
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
     * get the data from persistable object and call the model for database insertion opertation
     * @param CommissionPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$commissionArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$commissionArray = func_get_arg(0);
		for($data=0;$data<count($commissionArray);$data++)
		{
			$funcName[$data] = $commissionArray[$data][0]->getName();
			$getData[$data] = $commissionArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $commissionArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$commissionModel = new CommissionModel();
		$status = $commissionModel->insertData($getData,$keyName);
		return $status;
	}   
	/**
     * get the data from persistable object and call the model for database update opertation
     * @param Commission $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$commissionArray = array();
		$getData = array();
		$funcName = array();
		$commissionArray = func_get_arg(0);
		for($data=0;$data<count($commissionArray);$data++)
		{
			$funcName[$data] = $commissionArray[$data][0]->getName();
			$getData[$data] = $commissionArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $commissionArray[$data][0]->getkey();
		}
		$userId = $getData[1];
		// data pass to the model object for update
		$commissionModel = new CommissionModel();
		$status = $commissionModel->updateData($getData,$keyName,$userId);
		return $status;	
	}
	/**
     * get all the data  as per given id and call the model for database selection opertation
     * @param $userId
     * @return status
     */
	public function getCommissionData($userId)
	{
		$commissionModel = new CommissionModel();
		$status = $commissionModel->getData($userId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['404'])==0)
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
}