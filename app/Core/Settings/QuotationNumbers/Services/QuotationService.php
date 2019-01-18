<?php
namespace ERP\Core\Settings\QuotationNumbers\Services;

use ERP\Core\Settings\QuotationNumbers\Persistables\QuotationPersistable;
use ERP\Core\Settings\QuotationNumbers\Entities\Quotation;
use ERP\Model\Settings\QuotationNumbers\QuotationModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Settings\QuotationNumbers\Entities\EncodeData;
use ERP\Core\Settings\QuotationNumbers\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationService extends AbstractService
{
    /**
     * @var quotationService
	 * $var invoiceModel
     */
    private $quotationService;
    private $quotationModel;
	
    /**
     * @param QuotationService $quotationService
     */
    public function initialize(QuotationService $quotationService)
    {		
		echo "init";
    }
	
    /**
     * @param InvoicePersistable $persistable
     */
    public function create(QuotationPersistable $persistable)
    {
		return "create method of QuotationService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param InvoicePersistable $persistable
     * @return status
     */
	public function insert()
	{
		$quotationArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$quotationArray = func_get_arg(0);
		for($data=0;$data<count($quotationArray);$data++)
		{
			$funcName[$data] = $quotationArray[$data][0]->getName();
			$getData[$data] = $quotationArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $quotationArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$quotationModel = new QuotationModel();
		$status = $quotationModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllQuotationData()
	{
		$quotationModel = new QuotationModel();
		$status = $quotationModel->getAllData();
		
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
     * @param $quotationId
     * @return status
     */
	public function getQuotationData($quotationId)
	{
		$quotationModel = new QuotationModel();
		$status = $quotationModel->getData($quotationId);
		
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
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getAllData($companyId)
	{
		$quotationModel = new QuotationModel();
		$status = $quotationModel->getAllQuotationData($companyId);
		
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
     * get the latest data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getLatestQuotationData($companyId)
	{
		$quotationModel = new QuotationModel();
		$status = $quotationModel->getLatestQuotationData($companyId);
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
     * get the data from persistable object and call the model for database update opertation
     * @param QuotationPersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$quotationArray = array();
		$getData = array();
		$funcName = array();
		$quotationArray = func_get_arg(0);
		for($data=0;$data<count($quotationArray);$data++)
		{
			$funcName[$data] = $quotationArray[$data][0]->getName();
			$getData[$data] = $quotationArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $quotationArray[$data][0]->getkey();
		}
		$quotationId = $quotationArray[0][0]->getQuotationId();
		// data pass to the model object for update
		$quotationModel = new QuotationModel();
		$status = $quotationModel->updateData($getData,$keyName,$quotationId);
		return $status;	
	}
}