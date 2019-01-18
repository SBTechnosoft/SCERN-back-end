<?php
namespace ERP\Core\Companies\Services;

use ERP\Core\Companies\Persistables\CompanyPersistable;
use ERP\Core\Companies\Entities\Company;
use ERP\Model\Companies\CompanyModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Companies\Entities\EncodeData;
use ERP\Core\Companies\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Accounting\Ledgers\LedgerModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyService extends AbstractService 
{
    /**
     * @var companyService
	 * $var companyModel
     */
    private $companyService;
    private $companyModel;
	
    /**
     * @param CompanyService $companyService
     */
    public function initialize(CompanyService $companyService)
    {		
		echo "init";
    }
	
    /**
     * @param CompanyPersistable $persistable
     */
    public function create(CompanyPersistable $persistable)
    {
		return "create method of CompanyService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param CompanyPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$companyArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$companyArray = func_get_arg(0);
		$documentFlag=0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//check document is available
		if(is_array($companyArray[count($companyArray)-1][0]))
		{
			$documentCount = count($companyArray[count($companyArray)-1]);
			//get document data
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				$document[$documentArray] = array();
				$document[$documentArray][0] = $companyArray[count($companyArray)-1][$documentArray][0];
				$document[$documentArray][1] = $companyArray[count($companyArray)-1][$documentArray][1];
				$document[$documentArray][2] = $companyArray[count($companyArray)-1][$documentArray][2];
				$document[$documentArray][3] = $companyArray[count($companyArray)-1][$documentArray][3];
			}
			$documentFlag=1;
		}
		for($data=0;$data<count($companyArray);$data++)
		{
			if($documentFlag==1 && $data==(count($companyArray)-1))
			{
				break;
			}
			else
			{
				$funcName[$data] = $companyArray[$data][0]->getName();
				$getData[$data] = $companyArray[$data][0]->$funcName[$data]();
				$keyName[$data] = $companyArray[$data][0]->getkey();
			}
		}
		$ledgerModel = new LedgerModel();
		if($documentFlag==1)
		{
			// data pass to the model object for insert
			$companyModel = new CompanyModel();
			$status = $companyModel->insertAllData($getData,$keyName,$document);
			if(is_array($status))
			{
				$ledgerResult = $ledgerModel->insertGeneralLedger($status);
				return $ledgerResult;
			}
			else
			{
				return $status;
			}
		}
		else 
		{
			//data pass to the model object for insert
			$companyModel = new CompanyModel();
			$status = $companyModel->insertData($getData,$keyName);
			if(is_array($status))
			{
				$ledgerResult = $ledgerModel->insertGeneralLedger($status);
				return $ledgerResult;
			}
			else
			{
				return $status;
			}
		}
	}
	
	/**
     * get all the data call the model for database selection opertation
     * @return status
     */
	public function getAllCompanyData()
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$companyModel = new CompanyModel();
		$status = $companyModel->getAllData();
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
     * get all the data as per given id and call the model for database selection opertation
     * @param company_id
     * @return status
     */
	public function getCompanyData($companyId)
	{
		$companyModel = new CompanyModel();
		$status = $companyModel->getData($companyId);
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $status;
		}
		else
		{
			$decodedJsonDoc = json_decode($status,true);
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($status);
			return $encodeData;
		}
	}
	
    /**
     * get the data from persistable object and call the model for database update opertation
     * @param CompanyPersistable $persistable
     * @param updateOptions $options [optional]
     * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$companyArray = array();
		$getData = array();
		$funcName = array();
		$documentFlag=0;
		$dataFlag=0;
		$companyArray = func_get_arg(0);
		$companyId = func_get_arg(1);
		
		if(is_array($companyArray[count($companyArray)-1][0]))
		{
			$documentCount = count($companyArray[count($companyArray)-1]);
			//get document data
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				$document[$documentArray] = array();
				$document[$documentArray][0] = $companyArray[count($companyArray)-1][$documentArray][0];
				$document[$documentArray][1] = $companyArray[count($companyArray)-1][$documentArray][1];
				$document[$documentArray][2] = $companyArray[count($companyArray)-1][$documentArray][2];
				$document[$documentArray][3] = $companyArray[count($companyArray)-1][$documentArray][3];
			}
			$documentFlag=1;
		}
		for($data=0;$data<count($companyArray);$data++)
		{
			if($documentFlag==1 && $data==(count($companyArray)-1))
			{
				break;
			}
			else
			{
				$dataFlag=1;
				$funcName[$data] = $companyArray[$data][0]->getName();
				$getData[$data] = $companyArray[$data][0]->$funcName[$data]();
				$keyName[$data] = $companyArray[$data][0]->getkey();
			}
		}
		//data pass to the model object for update
		$companyModel = new CompanyModel();
		if($documentFlag==1 && $dataFlag==1)
		{
			$status = $companyModel->updateData($getData,$keyName,$companyId,$document);
			return $status;
		}
		else
		{
			if($documentFlag==1)
			{
				$status = $companyModel->updateDocumentData($companyId,$document);
				return $status;
			}
			else
			{
				$status = $companyModel->updateCompanyData($getData,$keyName,$companyId);
				return $status;
			}
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
    /**
     * delete
     * @param CompanyPersistable $persistable
     */
    public function delete(CompanyPersistable $persistable)
    {      
		$companyId = $persistable->getId();
        $companyModel = new CompanyModel();
		$status = $companyModel->deleteData($companyId);
		return $status;
    }   
}