<?php
namespace ERP\Core\States\Services;

use ERP\Core\States\Persistables\StatePersistable;
use ERP\Core\States\Entities\State;
use ERP\Model\States\StateModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\States\Entities\EncodeData;
use ERP\Core\States\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class StateService extends AbstractService
{
    /**
     * @var stateService
	 * $var stateModel
     */
    private $stateService;
    private $stateModel;
	
    /**
     * @param StateService $stateService
     */
    public function initialize(StateService $stateService)
    {		
		echo "init";
    }
	
    /**
     * @param StatePersistable $persistable
     */
    
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param StatePersistable $persistable
     * @return status
     */
	public function insert()
	{
		$stateArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$stateArray = func_get_arg(0);
		for($data=0;$data<count($stateArray);$data++)
		{
			$funcName[$data] = $stateArray[$data][0]->getName();
			$getData[$data] = $stateArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $stateArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$stateModel = new StateModel();
		$status = $stateModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllStateData()
	{
		$stateModel = new StateModel();
		$status = $stateModel->getAllData();
		
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
     * get all the dataas per given id and call the model for database selection opertation
     * @param state_abb
     * @return status
     */
	public function getStateData($stateAbb)
	{
		$stateModel = new StateModel();
		$status = $stateModel->getData($stateAbb);
		
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
     * @param StatePersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$stateArray = array();
		$getData = array();
		$funcName = array();
		$stateArray = func_get_arg(0);
		
		for($data=0;$data<count($stateArray);$data++)
		{
			$funcName[$data] = $stateArray[$data][0]->getName();
			$getData[$data] = $stateArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $stateArray[$data][0]->getkey();
		}
		$stateAbb = $stateArray[0][0]->getStateAbb();
		//data pass to the model object for update
		$stateModel = new StateModel();
		$status = $stateModel->updateData($getData,$keyName,$stateAbb);
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
     * @param $StatePersistable $persistable 
     */
    public function delete(StatePersistable $persistable)
    {      
		$stateAbb = $persistable->getStateAbb();
        $stateModel = new StateModel();
		$status = $stateModel->deleteData($stateAbb);
		return $status;
    }   
}