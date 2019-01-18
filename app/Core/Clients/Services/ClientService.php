<?php
namespace ERP\Core\Clients\Services;

use ERP\Core\Clients\Persistables\ClientPersistable;
use ERP\Core\Clients\Entities\Client;
use ERP\Model\Clients\ClientModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\User\Entities\User;
use ERP\Core\Clients\Entities\EncodeData;
use ERP\Core\Clients\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ClientService
{
    /**
     * @var clientService
	 * $var clientModel
     */
    private $clientService;
    private $clientModel;
	
    /**
     * @param ClientService $clientService
     */
    public function initialize(ClientService $clientService)
    {		
		echo "init";
    }
	
    /**
     * @param ClientPersistable $persistable
     */
    public function create(ClientPersistable $persistable)
    {
		return "create method of ClientService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param ClientPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$clientArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$clientArray = func_get_arg(0);
		
		// get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		
		for($data=0;$data<count($clientArray);$data++)
		{
			$funcName[$data] = $clientArray[$data][0]->getName();
			$getData[$data] = $clientArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $clientArray[$data][0]->getkey();
		}
		// data pass to the model object for insert
		$clientModel = new ClientModel();
		$status = $clientModel->insertData($getData,$keyName);
		if(strcmp($status,$fileSizeArray['500'])==0)
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
     * get all the data  as per given id and call the model for database selection opertation
     * @param $clientId
     * @return status
     */
	public function getClientData($clientId)
	{
		$clientModel = new ClientModel();
		$status = $clientModel->getData($clientId);
		
		// get exception message
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
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllClientData($headerData,$processedData=null,$identifyFlag)
	{
		$clientModel = new ClientModel();
		$status = $clientModel->getAllData($headerData,$processedData,$identifyFlag);
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
     * get all the data and call the model for database update opertation
     * @return status/error message
     */
	public function update()
	{
		$clientArray = array();
		$getData = array();
		$funcName = array();
		$clientArray = func_get_arg(0);
		for($data=0;$data<count($clientArray);$data++)
		{
			$funcName[$data] = $clientArray[$data][0]->getName();
			$getData[$data] = $clientArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $clientArray[$data][0]->getkey();
		}
		$clientId = $clientArray[0][0]->getClientId();
		// data pass to the model object for update
		$clientModel = new ClientModel();
		$status = $clientModel->updateData($getData,$keyName,$clientId);
		return $status;
	}
}