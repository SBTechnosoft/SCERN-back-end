<?php
namespace ERP\Core\Settings\InvoiceNumbers\Services;

use ERP\Core\Settings\InvoiceNumbers\Persistables\InvoicePersistable;
use ERP\Core\Settings\InvoiceNumbers\Entities\Invoice;
use ERP\Model\Settings\InvoiceNumbers\InvoiceModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Settings\InvoiceNumbers\Entities\EncodeData;
use ERP\Core\Settings\InvoiceNumbers\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InvoiceService extends AbstractService
{
    /**
     * @var invoiceService
	 * $var invoiceModel
     */
    private $invoiceService;
    private $invoiceModel;
	
    /**
     * @param InvoiceService $invoiceService
     */
    public function initialize(InvoiceService $invoiceService)
    {		
		echo "init";
    }
	
    /**
     * @param InvoicePersistable $persistable
     */
    public function create(InvoicePersistable $persistable)
    {
		return "create method of InvoiceService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param InvoicePersistable $persistable
     * @return status
     */
	public function insert()
	{
		$invoiceArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$invoiceArray = func_get_arg(0);
		for($data=0;$data<count($invoiceArray);$data++)
		{
			$funcName[$data] = $invoiceArray[$data][0]->getName();
			$getData[$data] = $invoiceArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $invoiceArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$invoiceModel = new InvoiceModel();
		$status = $invoiceModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllInvoiceData()
	{
		$invoiceModel = new InvoiceModel();
		$status = $invoiceModel->getAllData();
		
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
     * @param $invoiceId
     * @return status
     */
	public function getInvoiceData($invoiceId)
	{
		$invoiceModel = new InvoiceModel();
		$status = $invoiceModel->getData($invoiceId);
		
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
		$invoiceModel = new InvoiceModel();
		$status = $invoiceModel->getAllInvoiceData($companyId);
		
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
	public function getLatestInvoiceData($companyId)
	{
		$invoiceModel = new InvoiceModel();
		$status = $invoiceModel->getLatestInvoiceData($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['204'])==0)
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
	
	/**
     * get the data from persistable object and call the model for database update opertation
     * @param InvoicePersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$invoiceArray = array();
		$getData = array();
		$funcName = array();
		$invoiceArray = func_get_arg(0);
		for($data=0;$data<count($invoiceArray);$data++)
		{
			$funcName[$data] = $invoiceArray[$data][0]->getName();
			$getData[$data] = $invoiceArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $invoiceArray[$data][0]->getkey();
		}
		$invoiceId = $invoiceArray[0][0]->getInvoiceId();
		// data pass to the model object for update
		$invoiceModel = new InvoiceModel();
		$status = $invoiceModel->updateData($getData,$keyName,$invoiceId);
		return $status;	
	}
}