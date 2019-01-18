<?php
namespace ERP\Core\Settings\MeasurementUnits\Services;

use ERP\Core\Settings\MeasurementUnits\Persistables\MeasurementPersistable;
use ERP\Model\Settings\MeasurementUnits\MeasurementModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Settings\MeasurementUnits\Entities\EncodeData;
use ERP\Core\Settings\MeasurementUnits\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class MeasurementService extends AbstractService
{
    /**
     * @var measurementService
	 * $var measurementModel
     */
    private $measurementService;
    private $measurementModel;
	
    /**
     * @param MeasurementService $measurementService
     */
    public function initialize(MeasurementService $measurementService)
    {		
		echo "init";
    }
	
    /**
     * @param MeasurementPersistable $persistable
     */
    public function create(MeasurementPersistable $persistable)
    {
		return "create method of MeasurementService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param MeasurementPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$measurementArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$measurementArray = func_get_arg(0);
		for($data=0;$data<count($measurementArray);$data++)
		{
			$funcName[$data] = $measurementArray[$data][0]->getName();
			$getData[$data] = $measurementArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $measurementArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$measurementModel = new MeasurementModel();
		$status = $measurementModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllMeasurementData()
	{
		$measurementModel = new MeasurementModel();
		$status = $measurementModel->getAllData();
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
     * get all the data  as per given id and call the model for database selection opertation
     * @param $measurementId
     * @return status
     */
	public function getMeasurementData($measurementId)
	{
		$measurementModel = new MeasurementModel();
		$status = $measurementModel->getData($measurementId);
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0)
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
     * get the data from persistable object and call the model for database update opertation
     * @param SettingPersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$measurementArray = array();
		$getData = array();
		$funcName = array();
		$measurementArray = func_get_arg(0);
		for($data=0;$data<count($measurementArray);$data++)
		{
			$funcName[$data] = $measurementArray[$data][0]->getName();
			$getData[$data] = $measurementArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $measurementArray[$data][0]->getkey();
		}
		$measurementId = $measurementArray[0][0]->getMeasurementUnitId();
		// data pass to the model object for update
		$measurementModel = new MeasurementModel();
		$status = $measurementModel->updateData($getData,$keyName,$measurementId);
		return $status;	
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
	
    /**
     * @param delete
     * @param measurement-id
    */
    public function delete($measurementId)
    {
		$measurementModel = new MeasurementModel();
		$status = $measurementModel->deleteData($measurementId);
		return $status;
    }   
}