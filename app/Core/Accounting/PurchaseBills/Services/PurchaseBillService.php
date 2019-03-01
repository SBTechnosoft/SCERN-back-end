<?php
namespace ERP\Core\Accounting\PurchaseBills\Services;

// use ERP\Core\Accounting\PurchaseBills\Persistables\BillPersistable;
// use ERP\Core\Accounting\PurchaseBills\Entities\Bill;
use ERP\Model\Accounting\PurchaseBills\PurchaseBillModel;
use ERP\Core\Shared\Options\UpdateOptions;
// use ERP\Core\User\Entities\User;
// use ERP\Core\Accounting\PurchaseBills\Entities\EncodeData;
use ERP\Core\Accounting\PurchaseBills\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Request;
// use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PurchaseBillService
{
    /**
     * @var purchaseBillService
	 * $var purchaseBillModel
     */
    private $purchaseBillService;
    private $purchaseBillModel;
	
    /**
     * @param LedgerService $ledgerService
     */
    public function initialize(LedgerService $ledgerService)
    {		
		echo "init";
    }
	
    /**
     * @param LedgerPersistable $persistable
     */
    public function create(LedgerPersistable $persistable)
    {
		return "create method of LedgerService";
	}
	
	/**
	* get the data from persistable object and call the model for database insertion opertation
	* @param PurchaseBillPersistable $persistable
	* @return status/error message
	*/
	public function insert()
	{
		$purchaseBillArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$purchaseBillArray = func_get_arg(0);
		$requestInput = func_get_arg(1);

		if(is_array($purchaseBillArray))
		{
			$dataCount = count($purchaseBillArray);
			$documentDataArray = array();
			for($arrayData=0;$arrayData<$dataCount;$arrayData++)
			{
				if(is_object($purchaseBillArray[$arrayData][0]))
				{
					$funcName[$arrayData] = $purchaseBillArray[$arrayData][0]->getName();
					$getData[$arrayData] = $purchaseBillArray[$arrayData][0]->$funcName[$arrayData]();
					$keyName[$arrayData] = $purchaseBillArray[$arrayData][0]->getkey();
				}
				else if(is_array($purchaseBillArray[$arrayData][0]))
				{
					$documentCount = count($purchaseBillArray[$arrayData]);
					for($documentArray=0;$documentArray<$documentCount;$documentArray++)
					{
						$documentDataArray[$documentArray]['document_name'] = $purchaseBillArray[$arrayData][$documentArray][0];
						$documentDataArray[$documentArray]['document_size'] = $purchaseBillArray[$arrayData][$documentArray][1];
						$documentDataArray[$documentArray]['document_format'] = $purchaseBillArray[$arrayData][$documentArray][2];
						$documentDataArray[$documentArray]['document_path'] = $purchaseBillArray[$arrayData][$documentArray][3];
					}
				}
			}
			// data pass to the model object for insert
			$purchaseBillModel = new PurchaseBillModel();
			$status = $purchaseBillModel->insertData($getData,$keyName,$documentDataArray,$requestInput);
			// get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($status,$exceptionArray['500'])==0)
			{
				return $status;
			}
			else
			{
				return $exceptionArray['200'];
				// $encoded = new EncodeData();
				// $encodeData = $encoded->getEncodedData($status);
				// return $encodeData;
			}
		}
	}
	
	/**
	* update the data from persistable object and call the model for database update opertation
	* @param PurchaseBillPersistable $persistable
	* @return status/error message
	*/
	public function update()
	{
		$purchaseBillArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$purchaseBillArray = func_get_arg(0);
		$purchaseBillId = func_get_arg(1);
		if(is_array($purchaseBillArray))
		{
			$dataCount = count($purchaseBillArray);
			$documentDataArray = array();
			for($arrayData=0;$arrayData<$dataCount;$arrayData++)
			{
				if(is_object($purchaseBillArray[$arrayData][0]))
				{
					$funcName[$arrayData] = $purchaseBillArray[$arrayData][0]->getName();
					$getData[$arrayData] = $purchaseBillArray[$arrayData][0]->$funcName[$arrayData]();
					$keyName[$arrayData] = $purchaseBillArray[$arrayData][0]->getkey();
				}
				else if(is_array($purchaseBillArray[$arrayData][0]))
				{
					$documentCount = count($purchaseBillArray[$arrayData]);
					for($documentArray=0;$documentArray<$documentCount;$documentArray++)
					{
						$documentDataArray[$documentArray]['document_name'] = $purchaseBillArray[$arrayData][$documentArray][0];
						$documentDataArray[$documentArray]['document_size'] = $purchaseBillArray[$arrayData][$documentArray][1];
						$documentDataArray[$documentArray]['document_format'] = $purchaseBillArray[$arrayData][$documentArray][2];
						$documentDataArray[$documentArray]['document_path'] = $purchaseBillArray[$arrayData][$documentArray][3];
					}
				}
			}
			// data pass to the model object for udpate
			$purchaseBillModel = new PurchaseBillModel();
			$status = $purchaseBillModel->udpateData($getData,$keyName,$documentDataArray,$purchaseBillId);
			// get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($status,$exceptionArray['500'])==0)
			{
				return $status;
			}
			else
			{
				return $exceptionArray['200'];
				// $encoded = new EncodeData();
				// $encodeData = $encoded->getEncodedData($status);
				// return $encodeData;
			}
		}
	}
	
	 /**
     * get the data from persistable object and call the model for database get opertation
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function getData()
	{
		$data = func_get_arg(0);
		$companyId = func_get_arg(1);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//data pass to the model object for getData
		$purchaseBillModel = new PurchaseBillModel();
		$billResult = $purchaseBillModel->getSpecifiedData($companyId,$data);
		
		if(strcmp($billResult,$exceptionArray['404'])==0)
		{
			return $billResult;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedAllData($billResult);
			return $encodingResult;
		}
	}
	
   	/**
     * call the model for database get opertation
     * @param headerData
     * @return purchase-bill-data/error message
     */
	public function getPurchaseBillData($headerData)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// data pass to the model object for getData
		$purchaseBillModel = new PurchaseBillModel();
		$purchaseBillResult = $purchaseBillModel->getPurchaseBillData($headerData);
		
		if(strcmp($purchaseBillResult,$exceptionArray['204'])==0)
		{
			return $purchaseBillResult;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedAllData($purchaseBillResult);
			return $encodingResult;
		}
	}
	/**
     * call the model for database get opertation
     * @param headerData
     * @return sale-data/error message
     */
	public function getPurchaseBillByJfId($companyId,$jfId)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		// data pass to the model object for getData
		$purchaseBillModel = new PurchaseBillModel();
		$status = $purchaseBillModel->getPurchaseBillByJfId($companyId,$jfId);
		
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedAllData($status);
			return $encodingResult;
		}
	}
}