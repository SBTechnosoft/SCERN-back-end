<?php
namespace ERP\Core\Crm\JobForm\Services;

use ERP\Core\Crm\JobForm\Persistables\JobFormPersistable;
use ERP\Model\Crm\JobForm\JobFormModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\Crm\JobForm\Entities\EncodeData;
use ERP\Core\Crm\JobForm\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormService extends AbstractService
{
    /**
     * @var jobFormService
	 * $var jobFormModel
     */
    private $jobFormService;
    private $jobFormModel;
	
    /**
     * @param JobFormService $jobFormService
     */
    public function initialize(JobFormService $jobFormService)
    {		
		echo "init";
    }
	
    /**
     * @param JobFormPersistable $persistable
     */
    public function create(JobFormPersistable $persistable)
    {
		return "create method of JobFormService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param JobFormPersistable $persistable
     * @return status
     */
	public function insert()
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$jobFormArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$jobFormArray = func_get_arg(0);
		$headerArray = func_get_arg(1);
		
		$arrayData = array();
		$arrayData['clientName'] = $jobFormArray[0]->getClientName();
		$arrayData['address'] = $jobFormArray[0]->getAddress();
		$arrayData['contactNo'] = $jobFormArray[0]->getContactNo();
		$arrayData['emailId']= $jobFormArray[0]->getEmailId();
		$arrayData['jobCardNo'] = $jobFormArray[0]->getJobCardNo();
		$arrayData['labourCharge'] = $jobFormArray[0]->getLabourCharge();
		$arrayData['serviceType'] = $jobFormArray[0]->getServiceType();
		$arrayData['entryDate'] = $jobFormArray[0]->getEntryDate();
		$arrayData['deliveryDate'] = $jobFormArray[0]->getDeliveryDate();
		$arrayData['advance'] = $jobFormArray[0]->getAdvance();
		$arrayData['total']= $jobFormArray[0]->getTotal();
		$arrayData['tax']= $jobFormArray[0]->getTax();
		$arrayData['paymentMode'] = $jobFormArray[0]->getPaymentMode();
		$arrayData['stateAbb'] = $jobFormArray[0]->getStateAbb();
		$arrayData['cityId']= $jobFormArray[0]->getCityId();
		$arrayData['companyId']= $jobFormArray[0]->getCompanyId();
		$arrayData['bankName']= $jobFormArray[0]->getBankName();
		$arrayData['chequeNo']= $jobFormArray[0]->getChequeNo();
		$arrayData['clientId']= $jobFormArray[0]->getClientId();
			
		$inventoryArray = array();
		for($data=0;$data<count($jobFormArray);$data++)
		{
			$inventoryArray[$data] = array();
			$inventoryArray[$data]['productId'] = $jobFormArray[$data]->getProductId();
			$inventoryArray[$data]['productName'] = $jobFormArray[$data]->getProductName();
			$inventoryArray[$data]['productInformation'] = $jobFormArray[$data]->getProductInformation();
			$inventoryArray[$data]['qty'] = $jobFormArray[$data]->getQty();
			$inventoryArray[$data]['price'] = $jobFormArray[$data]->getPrice();
			$inventoryArray[$data]['discountType'] = $jobFormArray[$data]->getDiscountType();
			$inventoryArray[$data]['discount'] = $jobFormArray[$data]->getDiscount();
		}
		$jobFormModel = new JobFormModel();
		if(array_key_exists('operation',$headerArray))
		{
			if(strcmp($headerArray['operation'][0],'generateBill')==0)
			{
				//data pass to the model object for insert
				$status = $jobFormModel->insertBillJobData($getData,$keyName);
				return $status;
			}
			else
			{
				return $exceptionArray['content'];
			}
		}
		else
		{
			//data pass to the model object for insert
			$status = $jobFormModel->insertData($arrayData,$inventoryArray);
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
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllData()
	{
		$jobFormModel = new JobFormModel();
		$status = $jobFormModel->getAllData();
		
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
     * get specific data as per given job-card-id
     * @param int $id,$name
     */
	public function getData($jobCardId)
	{
		$jobFormModel = new JobFormModel();
		$status = $jobFormModel->getData($jobCardId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
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