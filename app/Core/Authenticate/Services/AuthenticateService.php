<?php
namespace ERP\Core\Authenticate\Services;

use ERP\Core\Authenticate\Persistables\AuthenticatePersistable;
// use ERP\Core\Authenticate\Entities\State;
use ERP\Model\Authenticate\AuthenticateModel;
use ERP\Core\Authenticate\Entities\EncodeData;
use ERP\Core\Authenticate\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class AuthenticateService
{
    /**
     * @var authenticateService
	 * $var authenticateModel
     */
    private $authenticateService;
    private $authenticateModel;
	
    /**
     * @param AuthenticateService $authenticate
     */
    public function initialize(AuthenticateService $authenticate)
    {		
		echo "init";
    }
	
    /**
     * @param AuthenticatePersistable $persistable
     */
    
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param AuthenticatePersistable $persistable
     * @return status
     */
	public function insert()
	{	
		$authenticateArray = func_get_arg(0);

		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$authenticateModel = new AuthenticateModel();
		if(is_array($authenticateArray))
		{
			$status = $authenticateModel->updateDate($authenticateArray['userId']);
			if(strcmp($status,$exceptionArray['200'])==0)
			{
				$result = $this->getData($authenticateArray['userId']);
				return $result;
			}
			return $status;
		}
		else
		{
			$userId = $authenticateArray->getUserId();
			$token = $authenticateArray->getToken();
			
			// data pass to the model object for insert
			$status = $authenticateModel->insertData($userId,$token);
			if(strcmp($status,$exceptionArray['200'])==0)
			{
				$result = $this->getData($userId);
				return $result;
			}
			return $status;
		}
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllData()
	{
		$authenticationModel = new AuthenticateModel();
		$status = $authenticationModel->getAllData();
		
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
     * get data as per given userId and call the model for database selection opertation
	 * @param userId
     * @return status
     */
	public function getData($userId)
	{
		$authenticationModel = new AuthenticateModel();
		$status = $authenticationModel->getData($userId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0 || strcmp($status,$exceptionArray['noAccess'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeData();
			$encodeAllData = $encoded->getEncodedData($status);
			return $encodeAllData;
		}
	}
}