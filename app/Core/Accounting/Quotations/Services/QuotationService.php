<?php
namespace ERP\Core\Accounting\Quotations\Services;

// use ERP\Core\Accounting\Quotations\Persistables\BillPersistable;
// use ERP\Core\Accounting\Quotations\Entities\Quotation;
use ERP\Model\Accounting\Quotations\QuotationModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\Quotations\Entities\EncodeData;
use ERP\Core\Accounting\Quotations\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Request;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationService
{
    /**
     * @var quotationService
	 * $var quotationModel
     */
    private $quotationService;
    private $quotationModel;
	
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
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function insert()
	{
		$quotationArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$quotationArray = func_get_arg(0);
		$headerData = func_get_arg(1);
		$documentArray=array();
		//only data insertion
		if(is_object($quotationArray))
		{
			$productArray = $quotationArray->getProductArray();
			$quotationNumber = $quotationArray->getQuotationNumber();
			$total = $quotationArray->getTotal();
			$totalDiscounttype = $quotationArray->getTotalDiscounttype();
			$totalDiscount = $quotationArray->getTotalDiscount();
			$totalCgstPercentage = $quotationArray->getTotalCgstPercentage();
			$totalSgstPercentage = $quotationArray->getTotalSgstPercentage();
			$totalIgstPercentage = $quotationArray->getTotalIgstPercentage();
			$extraCharge = $quotationArray->getExtraCharge();
			$tax = $quotationArray->getTax();
			$grandTotal = $quotationArray->getGrandTotal();
			$remark = $quotationArray->getRemark();
			$entryDate = $quotationArray->getEntryDate();
			$companyId = $quotationArray->getCompanyId();
			$branchId = $quotationArray->getBranchId() ? $quotationArray->getBranchId() : 0;
			$ClientId = $quotationArray->getClientId();
			$jfId= $quotationArray->getJfId();
			$poNumber= $quotationArray->getPoNumber();
			$paymentMode= $quotationArray->getPaymentMode();
			$invoiceNumber= $quotationArray->getInvoiceNumber();
			$bankName= $quotationArray->getBankName();
			$checkNumber= $quotationArray->getCheckNumber();
		}
		else
		{
			$dataCount = count($quotationArray);
			//data with document insertion
			for($dataArray=0;$dataArray<$dataCount;$dataArray++)
			{
				if(is_array($quotationArray[$dataArray]))
				{
					//document
					$documentArray[$dataArray] = $quotationArray[$dataArray];
				}
				else
				{
					$productArray = $quotationArray[$dataArray]->getProductArray();
					$quotationNumber = $quotationArray[$dataArray]->getQuotationNumber();
					$total = $quotationArray[$dataArray]->getTotal();
					$totalDiscounttype = $quotationArray[$dataArray]->getTotalDiscounttype();
					$totalDiscount = $quotationArray[$dataArray]->getTotalDiscount();
					$totalCgstPercentage = $quotationArray[$dataArray]->getTotalCgstPercentage();
					$totalSgstPercentage = $quotationArray[$dataArray]->getTotalSgstPercentage();
					$totalIgstPercentage = $quotationArray[$dataArray]->getTotalIgstPercentage();
					$extraCharge = $quotationArray[$dataArray]->getExtraCharge();
					$tax = $quotationArray[$dataArray]->getTax();
					$grandTotal = $quotationArray[$dataArray]->getGrandTotal();
					$remark = $quotationArray[$dataArray]->getRemark();
					$entryDate = $quotationArray[$dataArray]->getEntryDate();
					$companyId = $quotationArray[$dataArray]->getCompanyId();
					$branchId = $quotationArray[$dataArray]->getBranchId() ? $quotationArray[$dataArray]->getBranchId() : 0;
					$ClientId = $quotationArray[$dataArray]->getClientId();
					$jfId= $quotationArray[$dataArray]->getJfId();
					$poNumber= $quotationArray[$dataArray]->getPoNumber();
					$paymentMode= $quotationArray[$dataArray]->getPaymentMode();
					$invoiceNumber= $quotationArray[$dataArray]->getInvoiceNumber();
					$bankName= $quotationArray[$dataArray]->getBankName();
					$checkNumber= $quotationArray[$dataArray]->getCheckNumber();
				}
			}
			
		}
		//data pass to the model object for insert
		$quotationModel = new QuotationModel();
		$status = $quotationModel->insertData($productArray,$quotationNumber,$total,$extraCharge,$tax,$grandTotal,$remark,$entryDate,$companyId,$branchId,$ClientId,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$documentArray,$headerData,$poNumber,$paymentMode,$invoiceNumber,$bankName,$checkNumber);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['500'])==0)
		{
			return $status;
		}
		else
		{
			if(array_key_exists("issalesorder",$headerData))
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
	}

	/**
     * get the data from persistable object and call the model for database insertion opertation
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function logWorkflowStatus()
	{
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$statusArray = func_get_arg(0);
		$headerData = func_get_arg(1);
		if(is_object($statusArray))
		{
			$statusLogData = [];
			$conditionCheck = [];
			$statusLogData['workflow_status_id'] = $statusArray->getWorkflowStatusId();
			$statusLogData['assigned_to'] = $statusArray->getAssignedTo();
			$statusLogData['assigned_by'] = $statusArray->getAssignedBy();
			if ($statusArray->getCompanyId()) {
				$statusLogData['company_id'] = $statusArray->getCompanyId();
			}
			if ($statusArray->getQuotationId()) {
				$conditionCheck['quotation_id'] = $statusLogData['quotation_id'] = $statusArray->getQuotationId();
			}
			if ($statusArray->getSaleId()) {
				$statusLogData['sale_id'] = $conditionCheck['sale_id'] = $statusArray->getSaleId();
			}
			$quotationModel = new QuotationModel();
			$logStatus = $quotationModel->logWorkflowStatus($statusLogData,$headerData,$conditionCheck);
			return $logStatus;
		}
		else{
			return $exceptionArray['content'];
		}
	}
	
	/**
     * get quotation data as per given data in header
     * @param header-data
     * @return array-data/error message
     */
	public function getSearchingData($headerData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		//data pass to the model object for getData
		$quotationModel = new QuotationModel();
		$quotationResult = $quotationModel->getSpecifiedData($headerData);
		if(strcmp($quotationResult,$exceptionArray['204'])==0)
		{
			return $quotationResult;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedAllData($quotationResult);
			return $encodingResult;
		}
	}
	/**
	 * @param trimmed request
	 * @return status
	 */
	public function dispatch($dispatchData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$dispatch = [];
		foreach ($dispatchData as $key => $value) {
			$key = strtolower(preg_replace("([A-Z])", "_$0", $key));
			$dispatch[$key] = $value;
		}
		$quotationModel = new QuotationModel();
		$status = $quotationModel->dispatchInsert($dispatch);
		return $status;
	}
	/**
     * get quotation data as per given data in header
     * @param header-data
     * @return array-data/error message
     */
	public function getStatusData($headerData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		//data pass to the model object for getData
		$quotationModel = new QuotationModel();
		$result = $quotationModel->getStatusData($headerData);
		if(strcmp($result,$exceptionArray['204'])==0)
		{
			return $result;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedStatusData($result);
			return $encodingResult;
		}
	}
	/**
     * get quotation data as per given data in header
     * @param header-data
     * @return array-data/error message
     */
	public function getQuotationStatusCounts($companyId,$headerData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		//data pass to the model object for getData
		$quotationModel = new QuotationModel();
		$result = $quotationModel->getStatusQuoteCountData($companyId,$headerData);
		if(strcmp($result,$exceptionArray['204'])==0)
		{
			return $result;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedStatusCountData($result);
			return $encodingResult;
		}
	}
	public function getDispatched($saleId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		//data pass to the model object for getData
		$quotationModel = new QuotationModel();
		$result = $quotationModel->getDispatched($saleId);
		return $result;
	}
	/**
     * update quotation data
     * @param QuotationPersistable $persistable
     * @return status/error message
     */
	public function updateData()
	{
		$persistableData = func_get_arg(0);
		$quotationBillId = func_get_arg(1);
		$headerData = func_get_arg(2);
		$flag=0;
		$inventoryFlag=0;
		$dataFlag=0;
		$contentFlag=0;
		$noDataFlag=0;
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$quotationModel = new QuotationModel();
		if(empty($persistableData) && count($persistableData)==0)
		{
			$noDataFlag=1;
		}
		else if(is_array($persistableData))
		{
			$documentFlag=0;
			$persistableData1 = array();
			$singleData=array();
			$quotationId = $quotationBillId;
			$documentArray=array();
			$dataCount = count($persistableData);
			for($dataArray=0;$dataArray<$dataCount;$dataArray++)
			{
				if(is_object($persistableData[$dataArray]))
				{
					$contentFlag=1;
					$persistableData1 = $persistableData;
				}
				else
				{
					if(is_object($persistableData[$dataArray][0]))
					{
						$contentFlag=1;
						if($documentFlag==1)
						{
							$persistableData1 = $persistableData[$dataCount-1];
						}
						else
						{
							$persistableData1 = $persistableData[$dataCount-1];
						}
					}
					else
					{
						if($contentFlag!=1)
						{
							$documentFlag=1;
							//document is available
							$documentArray[$dataArray] = $persistableData[$dataArray];
						}
					}
				}
			}
			$arrayLength = count($persistableData1);
			$innerArrayLength = count($persistableData1[$arrayLength-1]);
			if(is_array($persistableData1))
			{
				if($innerArrayLength!=0)
				{
					if(!is_object($persistableData1[$arrayLength-1]))
					{
						//inventory is available
						$flag=1;
					}
					if($flag==1)
					{
						$quotationId = $quotationBillId;
						$quotationCount = count($persistableData1)-1;
						for($arrayData=0;$arrayData<$quotationCount;$arrayData++)
						{
							if($persistableData1[$arrayData]->getProductArray())
							{
								$inventoryFlag=1;
								$singleData['product_array'] = $persistableData1[$arrayData]->getProductArray();
							}
							else
							{
								$dataFlag=1;
								$funcName = $persistableData1[$arrayData]->getName();
								$value = $persistableData1[$arrayData]->$funcName();
								$key = $persistableData1[$arrayData]->getKey();
								$singleData[$key] = $value;
							}
						}
					}
					else
					{
						$quotationId = $quotationBillId;
						$quotationCount = count($persistableData1);
						for($arrayData=0;$arrayData<$quotationCount;$arrayData++)
						{
							$dataFlag=1;
							$funcName = $persistableData1[$arrayData]->getName();
							$value = $persistableData1[$arrayData]->$funcName();
							$key = $persistableData1[$arrayData]->getKey();
							$singleData[$key] = $value;
						}
					}
				}
			}
			$quotationUpdateResult = $quotationModel->updateQuotationData($singleData,$quotationId,$headerData,$documentArray,$contentFlag);
			if(strcmp($quotationUpdateResult,$exceptionArray['204'])==0 || strcmp($quotationUpdateResult,$exceptionArray['500'])==0)
			{
				return $quotationUpdateResult;
			}
		}
		else
		{
			// if(!is_object($persistableData[count($persistableData)-1]))
			// {
				//inventory is available
				// $flag=1;
			// }
			// $singleData = array();
			// if($flag==1)
			// {
				// $quotationId = $persistableData[0]->getQuotationId();
				// for($arrayData=0;$arrayData<count($persistableData)-1;$arrayData++)
				// {
					// if($persistableData[$arrayData]->getProductArray())
					// {
						// $inventoryFlag=1;
						// $singleData['product_array'] = $persistableData[$arrayData]->getProductArray();
					// }
					// else
					// {
						// $dataFlag=1;
						// $funcName = $persistableData[$arrayData]->getName();
						// $value = $persistableData[$arrayData]->$funcName();
						// $key = $persistableData[$arrayData]->getKey();
						// $singleData[$key] = $value;
					// }
				// }
			// }
			// else
			// {
				// $quotationId = $persistableData[0]->getQuotationId();
				// for($arrayData=0;$arrayData<count($persistableData);$arrayData++)
				// {
					// $dataFlag=1;
					// $funcName = $persistableData[$arrayData]->getName();
					// $value = $persistableData[$arrayData]->$funcName();
					// $key = $persistableData[$arrayData]->getKey();
					// $singleData[$key] = $value;
				// }
			// }
			// $quotationModel = new QuotationModel();
			// $quotationUpdateResult = $quotationModel->updateQuotationData($singleData,$quotationId);
			// if(strcmp($quotationUpdateResult,$exceptionArray['204'])!=0 || strcmp($quotationUpdateResult,$exceptionArray['500'])!=0)
			// {
				// $encoded = new EncodeData();
				// $encodeData = $encoded->getEncodedData($quotationUpdateResult);
				// $decodedQuotationData = json_decode($encodeData);
				
				// $quotationBillIdArray = array();
				// $quotationBillIdArray['quotationBillId'] = $quotationId;
				// $quotationBillIdArray['companyId'] = $decodedQuotationData->company->companyId;
				// $quotationBillIdArray['quotationData'] = $decodedQuotationData;
				// $documentController = new DocumentController(new Container());
				// $method=$constantArray['postMethod'];
				// $path=$constantArray['documentGenerateQuotationUrl'];
				// $documentRequest = Request::create($path,$method,$quotationBillIdArray);
				// $processedData = $documentController->getQuotationData($documentRequest);
				// return $processedData;
			// }
		}
		if(array_key_exists("issalesorder",$headerData))
		{
			$saleIdArray = array();
			$saleIdArray['saleId'] = $quotationId;
			$documentController = new DocumentController(new Container());
			
			$method=$constantArray['postMethod'];
			$path=$constantArray['documentGenerateUrl'];
			$documentRequest = Request::create($path,$method,$saleIdArray);
			
			if(array_key_exists('operation',$headerData))
			{
				$documentRequest->headers->set('operation',$headerData['operation'][0]);
				$documentRequest->headers->set('issalesorder',"ok");
			}
			else
			{
				$documentRequest->headers->set('key',$headerData);
				$documentRequest->headers->set('issalesorder',"ok");
			}
			$processedData = $documentController->getData($documentRequest);
			return $processedData;
		}
		else
		{
			if($noDataFlag==1)
			{
				//get quotation data
				$quotationUpdateResult = $quotationModel->getquotationIdData($quotationBillId);
				if(strcmp($quotationUpdateResult,$exceptionArray['204'])==0)
				{
					return $exceptionArray['204'];
				}
			}
			//pdf generation for update quotation data
			$documentController = new DocumentController(new Container());
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($quotationUpdateResult);
			$decodedQuotationData = json_decode($encodeData);
			
			$quotationBillIdArray = array();
			$quotationBillIdArray['quotationBillId'] = $quotationBillId;
			$quotationBillIdArray['companyId'] = $decodedQuotationData->company->companyId;
			$quotationBillIdArray['quotationData'] = $decodedQuotationData;
			$method=$constantArray['postMethod'];
			$path=$constantArray['documentGenerateQuotationUrl'];
			$documentRequest = Request::create($path,$method,$quotationBillIdArray);
			if(array_key_exists('operation',$headerData))
			{
				$documentRequest->headers->set('operation',$headerData['operation'][0]);
			}
			else
			{
				$documentRequest->headers->set('key',$headerData);
			}
			$processedData = $documentController->getQuotationData($documentRequest);
			return $processedData;
		}
	}
}