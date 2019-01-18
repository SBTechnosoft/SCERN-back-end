<?php
namespace ERP\Core\Cities\Services;

use ERP\Core\Cities\Persistables\CityPersistable;
use ERP\Core\Cities\Entities\City;
use ERP\Model\Cities\CityModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Cities\Entities\EncodeData;
use ERP\Core\Cities\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityService extends AbstractService
{
    /**
     * @var cityService
	 * $var cityModel
     */
    private $cityService;
    private $cityModel;
	
    /**
     * @param CityService $cityService
     */
    public function initialize(CityService $cityService)
    {		
		echo "init";
    }
	
    /**
     * @param CityPersistable $persistable
     */
    public function create(CityPersistable $persistable)
    {
		return "create method of CityService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param array
     * @return status
     */
	public function insert()
	{
		$cityArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$cityArray = func_get_arg(0);
		for($data=0;$data<count($cityArray);$data++)
		{
			$funcName[$data] = $cityArray[$data][0]->getName();
			$getData[$data] = $cityArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $cityArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$cityModel = new CityModel();
		$status = $cityModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllCityData()
	{
		$cityModel = new CityModel();
		$status = $cityModel->getAllData();
		
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
     * get all the data as per given state_abb and call the model for database selection opertation
     * @param state_abb
     * @return status
     */
	public function getAllData($stateAbb)
	{
		$cityModel = new CityModel();
		$status = $cityModel->getAllCityData($stateAbb);
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['404'])==0)
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
     * get all the data as per city_id and call the model for database selection opertation
     * @param city_id
     * @return status
     */
	public function getCityData($cityId)
	{
		$cityModel = new CityModel();
		$status = $cityModel->getData($cityId);
		
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
	
    /**
     * get the data from persistable object and call the model for database update opertation
     * @param CityPersistable $persistable
     * @param updateOptions $options [optional]
     * @return status
     */
    public function update()
    {
		$cityArray = array();
		$getData = array();
		$funcName = array();
		$cityArray = func_get_arg(0);
		for($data=0;$data<count($cityArray);$data++)
		{
			$funcName[$data] = $cityArray[$data][0]->getName();
			$getData[$data] = $cityArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $cityArray[$data][0]->getkey();
		}
		$cityId = $cityArray[0][0]->getCityId();
		//data pass to the model object for update
		$cityModel = new CityModel();
		$status = $cityModel->updateData($getData,$keyName,$cityId);
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
     * delete
     * @param CityPersistable $persistable
     * @return status
     */
    public function delete(CityPersistable $persistable)
    {      
		$cityId = $persistable->getId();
        $cityModel = new CityModel();
		$status = $cityModel->deleteData($cityId);
		return $status;
    }   
}