<?php
namespace ERP\Core\Accounting\Bills\Services;

use ERP\Core\Accounting\Bills\Persistables\BillPersistable;
use ERP\Core\Accounting\Bills\Entities\Bill;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\Bills\Entities\EncodeData;
use ERP\Core\Accounting\Bills\Entities\EncodeAllData;
use ERP\Core\Accounting\Bills\Entities\EncodeAllDraftData;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Request;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Entities\Constants\ConstantClass;
/** 
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BillService
{
    /**
     * @var billService
	 * $var billModel
     */
    private $billService;
    private $billModel;
	
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
		$billArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$billArray = func_get_arg(0);
		$requestInput = func_get_arg(1);
		
		//only data insertion
		if(is_object($billArray))
		{
			// print_r($billArray);
			// exit;

			$productArray = $billArray->getProductArray();
			$paymentMode = $billArray->getPaymentMode();
			$bankLedgerId = @$billArray->getBankLedgerId() ? @$billArray->getBankLedgerId() : 0;
			$invoiceNumber = $billArray->getInvoiceNumber();
			$jobCardNumber = $billArray->getJobCardNumber();
			$bankName = $billArray->getBankName();
			$checkNumber = $billArray->getCheckNumber();
			$total = $billArray->getTotal();
			$totalDiscounttype = $billArray->getTotalDiscounttype();
			$totalDiscount = $billArray->getTotalDiscount();
			$totalCgstPercentage = $billArray->getTotalCgstPercentage();
			$totalSgstPercentage = $billArray->getTotalSgstPercentage();
			$totalIgstPercentage = $billArray->getTotalIgstPercentage();
			$extraCharge = $billArray->getExtraCharge();
			$tax = $billArray->getTax();
			$grandTotal = $billArray->getGrandTotal();
			$advance = $billArray->getAdvance();
			$balance = $billArray->getBalance();
			$remark = $billArray->getRemark();
			$entryDate = $billArray->getEntryDate();
			$companyId = $billArray->getCompanyId();
			$branchId = $billArray->getBranchId();
			$ClientId = $billArray->getClientId();
			$salesType = $billArray->getSalesType();
			$poNumber = $billArray->getPoNumber();
			$userId = $billArray->getUserId();
			$jfId= $billArray->getJfId();
			$expense= $billArray->getExpense();
			$serviceDate= $billArray->getServiceDate();
			$createdBy= $billArray->getCreatedBy();
			//data pass to the model object for insert
			$billModel = new BillModel();
			$status = $billModel->insertData($productArray,$paymentMode,$bankLedgerId,$invoiceNumber,$jobCardNumber,$bankName,$checkNumber,$total,$extraCharge,$tax,$grandTotal,$advance,$balance,$remark,$entryDate,$companyId,$branchId,$ClientId,$salesType,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$poNumber,$requestInput,$expense,$serviceDate,$userId,$createdBy);
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($status,$exceptionArray['500'])==0)
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
		//data with image insertion
		else
		{
			$documentArray = array();
			$productArray = $billArray[count($billArray)-1]->getProductArray();
			$paymentMode = $billArray[count($billArray)-1]->getPaymentMode();
			$bankLedgerId = $billArray[count($billArray)-1]->getBankLedgerId();
			$invoiceNumber = $billArray[count($billArray)-1]->getInvoiceNumber();
			$jobCardNumber = $billArray[count($billArray)-1]->getJobCardNumber();
			$bankName = $billArray[count($billArray)-1]->getBankName();
			$checkNumber = $billArray[count($billArray)-1]->getCheckNumber();
			$total = $billArray[count($billArray)-1]->getTotal();
			$totalDiscounttype = $billArray[count($billArray)-1]->getTotalDiscounttype();
			$totalDiscount = $billArray[count($billArray)-1]->getTotalDiscount();
			$totalCgstPercentage = $billArray[count($billArray)-1]->getTotalCgstPercentage();
			$totalSgstPercentage = $billArray[count($billArray)-1]->getTotalSgstPercentage();
			$totalIgstPercentage = $billArray[count($billArray)-1]->getTotalIgstPercentage();
			$extraCharge = $billArray[count($billArray)-1]->getExtraCharge();
			$tax = $billArray[count($billArray)-1]->getTax();
			$grandTotal = $billArray[count($billArray)-1]->getGrandTotal();
			$advance = $billArray[count($billArray)-1]->getAdvance();
			$balance = $billArray[count($billArray)-1]->getBalance();
			$remark = $billArray[count($billArray)-1]->getRemark();
			$entryDate = $billArray[count($billArray)-1]->getEntryDate();
			$companyId = $billArray[count($billArray)-1]->getCompanyId();
			$branchId = $billArray[count($billArray)-1]->getBranchId();
			$ClientId = $billArray[count($billArray)-1]->getClientId();
			$salesType = $billArray[count($billArray)-1]->getSalesType();
			$poNumber = $billArray[count($billArray)-1]->getPoNumber();
			$userId = $billArray[count($billArray)-1]->getUserId();
			$jfId = $billArray[count($billArray)-1]->getJfId();
			$expense = $billArray[count($billArray)-1]->getExpense();
			$serviceDate = $billArray[count($billArray)-1]->getServiceDate();
			$createdBy= $billArray[count($billArray)-1]->getCreatedBy();

			for($doc=0;$doc<(count($billArray)-1);$doc++)
			{
				array_push($documentArray,$billArray[$doc]);	
			}
			
			//data pass to the model object for insert
			$billModel = new BillModel();
			$status = $billModel->insertAllData($productArray,$paymentMode,$bankLedgerId,$invoiceNumber,$jobCardNumber,$bankName,$checkNumber,$total,$extraCharge,$tax,$grandTotal,$advance,$balance,$remark,$entryDate,$companyId,$branchId,$ClientId,$salesType,$documentArray,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$poNumber,$requestInput,$expense,$serviceDate,$userId,$createdBy);
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($status,$exceptionArray['500'])==0)
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
		$billModel = new BillModel();
		$billResult = $billModel->getSpecifiedData($companyId,$data);
		
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
     * get the data from persistable object and call the model for database get opertation
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function getDraftData($companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//data pass to the model object for getData
		$billModel = new BillModel();
		$billResult = $billModel->getBillDraftData($companyId);
		if(strcmp($billResult,$exceptionArray['204'])==0)
		{
			return $billResult;
		}
		else
		{
			$encodeAllData = new EncodeAllDraftData();
			$encodingResult = $encodeAllData->getEncodedAllData($billResult);
			return $encodingResult;
		}
	}

	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getBulkPrintData($saleIds,$headerData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		if ($saleIds == ''){
			return $exceptionArray['204'];
		}
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		$saleIdArray = array();
		$saleIdArray['saleIds'] = $saleIds;
		$documentController = new DocumentController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['documentGenerateUrl'];
		$documentRequest = Request::create($path,$method,$saleIdArray);
		// $documentRequest->headers->set('key',$headerData);
		
		$processedData = $documentController->getbulkPrintData($documentRequest);
		return $processedData;
	}
	
   	/**
     * call the model for database get opertation
     * @param headerData
     * @return sale-data/error message
     */
	public function getPreviousNextData($headerData)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		// data pass to the model object for getData
		$billModel = new BillModel();
		$billResult = $billModel->getPreviousNextData($headerData);
		
		if(strcmp($billResult,$exceptionArray['204'])==0)
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
     * @return sale-data/error message
     */
	public function getBillByJfId($companyId,$jfId)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		// data pass to the model object for getData
		$billModel = new BillModel();
		$billResult = $billModel->getBillByJfId($companyId,$jfId);
		
		if(strcmp($billResult,$exceptionArray['204'])==0)
		{
			return $billResult;
		}
		else
		{
			$encodeData = new EncodeAllData();
			$encodingResult = $encodeData->getEncodedAllData($billResult);
			return $encodingResult;
		}
	}
	
	 /**
     * update bill payment data
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function updatePaymentData()
	{
		$persistableData = func_get_arg(0);
		$billArray = $persistableData->getBillArray();
		$decodedBillData = json_decode($billArray);
		
		//data pass to the model object for getData
		$billModel = new BillModel();
		$billResult = $billModel->updatePaymentData($decodedBillData);
		return $billResult;
	}
	
	/**
     * update bill data
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function updateData()
	{
		$persistableData = func_get_arg(0);
		$saleId = func_get_arg(1);
		$headerData = func_get_arg(2);
		$flag=0;
		$inventoryFlag=0;
		$dataFlag=0;
		$noDataFlag=0;
		$ClientDataFlag=0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$imageArrayData = array();
		// if(is_object($persistableData))
		// {
			
		// }
		// else
		// {
			if(!is_array($persistableData) && !is_object($persistableData))
			{
				$noDataFlag=1;
			}
			else if(is_array($persistableData))
			{
				if(count($persistableData)==0)
				{
					//almost only client data is available
					$noDataFlag=1;
					$ClientDataFlag=1;
				}
				else
				{
					if(is_array($persistableData[0]))
					{
						$imageFlag=1;
						
						// store image in an array
						for($imageArray=0;$imageArray<count($persistableData)-1;$imageArray++)
						{
							$imageArrayData[$imageArray] = $persistableData[$imageArray];
						}
						$arrayLength = count($persistableData);
						if(is_array($persistableData[$arrayLength-1]) || is_object($persistableData[$arrayLength-1]))
						{
							$innerArrayLength = count($persistableData[$arrayLength-1]);
						}
						else
						{
							$innerArrayLength = 0;
						}
						if($innerArrayLength!=0)
						{
							if(is_array($persistableData[$arrayLength-1]))
							{
								if(!is_object($persistableData[$arrayLength-1][$innerArrayLength-1]))
								{
									// inventory is available
									$flag=1;
								}
							}
							if($flag==1)
							{
								$saleId = $persistableData[$arrayLength-1][0]->getSaleId();
								for($arrayData=0;$arrayData<count($persistableData[$arrayLength-1])-1;$arrayData++)
								{
									if($persistableData[$arrayLength-1][$arrayData]->getProductArray())
									{
										$inventoryFlag=1;
										$singleData['product_array'] = $persistableData[$arrayLength-1][$arrayData]->getProductArray();
									}
									else
									{
										$dataFlag=1;
										$funcName = $persistableData[$arrayLength-1][$arrayData]->getName();
										$value = $persistableData[$arrayLength-1][$arrayData]->$funcName();
										$key = $persistableData[$arrayLength-1][$arrayData]->getKey();
										$singleData[$key] = $value;
									}
								}
							}
							else
							{
								if(is_array($persistableData[$arrayLength-1]))
								{
									for($arrayData=0;$arrayData<count($persistableData[$arrayLength-1]);$arrayData++)
									{
										$dataFlag=1;
										$funcName = $persistableData[$arrayLength-1][$arrayData]->getName();
										$value = $persistableData[$arrayLength-1][$arrayData]->$funcName();
										$key = $persistableData[$arrayLength-1][$arrayData]->getKey();
										$singleData[$key] = $value;
										
									}
								}
								else if(is_object($persistableData[$arrayLength-1]))
 								{
									$singleData['entry_date'] =  $persistableData[$arrayLength-1]->getEntryDate();
								}
							}
							if(array_key_exists('entry_date',$singleData))
							{
								// date conversion 
								// entry-date conversion
								$splitedEntryDate = explode("-",$singleData['entry_date']);
								$transformEntryDates = $splitedEntryDate[2]."-".$splitedEntryDate[1]."-".$splitedEntryDate[0];
								$singleData['entry_date'] = $transformEntryDates;
							}
							if(array_key_exists('service_date',$singleData))
							{
								// date conversion 
								// entry-date conversion
								$splitedServiceDate = explode("-",$singleData['service_date']);
								$transformServiceDates = $splitedServiceDate[2]."-".$splitedServiceDate[1]."-".$splitedServiceDate[0];
								$singleData['service_date'] = $transformServiceDates;
							}
							//echo "www";
							$billModel = new BillModel();
							$billUpdateResult = $billModel->updateBillData($singleData,$saleId,$imageArrayData,$headerData);
							if(strcmp($billUpdateResult,$exceptionArray['200'])==0)
							{
								$saleIdArray = array();
								$saleIdArray['saleId'] = $saleId;
								$documentController = new DocumentController(new Container());
								$method=$constantArray['postMethod'];
								$path=$constantArray['documentGenerateUrl'];
								$documentRequest = Request::create($path,$method,$saleIdArray);
								if(array_key_exists('operation',$headerData))
								{
									$documentRequest->headers->set('operation',$headerData['operation'][0]);
								}
								else
								{
									$documentRequest->headers->set('key',$headerData);
								}
								if(array_key_exists("issalesorder",$headerData))
								{
									$documentRequest->headers->set('issalesorder',$headerData['issalesorder'][0]);
								}
								$processedData = $documentController->getData($documentRequest);
								return $processedData;
							}
						}
						else
						{
							// only image is available
							$billModel = new BillModel();
							$billUpdateResult = $billModel->updateImageData($saleId,$imageArrayData);
							if(strcmp($billUpdateResult,$exceptionArray['200'])==0)
							{
								$saleIdArray = array();
								$saleIdArray['saleId'] = $saleId;
								$documentController = new DocumentController(new Container());
								
								$method=$constantArray['postMethod'];
								$path=$constantArray['documentGenerateUrl'];
								$documentRequest = Request::create($path,$method,$saleIdArray);
								if(array_key_exists('operation',$headerData))
								{
									$documentRequest->headers->set('operation',$headerData['operation'][0]);
								}
								else
								{
									$documentRequest->headers->set('key',$headerData);
								}
								if(array_key_exists("issalesorder",$headerData))
								{
									$documentRequest->headers->set('issalesorder',$headerData['issalesorder'][0]);
								}
								$processedData = $documentController->getData($documentRequest);
								return $processedData;
							}
						}
					}
					else
					{
						if(!is_object($persistableData[count($persistableData)-1]))
						{
							// inventory is available
							$flag=1;
						}
						$singleData = array();
						if($flag==1)
						{
							$saleId = $persistableData[0]->getSaleId();
							for($arrayData=0;$arrayData<count($persistableData)-1;$arrayData++)
							{
								if($persistableData[$arrayData]->getProductArray())
								{
									$inventoryFlag=1;
									$singleData['product_array'] = $persistableData[$arrayData]->getProductArray();
								}
								else
								{
									$dataFlag=1;
									$funcName = $persistableData[$arrayData]->getName();
									$value = $persistableData[$arrayData]->$funcName();
									$key = $persistableData[$arrayData]->getKey();
									$singleData[$key] = $value;
								}
							}
						}
						else
						{
							$saleId = $persistableData[0]->getSaleId();
							for($arrayData=0;$arrayData<count($persistableData);$arrayData++)
							{
								$dataFlag=1;
								$funcName = $persistableData[$arrayData]->getName();
								$value = $persistableData[$arrayData]->$funcName();
								$key = $persistableData[$arrayData]->getKey();
								$singleData[$key] = $value;
							}
						}
						if(array_key_exists('entry_date',$singleData))
						{
							//date conversion 
							//entry-date conversion
							$splitedEntryDate = explode("-",$singleData['entry_date']);
							$transformEntryDate = $splitedEntryDate[2]."-".$splitedEntryDate[1]."-".$splitedEntryDate[0];
							$singleData['entry_date'] = $transformEntryDate;
						}
						if(array_key_exists('service_date',$singleData))
						{
							//date conversion 
							//entry-date conversion
							$splitedServiceDate = explode("-",$singleData['service_date']);
							$transformServiceDate = $splitedServiceDate[2]."-".$splitedServiceDate[1]."-".$splitedServiceDate[0];
							$singleData['service_date'] = $transformServiceDate;
						}
						$billModel = new BillModel();
						$billUpdateResult = $billModel->updateBillData($singleData,$saleId,$imageArrayData,$headerData);
						if(strcmp($billUpdateResult,$exceptionArray['200'])==0)
						{
							$saleIdArray = array();
							$saleIdArray['saleId'] = $saleId;
							$documentController = new DocumentController(new Container());
							
							$method=$constantArray['postMethod'];
							$path=$constantArray['documentGenerateUrl'];
							$documentRequest = Request::create($path,$method,$saleIdArray);
							if(array_key_exists('operation',$headerData))
							{
								$documentRequest->headers->set('operation',$headerData['operation'][0]);
							}
							else
							{
								$documentRequest->headers->set('key',$headerData);
							}
							if(array_key_exists("issalesorder",$headerData))
							{
								$documentRequest->headers->set('issalesorder',$headerData['issalesorder'][0]);
							}
							$processedData = $documentController->getData($documentRequest);
							return $processedData;
						}
					}
				}
			}
			else
			{
				$entryDate = $persistableData->getEntrydate();
				if ($entryDate != ''){
					//transform date
					//entry-date conversion
					$splitedEntryDate = explode("-",$entryDate);
					$transformEntryDate = $splitedEntryDate[2]."-".$splitedEntryDate[1]."-".$splitedEntryDate[0];
					//echo "ggg";
					$billModel = new BillModel();
					$billUpdateResult = $billModel->updateBillEntryData($transformEntryDate,$saleId,$headerData);
				}
				
				$clientId = $persistableData->getClientId();
				if ($clientId != '')
				{
					$singleData['client_id'] = $clientId;
					$billModel = new BillModel();
					$billUpdateResult = $billModel->updateBillData($singleData,$saleId,$imageArrayData = array(),$headerData);
				}
			
				if(strcmp($billUpdateResult,$exceptionArray['200'])==0)
				{
					$saleIdArray = array();
					$saleIdArray['saleId'] = $saleId;
					$documentController = new DocumentController(new Container());
					
					$method=$constantArray['postMethod'];
					$path=$constantArray['documentGenerateUrl'];
					$documentRequest = Request::create($path,$method,$saleIdArray);
					
					if(array_key_exists('operation',$headerData))
					{
						$documentRequest->headers->set('operation',$headerData['operation'][0]);
					}
					else
					{
						$documentRequest->headers->set('key',$headerData);
					}
					if(array_key_exists("issalesorder",$headerData))
					{
						$documentRequest->headers->set('issalesorder',$headerData['issalesorder'][0]);
					}
					$processedData = $documentController->getData($documentRequest);
					return $processedData;
				}
			}
		// }
		if($noDataFlag==1)
		{
			$saleIdArray = array();
			$saleIdArray['saleId'] = $saleId;
			$documentController = new DocumentController(new Container());
			
			$method=$constantArray['postMethod'];
			$path=$constantArray['documentGenerateUrl']; 
			$documentRequest = Request::create($path,$method,$saleIdArray);
			if(array_key_exists('operation',$headerData))
			{
				$documentRequest->headers->set('operation',$headerData['operation'][0]);
			}
			else
			{
				$documentRequest->headers->set('key',$headerData);
			}
			if(array_key_exists("issalesorder",$headerData))
			{
				$documentRequest->headers->set('issalesorder',$headerData['issalesorder'][0]);
			}
			$processedData = $documentController->getData($documentRequest);
			return $processedData;
		}
	}
}