<?php
namespace ERP\Api\V1_0\Accounting\Journals\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Accounting\Journals\Services\JournalService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Accounting\Journals\Processors\JournalProcessor;
use ERP\Core\Accounting\Journals\Persistables\JournalPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Support\Collection;
use ERP\Api\V1_0\Products\Processors\ProductProcessor;
use ERP\Core\Products\Services\ProductService;
use ERP\Core\Products\Persistables\ProductPersistable;
use ERP\Model\Accounting\Journals\JournalModel;
use ERP\Entities\Constants\ConstantClass;
use Illuminate\Container\Container;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Model\Products\ProductModel;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JournalController extends BaseController implements ContainerInterface
{
	/**
     * @var journalService
     * @var processor
     * @var request
     * @var journalPersistable
     */
	private $journalService;
	private $processor;
	private $request;
	private $journalPersistable;	
	
	/**
	 * get and invoke method is of ContainerInterface method
	 */		
    public function get($id,$name)
	{
		// echo "get";
	}
	public function invoke(callable $method)
	{
		// echo "invoke";
	}
	
	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function store(Request $request)
    {
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
		if(strcmp($RequestUri[1],"accounting")==0 && strcmp($RequestUri[2],"bills")==0 || strcmp($RequestUri[1],"accounting")==0 && strcmp($RequestUri[2],"purchase-bills")==0 || strcmp($RequestUri[1],"accounting")==0 && strcmp($RequestUri[2],"sales-returns")==0)
		{
		}
		else
		{
			//Authentication
			$tokenAuthentication = new TokenAuthentication();
			$authenticationResult = $tokenAuthentication->authenticate($request->header());
			
			if(strcmp($constantArray['success'],$authenticationResult)!=0)
			{
				return $authenticationResult;
			}
			if(in_array(true,$request->file()))
			{
				$documentPath = $constantArray['journalDocumentUrl'];
				$documentController =new DocumentController(new Container());
				$processedData = $documentController->insertUpdate($this->request,$documentPath);
				if(is_array($processedData))
				{
					$journalModel = new JournalModel();
					$documentResult = $journalModel->insertPurchaseDocumentData($processedData,$request->header()['type'][0]);
					if(strcmp($documentResult,$exceptionArray['200'])!=0)
					{
						return $documentResult;
					}
				}
				else
				{
					return $processedData;
				}
			}
		}
		//special journal entry and inventory entry
		$this->request = $request;
		$jfId = trim($this->request->input()['jfId']);
		
		// check the requested Http method
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		// insert
		if($requestMethod == $constantArray['postMethod'])
		{
			$processor = new JournalProcessor();
			$journalPersistable = new JournalPersistable();
			$journalPersistable = $processor->createPersistable($this->request);
			if(is_array($journalPersistable))
			{
				// print_r($journalPersistable);
				$journalService= new JournalService();
				$status = $journalService->insert($journalPersistable);
				if(count($request->input())>4)
				{
					$productService= new ProductService();	
					$productPersistable = new ProductPersistable();
					if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
					{
						$outward = $constantArray['journalOutward'];
						$productProcessor = new ProductProcessor();
						$productPersistable = $productProcessor->createPersistableInOutWard($this->request,$outward);
						if(is_array($productPersistable))
						{
							$vendorId = "";
							$status = $productService->insertInOutward($productPersistable,$jfId,$vendorId);
							return $status;
						}
						else
						{
							return $productPersistable;
						}
					}
					else if(strcmp($request->header()['type'][0],$constantArray['purchase'])==0)
					{
						$inward = $constantArray['journalInward'];
						$productProcessor = new ProductProcessor();
						$productPersistable = $productProcessor->createPersistableInOutWard($this->request,$inward);
						$vendorId = $productProcessor->processVendorId($request->input());
						if(is_array($productPersistable) && is_array($vendorId))
						{
							$status = $productService->insertInOutward($productPersistable,$jfId,$vendorId['vendor_id']);
							return $status;
						}
						else
						{
							return $productPersistable;
						}
					}elseif(strcmp($request->header()['type'][0],$constantArray['receiptType'])==0) {
						return $status;
					}elseif(strcmp($request->header()['type'][0],$constantArray['paymentType'])==0) {
						return $status;
					}elseif(strcmp($request->header()['type'][0], $constantArray['salesReturnType'])==0) {
						$inward = $constantArray['journalInward'];
						$productProcessor = new ProductProcessor();
						$productPersistable = $productProcessor->createPersistableInOutWard($this->request,$inward);
						if(is_array($productPersistable))
						{
							$status = $productService->insertInOutward($productPersistable,$jfId,'');
							return $status;
						}
						else
						{
							return $productPersistable;
						}
					}
				}
				else
				{
					return $status;
				}
			}
			else
			{
				return $journalPersistable;
			}
		}
	}
	
	/**
     * get the next journal folio id
     */
    public function getData(Request $request)
    {
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
		if(strcmp($RequestUri[1],"accounting")==0 || strcmp($RequestUri[2],"bills")==0)
		{
			$journalService = new JournalService();
			$status = $journalService->getJournalData();
			return $status;
		}
		else
		{
			//Authentication
			$tokenAuthentication = new TokenAuthentication();
			$authenticationResult = $tokenAuthentication->authenticate($request->header());
			
			//get constant array
			$constantClass = new ConstantClass();
			$constantArray = $constantClass->constantVariable();
			
			if(strcmp($constantArray['success'],$authenticationResult)==0)
			{
				$journalService = new JournalService();
				$status = $journalService->getJournalData();
				return $status;
			}
			else
			{
				return $authenticationResult;
			}
		}
	}
	
	/**
     * get the journal data
     */
    public function getJournalData(Request $request,$journalId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$journalService = new JournalService();
			$status = $journalService->getJournalArrayData($journalId);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specific data between given date or current year data
     */
    public function getSpecificData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if(array_key_exists("type",$request->header()))
			{
				if(strcmp(trim($request->header()['type'][0]),$constantArray['sales'])==0 || strcmp(trim($request->header()['type'][0]),$constantArray['purchase'])==0)
				{
					//get journal-data as well as transaction-data for update
					if(array_key_exists($constantArray['jfId'],$request->header()))
					{
						$jfId = $request->header()['jfid'];
						$journalModel = new JournalModel();
						$status = $journalModel->getJournalTransactionData($companyId,$request->header()['type'][0],$jfId);
						if(is_array($status))
						{
							$result = json_decode($status);
							return $status;
						}
						else
						{
							return $status;
						}
					}
				}
				else
				{
					return $exceptionArray['content'];
				}
			}
			//get the data between fromDate and toDate
			else if(array_key_exists($constantArray['fromDate'],$request->header()) && array_key_exists($constantArray['toDate'],$request->header()))
			{
				if(array_key_exists('journaltype',$request->header()))
				{
					$this->request = $request;
					$processor = new JournalProcessor();
					$journalPersistable = new JournalPersistable();
					$journalPersistable = $processor->createPersistableData($this->request);
					$journalService= new JournalService();
					$status = $journalService->getJournalDetail($journalPersistable,$companyId,$request->header()['journaltype'][0]);
					return $status;
				}
				else
				{
					return $exceptionArray['content'];
				}
			}
			//if date is not given..get the data of current year
			else
			{
				$journalModel = new JournalModel();
				$status = $journalModel->getCurrentYearData($companyId);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * update the specified resource 
	 * @param  Request object[Request $request] and journal-folio id
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function update(Request $request,$jfId)
	{
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
			
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
		if(strcmp($RequestUri[1],"accounting")==0 && strcmp($RequestUri[2],"bills")==0 || 
			strcmp($RequestUri[1],"accounting")==0 && strcmp($RequestUri[2],"purchase-bills")==0)
		{   
			$this->request = $request;
			$processor = new JournalProcessor();
			$journalPersistable = new JournalPersistable();		
			$journalService= new JournalService();		
			$journalModel = new JournalModel();
			$jfIdArrayData = $journalModel->getJfIdArrayData($jfId);
			$entryDateFlag=0;
			$companyIdFlag=0;
			$journalArrayFlag=0;
			$invoiceNumberFlag=0;
			$productArrayFlag=0;
			$transactionDateFlag=0;
			$billNumberFlag=0;
			$taxFlag=0;
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			//check array exists
			if(array_key_exists($constantArray['data'], $this->request->input()))
			{
				$journalData = $this->request->input()['data'];
				$dataCountOfArray = count($this->request->input()['data']);
				for($dataArray=0;$dataArray<$dataCountOfArray-1;$dataArray++)
				{
					if(strcmp($journalData[$dataArray]['ledgerId'],$journalData[$dataArray+1]['ledgerId'])==0)
					{
						return $exceptionArray['content'];
					}
				}
			}
			//check journal-data is available in database as per given jf-id
			if(strcmp($jfIdArrayData,$exceptionArray['404'])==0)
			{
				return $exceptionArray['404'];
			}
			if(array_key_exists($constantArray['type'],$request->header()))
			{
				if(strcmp($request->header()['type'][0],$constantArray['sales'])==0 || strcmp($request->header()['type'][0],$constantArray['purchase'])==0)
				{
					$productArray = array();
					$journalArray = array();
					$inputArray = $this->request->input();
					if(array_key_exists($constantArray['entryDate'],$inputArray))
					{
						$entryDateFlag=1;
						$journalArray['entryDate']=$inputArray['entryDate'];
					}
					if(array_key_exists('transactionDate',$inputArray))
					{
						$transactionDateFlag=1;
						$productArray['transactionDate']=$inputArray['transactionDate'];
					}
					if(array_key_exists($constantArray['companyId'],$inputArray))
					{
						$companyIdFlag=1;
						$journalArray['companyId']=$inputArray['companyId'];
						$productArray['companyId'] = $inputArray['companyId'];
					}
					if(array_key_exists($constantArray['invoiceNumber'],$inputArray))
					{
						$invoiceNumberFlag=1;
						$productArray['invoiceNumber'] = $inputArray['invoiceNumber'];
					}
					if(array_key_exists($constantArray['billNumber'],$inputArray))
					{
						$billNumberFlag=1;
						$productArray['billNumber'] = $inputArray['billNumber'];
					}
					if(array_key_exists($constantArray['tax'],$inputArray))
					{
						$taxFlag=1;
						$productArray['tax'] = $inputArray['tax'];
					}
					//check array exists in request 
					if(array_key_exists($constantArray['data'],$this->request->input()))
					{
						$journalArrayFlag=1;
						$journalArray['data']=array();
						for($arrayData=0;$arrayData<count($this->request->input()['data']);$arrayData++)
						{
							$journalArray['data'][$arrayData]=array();
							$journalArray['data'][$arrayData]['amount']=$this->request->input()['data'][$arrayData]['amount'];
							$journalArray['data'][$arrayData]['amountType']=$this->request->input()['data'][$arrayData]['amountType'];
							$journalArray['data'][$arrayData]['ledgerId']=$this->request->input()['data'][$arrayData]['ledgerId'];
						}
					}
					//check array is exists in request
					if(array_key_exists($constantArray['inventory'],$inputArray))
					{
						$productArrayFlag=1;
						$productArray['inventory'] = array();
						for($inventoryArray=0;$inventoryArray<count($inputArray['inventory']);$inventoryArray++)
						{
							$productArray['inventory'][$inventoryArray] = array();
							$productArray['inventory'][$inventoryArray]['productId']=$inputArray['inventory'][$inventoryArray]['productId'];
							$productArray['inventory'][$inventoryArray]['discount']=$inputArray['inventory'][$inventoryArray]['discount'];
							$productArray['inventory'][$inventoryArray]['discountType']=$inputArray['inventory'][$inventoryArray]['discountType'];
							$productArray['inventory'][$inventoryArray]['price']=$inputArray['inventory'][$inventoryArray]['price'];
							$productArray['inventory'][$inventoryArray]['qty']=$inputArray['inventory'][$inventoryArray]['qty'];
							$productArray['inventory'][$inventoryArray]['measurementUnit']=$inputArray['inventory'][$inventoryArray]['measurementUnit'];
						}
					}
					//journal data is available in sale/purchase for update
					if($entryDateFlag==1 || $companyIdFlag==1 || $journalArrayFlag==1)
					{
						if($productArrayFlag==1 || $taxFlag==1)
						{
							//journal data is processed(trim,validation and set data in object)
							$journalPersistable = $processor->createPersistableChangeData($request->header(),$productArray,$journalArray,$jfId);
							if(!is_array($journalPersistable))
							{
								return $journalPersistable;
							}
						}
						else
						{
							//journal data is processed(trim,validation and set data in object)
							$journalPersistable = $processor->createPersistableChange($request,$request->header(),$journalArray,$jfId);
							if(!is_array($journalPersistable))
							{
								return $journalPersistable;
							}
						}
						if(is_array($journalPersistable))
						{
							if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
							{
								$headerType = $constantArray['saleType'];
							}
							else
							{
								$headerType = $constantArray['purchaseType'];
							}
							$status = $journalService->update($journalPersistable,$jfId,$headerType);
							
							//update data in product_transaction
							if(strcmp($status,$exceptionArray['200'])==0)
							{
								
								//product transaction data is available for update
								if($productArrayFlag==1 || $invoiceNumberFlag==1 || $entryDateFlag==1 || $companyIdFlag==1 || $billNumberFlag==1 || $transactionDateFlag==1 || $taxFlag==1)
								{
									//sale data update
									if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
									{ 
										if($billNumberFlag==1)
										{
											//wrong entry
										}
										else
										{
											$inOutward = $constantArray['journalOutward'];
										}
									}
									else
									{
										if($invoiceNumberFlag==1)
										{
											//wrong entry
										}
										else
										{
											$inOutward = $constantArray['journalInward'];
										}
									}
									$productService= new ProductService();	
									$productPersistable = new ProductPersistable();
									$productProcessor = new ProductProcessor();
									$productPersistable = $productProcessor->createPersistableChangeInOutWard($productArray,$inOutward,$jfId);
									//here two array and string is return at a time
									if(is_array($productPersistable))
									{
										$status = $productService->updateInOutwardData($productPersistable,$jfId,$inOutward);
										return $status;
									}
									else
									{
										return $productPersistable;
									}
								}
								else
								{
									return $status;
								}
								
							}
							else
							{
								return $journalPersistable;
							}
						}
					}
					else
					{
						//sale data update
						if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
						{
							if($billNumberFlag==1)
							{
								//wrong entry
							}
							else
							{
								$inOutward = $constantArray['journalOutward'];
							}
						}
						else
						{
							if($invoiceNumberFlag==1)
							{
								//wrong entry
							}
							else
							{
								$inOutward = $constantArray['journalInward'];
							}
						}
						$productService= new ProductService();	
						$productPersistable = new ProductPersistable();
						$productProcessor = new ProductProcessor();
						$productPersistable = $productProcessor->createPersistableChangeInOutWard($productArray,$inOutward,$jfId);
						
						//here two array and string is return at a time
						if(is_array($productPersistable))
						{
							$status = $productService->updateInOutwardData($productPersistable,$jfId,$inOutward);
							return $status;
						}
						else
						{
							return $productPersistable;
						}
					}
				}
				//payment/receipt
				else
				{
					$headerData = $request->header();
					$journalArray = $this->request->input();
					if(strcmp($headerData['type'][0],$constantArray['paymentType'])==0)
					{
						$headerType = $constantArray['paymentType'];
					}
					else
					{
						$headerType = $constantArray['receiptType'];
					}
					//journal data is processed(trim,validation and set data in object)
					$journalPersistable = $processor->createPersistableChange($request,$headerData,$journalArray,$jfId);
					//here two array and string is return at a time
					if(is_array($journalPersistable))
					{
						$status = $journalService->update($journalPersistable,$jfId,$headerType);
						return $status;
					}
					else
					{
						return $journalPersistable;
					}
				}
			}
			else
			{
				$headerData = $request->header();
				$headerType = $constantArray['specialJournalType'];
				$journalArray = $this->request->input();
				//journal data is processed(trim,validation and set data in object)
				$journalPersistable = $processor->createPersistableChange($request,$headerData,$journalArray,$jfId);
				
				//here two array and string is return at a time
				if(is_array($journalPersistable))
				{
					$status = $journalService->update($journalPersistable,$jfId,$headerType);
					return $status;
				}
				else
				{
					return $journalPersistable;
				}
			}
		}
		else
		{
			//Authentication
			$tokenAuthentication = new TokenAuthentication();
			$authenticationResult = $tokenAuthentication->authenticate($request->header());
			
			if(strcmp($constantArray['success'],$authenticationResult)==0)
			{
				$this->request = $request;
				$processor = new JournalProcessor();
				$journalPersistable = new JournalPersistable();		
				$journalService= new JournalService();		
				$journalModel = new JournalModel();
				$jfIdArrayData = $journalModel->getJfIdArrayData($jfId);
				$clientNameFlag=0;
				$entryDateFlag=0;
				$companyIdFlag=0;
				$journalArrayFlag=0;
				$invoiceNumberFlag=0;
				$productArrayFlag=0;
				$transactionDateFlag=0;
				$billNumberFlag=0;
				$taxFlag=0;
				
				//get exception message
				$exception = new ExceptionMessage();
				$exceptionArray = $exception->messageArrays();
				
				if(empty($this->request->input()) && in_array(true,$request->file()))
				{
					$documentPath = $constantArray['journalDocumentUrl'];
					$documentController =new DocumentController(new Container());
					$processedData = $documentController->insertUpdate($request,$documentPath);
					if(is_array($processedData))
					{
						$journalDocumentResult = $journalModel->updatePurchaseDocumentData($jfId,$processedData,$request->header()['type'][0]);
						return $journalDocumentResult;
					}
					else
					{
						return $processedData;
					}
				}
				else
				{
					//check array exists
					if(array_key_exists($constantArray['data'], $this->request->input()))
					{
						$journalData = $this->request->input()['data'];
						$dataCountOfArray = count($this->request->input()['data']);
						for($dataArray=0;$dataArray<$dataCountOfArray-1;$dataArray++)
						{
							if(strcmp($journalData[$dataArray]['ledgerId'],$journalData[$dataArray+1]['ledgerId'])==0)
							{
								return $exceptionArray['content'];
							}
						}
					}
					//check journal-data is available in database as per given jf-id
					if(strcmp($jfIdArrayData,$exceptionArray['404'])==0)
					{
						return $exceptionArray['404'];
					}
					if(array_key_exists($constantArray['type'],$request->header()))
					{
						if(strcmp($request->header()['type'][0],$constantArray['sales'])==0 || strcmp($request->header()['type'][0],$constantArray['purchase'])==0)
						{
							$productArray = array();
							$journalArray = array();
							$inputArray = $this->request->input();
							if(array_key_exists($constantArray['entryDate'],$inputArray))
							{
								$entryDateFlag=1;
								$journalArray['entryDate']=$inputArray['entryDate'];
							}
							if(array_key_exists('clientName',$inputArray))
							{
								$clientNameFlag=1;
							}
							if(array_key_exists('transactionDate',$inputArray))
							{
								$transactionDateFlag=1;
								$productArray['transactionDate']=$inputArray['transactionDate'];
							}
							if(array_key_exists($constantArray['companyId'],$inputArray))
							{
								$companyIdFlag=1;
								$journalArray['companyId']=$inputArray['companyId'];
								$productArray['companyId'] = $inputArray['companyId'];
							}
							if(array_key_exists($constantArray['invoiceNumber'],$inputArray))
							{
								$invoiceNumberFlag=1;
								$productArray['invoiceNumber'] = $inputArray['invoiceNumber'];
							}
							if(array_key_exists($constantArray['billNumber'],$inputArray))
							{
								$billNumberFlag=1;
								$productArray['billNumber'] = $inputArray['billNumber'];
							}
							if(array_key_exists($constantArray['tax'],$inputArray))
							{
								$taxFlag=1;
								$productArray['tax'] = $inputArray['tax'];
							}
							if($clientNameFlag==1)
							{
								//update client name
								$productProcessor = new ProductProcessor();
								$clientProcessResult = $productProcessor->processClientName($inputArray);
								if(is_array($clientProcessResult))
								{
									$productModel = new ProductModel();
									$clientUpdateResult = $productModel->updateClientName($clientProcessResult,$jfId);
									if(strcmp($clientUpdateResult,$exceptionArray['500'])==0)
									{
										return $clientUpdateResult;
									}
								}
								else
								{
									return $clientProcessResult;
								}
								if(count($inputArray)==1)
								{
									return $clientUpdateResult;
								}
							}
							//check array exists in request 
							if(array_key_exists($constantArray['data'],$this->request->input()))
							{
								$journalArrayFlag=1;
								$journalArray['data']=array();
								for($arrayData=0;$arrayData<count($this->request->input()['data']);$arrayData++)
								{
									$journalArray['data'][$arrayData]=array();
									$journalArray['data'][$arrayData]['amount']=$this->request->input()['data'][$arrayData]['amount'];
									$journalArray['data'][$arrayData]['amountType']=$this->request->input()['data'][$arrayData]['amountType'];
									$journalArray['data'][$arrayData]['ledgerId']=$this->request->input()['data'][$arrayData]['ledgerId'];
								}
							}
							//check array is exists in request
							if(array_key_exists($constantArray['inventory'],$inputArray))
							{
								$productArrayFlag=1;
								$productArray['inventory'] = array();
								for($inventoryArray=0;$inventoryArray<count($inputArray['inventory']);$inventoryArray++)
								{
									$productArray['inventory'][$inventoryArray] = array();
									$productArray['inventory'][$inventoryArray]['productId']=$inputArray['inventory'][$inventoryArray]['productId'];
									$productArray['inventory'][$inventoryArray]['discount']=$inputArray['inventory'][$inventoryArray]['discount'];
									$productArray['inventory'][$inventoryArray]['discountType']=$inputArray['inventory'][$inventoryArray]['discountType'];
									$productArray['inventory'][$inventoryArray]['price']=$inputArray['inventory'][$inventoryArray]['price'];
									$productArray['inventory'][$inventoryArray]['qty']=$inputArray['inventory'][$inventoryArray]['qty'];
								}
							}
							//journal data is available in sale/purchase for update
							if($entryDateFlag==1 || $companyIdFlag==1 || $journalArrayFlag==1)
							{
								if($productArrayFlag==1 || $taxFlag==1)
								{
									//journal data is processed(trim,validation and set data in object)
									$journalPersistable = $processor->createPersistableChangeData($request->header(),$productArray,$journalArray,$jfId);
									if(!is_array($journalPersistable))
									{
										return $journalPersistable;
									}
								}
								else
								{
									//journal data is processed(trim,validation and set data in object)
									$journalPersistable = $processor->createPersistableChange($request,$request->header(),$journalArray,$jfId);
									if(!is_array($journalPersistable))
									{
										return $journalPersistable;
									}
								}
								if(is_array($journalPersistable))
								{
									if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
									{
										$headerType = $constantArray['saleType'];
									}
									else
									{
										$headerType = $constantArray['purchaseType'];
									}
									$status = $journalService->update($journalPersistable,$jfId,$headerType);
									//update data in product_transaction
									if(strcmp($status,$exceptionArray['200'])==0)
									{
										//product transaction data is available for update
										if($productArrayFlag==1 || $invoiceNumberFlag==1 || $entryDateFlag==1 || $companyIdFlag==1 || $billNumberFlag==1 || $transactionDateFlag==1 || $taxFlag==1)
										{
											//sale data update
											if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
											{ 
												if($billNumberFlag==1)
												{
													//wrong entry
												}
												else
												{
													$inOutward = $constantArray['journalOutward'];
												}
											}
											else
											{
												if($invoiceNumberFlag==1)
												{
													//wrong entry
												}
												else
												{
													$inOutward = $constantArray['journalInward'];
												}
											}
											$productService= new ProductService();	
											$productPersistable = new ProductPersistable();
											$productProcessor = new ProductProcessor();
											
											$productPersistable = $productProcessor->createPersistableChangeInOutWard($productArray,$inOutward,$jfId);
											//here two array and string is return at a time
											if(is_array($productPersistable))
											{
												$status = $productService->updateInOutwardData($productPersistable,$jfId,$inOutward);
												
												//purchase document update
												if(in_array(true,$request->file()) && strcmp($status,$exceptionArray['200'])==0)
												{ 
													$documentPath = $constantArray['journalDocumentUrl'];
													$documentController =new DocumentController(new Container());
													$processedData = $documentController->insertUpdate($request,$documentPath);
													if(is_array($processedData))
													{
														$journalDocumentResult = $journalModel->updatePurchaseDocumentData($jfId,$processedData,$request->header()['type'][0]);
														return $journalDocumentResult;
													}
													else
													{
														return $processedData;
													}
												}
												else
												{
													return $status;
												}
											}
											else
											{
												return $productPersistable;
											}
										}
										else
										{
											return $status;
										}
										
									}
									else
									{
										return $journalPersistable;
									}
								}
							}
							else
							{
								//sale data update
								if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
								{
									if($billNumberFlag==1)
									{
										//wrong entry
									}
									else
									{
										$inOutward = $constantArray['journalOutward'];
									}
								}
								else
								{
									if($invoiceNumberFlag==1)
									{
										//wrong entry
									}
									else
									{
										$inOutward = $constantArray['journalInward'];
									}
								}
								$productService= new ProductService();	
								$productPersistable = new ProductPersistable();
								$productProcessor = new ProductProcessor();
								$productPersistable = $productProcessor->createPersistableChangeInOutWard($productArray,$inOutward,$jfId);
								
								//here two array and string is return at a time
								if(is_array($productPersistable))
								{
									$status = $productService->updateInOutwardData($productPersistable,$jfId,$inOutward);
									$documentPath = $constantArray['journalDocumentUrl'];
									if(in_array(true,$request->file()) && strcmp($status,$exceptionArray['200'])==0)
									{
										$documentController =new DocumentController(new Container());
										$processedData = $documentController->insertUpdate($request,$documentPath);
										if(is_array($processedData))
										{
											$journalDocumentResult = $journalModel->updatePurchaseDocumentData($jfId,$processedData,$request->header()['type'][0]);
											return $journalDocumentResult;
										}
										else
										{
											return $processedData;
										}
									}	
									else
									{
										return $status;
									}
								}
								else
								{
									return $productPersistable;
								}
							}
						} 
						//payment/receipt
						else
						{
							$headerData = $request->header();
							$journalArray = $this->request->input();
							if(strcmp($headerData['type'][0],$constantArray['paymentType'])==0)
							{
								$headerType = $constantArray['paymentType'];
							}
							else
							{
								$headerType = $constantArray['receiptType'];
							}
							//journal data is processed(trim,validation and set data in object)
							$journalPersistable = $processor->createPersistableChange($request,$headerData,$journalArray,$jfId);
							
							//here two array and string is return at a time
							if(is_array($journalPersistable))
							{
								$status = $journalService->update($journalPersistable,$jfId,$headerType);
								return $status;
							}
							else
							{
								return $journalPersistable;
							}
						}
					}
					else
					{
						$headerData = $request->header();
						$headerType = $constantArray['specialJournalType'];
						$journalArray = $this->request->input();
						//journal data is processed(trim,validation and set data in object)
						$journalPersistable = $processor->createPersistableChange($request,$headerData,$journalArray,$jfId);
						
						//here two array and string is return at a time
						if(is_array($journalPersistable))
						{
							$status = $journalService->update($journalPersistable,$jfId,$headerType);
							return $status;
						}
						else
						{
							return $journalPersistable;
						}
					}
				}
			}
			else
			{
				return $authenticationResult;
			}
		}
	}
}
