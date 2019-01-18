<?php
namespace ERP\Core\Banks\Services;

use ERP\Core\Banks\Persistables\BankPersistable;
use ERP\Core\Banks\Entities\Bank;
use ERP\Model\Banks\BankModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Banks\Entities\EncodeData;
use ERP\Core\Banks\Entities\EncodeAllData;
use ERP\Core\Banks\Entities\EncodeAllBranchData;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BankService extends AbstractService
{
    /**
     * @var bankService
	 * $var bankModel
     */
    private $bankService;
    private $bankModel;
	
    /**
     * @param BankService $bankService
     */
    public function initialize(BankService $bankService)
    {		
		echo "init";
    }
	
    /**
     * @param BranchPersistable $persistable
     */
    public function create(BankPersistable $persistable)
    {
		return "create method of BankService";
		
    }
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllBankData()
	{
		$bankModel = new BankModel();
		$status = $bankModel->getAllData();
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
     * get all the data  as per given id and call the model for database selection opertation
     * @param $bankId
     * @return status
     */
	public function getBankData($bankId)
	{
		$bankModel = new BankModel();
		$status = $bankModel->getData($bankId);
		
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
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllBranchData()
	{
		$bankModel = new BankModel();
		$status = $bankModel->getAllBranchData();
		// get exception message
		$exception = new ExceptionMessage();
		$exceltionArray = $exception->messageArrays();
		if(strcmp($status,$exceltionArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllBranchData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data  as per given id and call the model for database selection opertation
     * @param $bankId
     * @return status
     */
	public function getBranchData($bankId)
	{
		$bankModel = new BankModel();
		$status = $bankModel->getBranchData($bankId);
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllBranchData();
			$encodeData = $encoded->getEncodedAllData($status);
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