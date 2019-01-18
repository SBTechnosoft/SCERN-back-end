<?php
namespace ERP\Core\Settings\Professions\Services;

use ERP\Core\Settings\Professions\Persistables\ProfessionPersistable;
use ERP\Model\Settings\Professions\ProfessionModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Settings\Professions\Entities\EncodeData;
use ERP\Core\Settings\Professions\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfessionService extends AbstractService
{
    /**
     * @var professionService
	 * $var professionModel
     */
    private $professionService;
    private $professionModel;
	
    /**
     * @param ProfessionService $professionService
     */
    public function initialize(ProfessionService $professionService)
    {		
		echo "init";
    }
	
    /**
     * @param ProfessionPersistable $persistable
     */
    public function create(ProfessionPersistable $persistable)
    {
		return "create method of ProfessionService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param ProfessionPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$professionArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$professionArray = func_get_arg(0);
		for($data=0;$data<count($professionArray);$data++)
		{
			$funcName[$data] = $professionArray[$data][0]->getName();
			$getData[$data] = $professionArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $professionArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$professionModel = new ProfessionModel();
		$status = $professionModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllProfessionData()
	{
		$professionModel = new ProfessionModel();
		$status = $professionModel->getAllData();
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
     * @param $professionId
     * @return status
     */
	public function getProfessionData($professionId)
	{
		$professionModel = new ProfessionModel();
		$status = $professionModel->getData($professionId);
		
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
		$professionArray = array();
		$getData = array();
		$funcName = array();
		$professionArray = func_get_arg(0);
		for($data=0;$data<count($professionArray);$data++)
		{
			$funcName[$data] = $professionArray[$data][0]->getName();
			$getData[$data] = $professionArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $professionArray[$data][0]->getkey();
		}
		$professionId = $professionArray[0][0]->getProfessionId();
		// data pass to the model object for update
		$professionModel = new ProfessionModel();
		$status = $professionModel->updateData($getData,$keyName,$professionId);
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
     * @param profession-id
     */
    public function delete($professionId)
    {      
		$professionModel = new ProfessionModel();
		$status = $professionModel->deleteData($professionId);
		return $status;
    }   
}