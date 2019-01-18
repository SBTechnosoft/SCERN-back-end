<?php
namespace ERP\Core\Users\Services;

use ERP\Core\Users\Persistables\UserPersistable;
use ERP\Model\Users\UserModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\Users\Entities\User;
use ERP\Core\Users\Entities\EncodeData;
use ERP\Core\Users\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
use ERP\Http\Requests;
use Illuminate\Http\Request;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserService extends AbstractService
{
    /**
     * @var userService
	 * $var userModel
     */
    private $userService;
    private $userModel;
	
    /**
     * @param UserService $userService
     */
    public function initialize(UserService $userService)
    {		
		echo "init";
    }
	
    /**
     * @param UserPersistable $persistable
     */
    
	 /**
     * get the data from persistable object and call the model for database insertion   opertation
     * @param UserPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$userArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$userArray = func_get_arg(0);
		for($data=0;$data<count($userArray);$data++)
		{
			$funcName[$data] = $userArray[$data][0]->getName();
			$getData[$data] = $userArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $userArray[$data][0]->getkey();
		}
		// data pass to the model object for insert
		$userModel = new UserModel();
		$status = $userModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllUserData(Request $request)
	{
		$userModel = new UserModel();
		$status = $userModel->getAllData($request);
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
     * get all the dataas per given id and call the model for database selection opertation
     * @param state_abb
     * @return status
     */
	public function getUserData($userId)
	{
		$userModel = new UserModel();
		$status = $userModel->getData($userId);
		
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
     * @param UserPersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$userArray = array();
		$getData = array();
		$funcName = array();
		$userArray = func_get_arg(0);
		
		for($data=0;$data<count($userArray);$data++)
		{
			$funcName[$data] = $userArray[$data][0]->getName();
			$getData[$data] = $userArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $userArray[$data][0]->getkey();
		}
		$userId = $userArray[0][0]->getUserId();
		//data pass to the model object for update
		$userModel = new UserModel();
		$status = $userModel->updateData($getData,$keyName,$userId);
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
    public function delete(UserPersistable $persistable)
    {      
		$userId = $persistable->getUserId();
		$userModel = new UserModel();
		$status = $userModel->deleteData($userId);
		return $status;
    }   
}