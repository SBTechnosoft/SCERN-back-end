<?php
namespace ERP\Api\V1_0\Accounting\Bills\Processors;
	
use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Accounting\Bills\Persistables\BillPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Accounting\Bills\Validations\BillValidate;
use ERP\Api\V1_0\Accounting\Bills\Transformers\BillTransformer;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Model\Clients\ClientModel;
use ERP\Api\V1_0\Accounting\Journals\Controllers\JournalController;
use Illuminate\Container\Container;
use ERP\Api\V1_0\Clients\Controllers\ClientController;
use ERP\Api\V1_0\Accounting\Ledgers\Controllers\LedgerController;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Accounting\Bills\Entities\SalesTypeEnum;
use Carbon;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Core\Clients\Entities\ClientArray;
use ERP\Core\Accounting\Ledgers\Entities\LedgerArray;
use ERP\Model\Accounting\Journals\JournalModel;
use ERP\Core\Accounting\Journals\Validations\BuisnessLogic;
use ERP\Core\Entities\CompanyDetail;
use ERP\Model\Users\UserModel;
use ERP\Core\Users\Commissions\Services\CommissionService;
use ERP\Core\Products\Services\ProductService;
use ERP\Core\Companies\Services\CompanyService;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
	
class BillProcessor extends BaseProcessor
{	/**
     * @var billPersistable
	 * @var request
	*/
	private $billPersistable;
	private $request;   

	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Bill Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		
		// $clientContactFlag=0;
		$contactFlag=0;
		$paymentModeFlag=0;
		$taxFlag=0;
		$docFlag=0;
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();	
		//trim an input 
		$billTransformer = new BillTransformer();
		$tRequest = $billTransformer->trimInsertData($this->request);

		if($tRequest==1)
		{
			return $msgArray['content'];
		}	
		else
		{
			$ledgerModel = new LedgerModel();
			//validation
			$billValidate = new BillValidate();
			$status = $billValidate->validate($tRequest);
			if($status==$constantArray['success'])
			{
				//get contact-number from input data
				if(!array_key_exists($constantArray['contactNo'],$tRequest))
				{
					$contactNo="";
				}
				else
				{
					$contactNo = $tRequest['contact_no'];
				}
				// Get Staff ID to set Commission
				if (array_key_exists($constantArray['userId'], $tRequest)) {
					$userModel = new UserModel();
					$staffJsonData = $userModel->getData($tRequest['user_id']);
					$staffArrayData = json_decode($staffJsonData);
					if (is_array($staffArrayData) || is_object($staffArrayData)) {
						$staffLedgerData = $ledgerModel->getDataAsPerUserId($tRequest['company_id'],$tRequest['user_id']);
						if (is_array(json_decode($staffLedgerData))) {
							$decodedStaffData = json_decode($staffLedgerData,true)[0];
							$staffLedgerId = $decodedStaffData['ledger_id'];
							$staffLedgerValidationResult = $this->staffLedgerUpdate($staffArrayData[0],$staffLedgerId,$tRequest['user_id'],$staffLedgerData);
							if(strcmp($staffLedgerValidationResult,$msgArray['200'])!=0)
							{
								return $staffLedgerValidationResult;
							}
						}else{
							$staffLedgerInsertionResult = $this->staffLedgerInsertion($staffArrayData[0],$tRequest['user_id'],$tRequest['invoice_number'],$tRequest['company_id']);
							if(strcmp($msgArray['500'],$staffLedgerInsertionResult)==0 || strcmp($msgArray['content'],$staffLedgerInsertionResult)==0)
							{
								return $staffLedgerInsertionResult;
							}
							$staffLedgerId = json_decode($staffLedgerInsertionResult)[0]->ledger_id;
						}
					}
				}
				// End of Staff commission
				if($contactNo=="" || $contactNo==0)
				{
					//client insertion and ledger validation
					//ledger validation
					$result = $this->ledgerValidationOfInsertion($tRequest['company_id'],$tRequest['client_name'],$tRequest['contact_no']);
					if(is_array($result))
					{
						//client insertion
						$clientResult = $this->clientInsertion($tRequest);
						if(strcmp($clientResult,$msgArray['content'])==0)
						{
							return $clientResult;
						}
						$clientId = json_decode($clientResult)->clientId;
						$ledgerInsertionResult = $this->ledgerInsertion($tRequest,$clientId,$tRequest['invoice_number'],$tRequest['company_id']);
						//ledger insertion (|| $processedData[0][0]=='[' error while validation error occur)
						if(strcmp($msgArray['500'],$ledgerInsertionResult)==0 || strcmp($msgArray['content'],$ledgerInsertionResult)==0)
						{
							return $ledgerInsertionResult;
						}
						$ledgerId = json_decode($ledgerInsertionResult)[0]->ledger_id;
					}
					else
					{
						return $result;
					}
				}
				else
				{
					//check client is exists by contact-number
					$clientModel = new ClientModel();
					$clientArrayData = $clientModel->getClientData($contactNo);
					$clientData = (json_decode($clientArrayData));
					if(is_array($clientData) || is_object($clientData))
					{
						if(is_object($clientData))
						{
							$clientObjectData = $clientData->clientData;
						}
						else if(is_array($clientData))
						{
							$clientObjectData = $clientData['clientData'];
						}
						//update client-data and check ledger
						$ledgerData = $ledgerModel->getDataAsPerContactNo($tRequest['company_id'],$tRequest['contact_no']);
						if(is_array(json_decode($ledgerData)))
						{
							$ledgerId = json_decode($ledgerData)[0]->ledger_id;
							$inputArray = array();
							$inputArray['contactNo'] = $tRequest['contact_no'];
							//update client-data
							$encodedClientData = $clientObjectData;
							$clientId = $encodedClientData[0]->client_id;
							$clientUpdateResult = $this->clientUpdate($tRequest,$clientId,$clientArrayData);
							if(strcmp($clientUpdateResult,$msgArray['200'])!=0)
							{
								return $clientUpdateResult;
							}
							//update ledger-data
							$ledgerValidationResult = $this->ledgerUpdate($tRequest,$ledgerId,$clientId,$ledgerData);
							if(strcmp($ledgerValidationResult,$msgArray['200'])!=0)
							{
								return $ledgerValidationResult;
							}
						}
						else
						{
							//insert ledger and update client
							//ledger validation
							$result = $this->ledgerValidationOfInsertion($tRequest['company_id'],$tRequest['client_name'],$tRequest['contact_no']);
							if(is_array($result))
							{
								//update client
								//update client-data
								$encodedClientData = $clientObjectData;
								$clientId = $encodedClientData[0]->client_id;
								$clientUpdateResult = $this->clientUpdate($tRequest,$clientId,$clientArrayData);
								if(strcmp($clientUpdateResult,$msgArray['200'])!=0)
								{
									return $clientUpdateResult;
								}
								//insert ledger
								$ledgerInsertionResult = $this->ledgerInsertion($tRequest,$clientId,$tRequest['invoice_number'],$tRequest['company_id']);
								//ledger insertion (|| $processedData[0][0]=='[' error while validation error occur)
								if(strcmp($msgArray['500'],$ledgerInsertionResult)==0 || strcmp($msgArray['content'],$ledgerInsertionResult)==0)
								{
									return $ledgerInsertionResult;
								}
								$ledgerId = json_decode($ledgerInsertionResult)[0]->ledger_id;
							}
							else
							{
								return $result;
							}
						}
					}
					else
					{
						//client insert and ledger validation
						$ledgerData = $ledgerModel->getDataAsPerContactNo($tRequest['company_id'],$tRequest['contact_no']);
						if(is_array(json_decode($ledgerData)))
						{
							//client insertion
							$clientResult = $this->clientInsertion($tRequest);
							if(strcmp($clientResult,$msgArray['content'])==0)
							{
								return $clientResult;
							}
							$clientId = json_decode($clientResult)->clientId;
							$ledgerId = json_decode($ledgerData)[0]->ledger_id;
							//update ledger-data
							$ledgerValidationResult = $this->ledgerUpdate($tRequest,$ledgerId,$clientId,$ledgerData);
							if(strcmp($ledgerValidationResult,$msgArray['200'])!=0)
							{
								return $ledgerValidationResult;
							}
						}
						else
						{
							//client insert and ledger insert
							$result = $this->ledgerValidationOfInsertion($tRequest['company_id'],$tRequest['client_name'],$tRequest['contact_no']);
							if(is_array($result))
							{
								//client insertion
								$clientResult = $this->clientInsertion($tRequest);
								if(strcmp($clientResult,$msgArray['content'])==0)
								{
									return $clientResult;
								}
								$clientId = json_decode($clientResult)->clientId;
								$ledgerInsertionResult = $this->ledgerInsertion($tRequest,$clientId,$tRequest['invoice_number'],$tRequest['company_id']);
								// ledger insertion (|| $processedData[0][0]=='[' error while validation error occur)
								if(strcmp($msgArray['500'],$ledgerInsertionResult)==0 || strcmp($msgArray['content'],$ledgerInsertionResult)==0)
								{
									return $ledgerInsertionResult;
								}
								$ledgerId = json_decode($ledgerInsertionResult)[0]->ledger_id;
							}
							else
							{
								return $result;
							}
						}
					}
				}
			}
			else
			{
				//data is not valid...return validation error message
				return $status;
			}
		}
		$paymentMode = $tRequest['payment_mode'];
		if(strcmp($paymentMode,$constantArray['credit'])==0)
		{
			if($tRequest['total']!=$tRequest['advance'])
			{
				$ledgerResult = $ledgerModel->getLedgerId($tRequest['company_id'],$constantArray['cashLedger']);
				if(is_array(json_decode($ledgerResult)))
				{
					$paymentLedgerId = json_decode($ledgerResult)[0]->ledger_id;
				}
			}
			else
			{
				return $msgArray['paymentMode'];
			}
		}
		else
		{
			if (strcmp($paymentMode,$constantArray['cashLedger'])==0)
			{
				$ledgerResult = $ledgerModel->getLedgerId($tRequest['company_id'],$paymentMode);
				if(is_array(json_decode($ledgerResult)))
				{
					$paymentLedgerId = json_decode($ledgerResult)[0]->ledger_id;
				}
			}
			else{
				$paymentLedgerId = $tRequest['bank_ledger_id'];
			}
		}

		// get jf_id
		$journalController = new JournalController(new Container());
		$journalMethod=$constantArray['getMethod'];
		$journalPath=$constantArray['journalUrl'];
		$journalDataArray = array();
		$journalJfIdRequest = Request::create($journalPath,$journalMethod,$journalDataArray);
		$jfId = $journalController->getData($journalJfIdRequest);
		$jsonDecodedJfId = json_decode($jfId)->nextValue;
		//get general ledger array data
		$generalLedgerData = $ledgerModel->getLedger($tRequest['company_id']);
		$generalLedgerArray = json_decode($generalLedgerData);
		$salesTypeEnum = new SalesTypeEnum();
		$salesTypeEnumArray = $salesTypeEnum->enumArrays();
		// if(strcmp($request->header()['salestype'][0],$salesTypeEnumArray['retailSales'])==0)
		// {
			// get ledger-id of retail_sales as per given company_id
			// $ledgerIdData = $ledgerModel->getLedgerId($tRequest['company_id'],$request->header()['salestype'][0]);
			// $decodedLedgerId = json_decode($ledgerIdData);
		// }
		// else
		// {
			//get ledger-id of whole sales as per given company_id
			$ledgerIdData = $ledgerModel->getLedgerId($tRequest['company_id'],$salesTypeEnumArray['wholesales']);
			$decodedLedgerId = json_decode($ledgerIdData);
		// }
		//get the company details from database
		$companyDetail = new CompanyDetail();
		$companyDetails = $companyDetail->getCompanyDetails($tRequest['company_id']);
		//convert total to no-of decimal point
		$tRequest['total'] = number_format($tRequest['total'],$companyDetails['noOfDecimalPoints'],'.','');	
		$tRequest['advance'] = number_format($tRequest['advance'],$companyDetails['noOfDecimalPoints'],'.','');	

		$ledgerTaxAcId = $generalLedgerArray[0][0]->ledger_id;
		$ledgerSaleAcId = $decodedLedgerId[0]->ledger_id;
		$ledgerDiscountAcId = $generalLedgerArray[1][0]->ledger_id;
		
		$amountTypeEnum = new AmountTypeEnum();
		$amountTypeArray = $amountTypeEnum->enumArrays();
		$ledgerAmount = $tRequest['total']-$tRequest['advance'];		
		$discountTotal=0;
		for($discountArray=0;$discountArray<count($tRequest[0]);$discountArray++)
		{
			if(strcmp($tRequest[0][$discountArray]['discountType'],$constantArray['Flatdiscount'])==0)
			{
				$discount = $tRequest[0][$discountArray]['discount'];
			}
			else
			{
				$discount = ($tRequest[0][$discountArray]['discount']/100)*$tRequest[0][$discountArray]['price'];
			}	
			$discountTotal = $discount+$discountTotal;
		}
		
		// if(strcmp($tRequest['total_discounttype'],'flat')==0)
		// {
			// $totalDiscount = $tRequest['total_discount'];
		// }
		// else
		// {
			// $totalDiscount = ($tRequest['total_discount']/100)*$tRequest['total'];
		// }
		// $discountTotal = $discountTotal+$totalDiscount;
		$totalSaleAmount = $discountTotal+$tRequest['total'];
		$totalDebitAmount = $tRequest['tax']+$tRequest['total'];
		// New Ledger / Journal Calculation
		$dataArray = [];
		$transactionType = [];
		$transactionType[0] = $constantArray['sales'];
		if ($tRequest['total'] == $tRequest['advance']) {
			$dataArray[0][0] = [
				"amount"=>$tRequest['advance'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$paymentLedgerId
			];
		}else{
			$dataArray[0][0] = [
				"amount"=>$tRequest['total'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$ledgerId
			];
			//  Oth Array is for Sales Txn 1st Array is for Cash or Advance Payment done by Client on Bill Generation
			if ($request->input()['advance']!="" && $tRequest['advance']!=0) {
				$transactionType[1] = $constantArray['receiptType'];
				$dataArray[1][0] = [
				"amount"=>$tRequest['advance'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$paymentLedgerId
				];
				$dataArray[1][1] = [
					"amount"=>$tRequest['advance'],
					"amountType"=>$amountTypeArray['creditType'],
					"ledgerId"=>$ledgerId
				];
			}
		}
		if ($discountTotal != 0) {
			$dataArray[0][] = [
				"amount"=>$discountTotal,
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$ledgerDiscountAcId
			];
		}
		if ($tRequest['tax'] != 0) {
			$dataArray[0][] = [
				"amount"=>$tRequest['total']+$discountTotal-$tRequest['tax'],
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$ledgerSaleAcId
			];
			$dataArray[0][] = [
				"amount"=>$tRequest['tax'],
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$ledgerTaxAcId
			];
		}else{
			$dataArray[0][] = [
				"amount"=>$tRequest['total']+$discountTotal,
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$ledgerSaleAcId
			];
		}

		// Staff Commission Calculations
		/* 
		* $tRequest[0] Array of inventory data
		* $tRequest['user_id'] user's id
		* $tRequest['company_id'] company's id
		* Get commission data from user id
		*CommissionService  $msgArray commissionLedgerArray
		*/
		if (isset($tRequest['user_id']) && $tRequest['user_id'] && isset($staffLedgerId)) {
			$commissionService = new CommissionService();
			$commissionArrayRank = count($dataArray);
			$commissionDataJson = $commissionService->getCommissionData($tRequest['user_id']);
			if (strcmp($commissionDataJson,$msgArray['404'])!=0) {
				$commissionDataArray = (array)json_decode($commissionDataJson);
				if ($commissionDataArray['commissionStatus'] != 'off') {
					if ($commissionDataArray['commissionRate'] != '0' || $commissionDataArray['commissionType'] == 'itemWise') {
						$productService = new productService();
						$commissionRateType = $commissionDataArray['commissionRateType'];
						$commissionCalcOn = $commissionDataArray['commissionCalcOn'];
						if ($commissionDataArray['commissionType'] == 'general') {
							$commissionAmount = 0;
							foreach ($tRequest[0] as $commissionProduct) {
								if ($commissionRateType == 'flat') {
									$commissionAmount += (float)$commissionDataArray['commissionRate'] * (float)$commissionProduct['qty'];
								}else{
									$productDataJson = $productService->getProductData($commissionProduct['productId']);
									if (strcmp($productDataJson, $msgArray['404']) != 0) {
										$productDataArray = json_decode($productDataJson);
										if ($commissionCalcOn == 'MRP') {
											$commissionAmount += (float)$productDataArray['mrp'] * (float)$commissionProduct['qty'] * (float)$commissionDataArray['commissionRate'] / 100;
										}else{
											$commissionAmount += (float)$commissionProduct['price'] * (float)$commissionProduct['qty'] * (float)$commissionDataArray['commissionRate'] / 100;
										}
									}
								}
							}
							// For General commission Ends here
							$dataArray[$commissionArrayRank][0] = array(
								'amount' => (string)round($commissionAmount,2),
								'amountType' => $amountTypeArray['creditType'],
								'ledgerId' => $staffLedgerId,
							);
						}elseif ($commissionDataArray['commissionType'] == 'brandWise') {
							$commissionAmount = 0;
							$brandCommissionData = (array)json_decode($commissionDataArray['commissionFor']);
							foreach ($tRequest[0] as $commissionProduct) {
								if ($commissionRateType == 'flat') {
									$commissionAmount += (float)$commissionDataArray['commissionRate'] * (float)$commissionProduct['qty'];
								}else{
									$productDataJson = $productService->getProductData($commissionProduct['productId']);
									if (strcmp($productDataJson, $msgArray['404']) != 0) {
										$productDataArray = (array)json_decode($productDataJson);
										if (isset($productDataArray['productGroupId']) 
										&& isset($brandCommissionData[$productDataArray['productGroupId']])
										&& $brandCommissionData[$productDataArray['productGroupId']] ) 
										{
											if ($commissionCalcOn == 'MRP') {
												$commissionAmount += (float)$productDataArray['mrp'] * (float)$commissionProduct['qty'] * (float)$commissionDataArray['commissionRate'] / 100;
											}else{
												$commissionAmount += (float)$commissionProduct['price'] * (float)$commissionProduct['qty'] * (float)$commissionDataArray['commissionRate'] / 100;
											}
										}
									}
								}
							}
							
							$dataArray[$commissionArrayRank][0] = array(
								'amount' => (string)round($commissionAmount,2),
								'amountType' => $amountTypeArray['creditType'],
								'ledgerId' => $staffLedgerId,
							);
						}elseif ($commissionDataArray['commissionType'] == 'categoryWise') {
							$commissionAmount = 0;
							$categoryCommissionData = (array)json_decode($commissionDataArray['commissionFor']);
							foreach ($tRequest[0] as $commissionProduct) {
								if ($commissionRateType == 'flat') {
									$commissionAmount += (float)$commissionDataArray['commissionRate'] * (float)$commissionProduct['qty'];
								}else{
									$productDataJson = $productService->getProductData($brandWiseCommission['productId']);
									if (strcmp($productDataJson, $msgArray['404']) != 0) {
										$productDataArray = (array)json_decode($productDataJson);
										if (isset($productDataArray['productCategoryId']) 
										&& isset($categoryCommissionData[$productDataArray['productCategoryId']])
										&& $categoryCommissionData[$productDataArray['productCategoryId']] ) 
										{
											if ($commissionCalcOn == 'MRP') {
												$commissionAmount += (float)$productDataArray['mrp'] * (float)$commissionProduct['qty'] * (float)$commissionDataArray['commissionRate'] / 100;
											}else{
												$commissionAmount += (float)$commissionProduct['price'] * (float)$commissionProduct['qty'] * (float)$commissionDataArray['commissionRate'] / 100;
											}
										}
									}
								}
							}
							$dataArray[$commissionArrayRank][0] = array(
								'amount' => (string)round($commissionAmount,2),
								'amountType' => $amountTypeArray['creditType'],
								'ledgerId' => $staffLedgerId,
							);
						}elseif ($commissionDataArray['commissionType'] == 'itemWise') {
							// Itemwise Commission calculation
							$commissionAmount = 0;
							$commissionAmount = $this->itemWiseCommissionCalc($tRequest[0],$tRequest['company_id']);

							$dataArray[$commissionArrayRank][0] = array(
								'amount' => (string)round($commissionAmount,2),
								'amountType' => $amountTypeArray['creditType'],
								'ledgerId' => $staffLedgerId,
							);
							
						}
						if (isset($dataArray[$commissionArrayRank])
						 && isset($dataArray[$commissionArrayRank][0]['amount'])
						 && $dataArray[$commissionArrayRank][0]['amount'] > 0) {
							$commissionArrayConstant = new LedgerArray();
							$commissionLedgerArray = $commissionArrayConstant->commissionLedgerArray();
							if (is_array($commissionLedgerArray) && count($commissionLedgerArray)) {
								$ledgerModel = new LedgerModel();
								$commissionExpenseLedgerJson = $ledgerModel->getLedgerId($tRequest['company_id'], $commissionLedgerArray[0]);
								if ($commissionExpenseLedgerJson == $msgArray['404']) {
									$commissionLedgerStatus = $this->insertCommissionLedger($tRequest['company_id'], $commissionLedgerArray[0]);
									if(strcmp($commissionLedgerStatus,$msgArray['content'])==0 || strcmp($commissionLedgerStatus,$msgArray['404'])==0){
										return $commissionLedgerStatus;
									}

									$commissionExpenseLedgerId = json_decode($commissionLedgerStatus)[0]->ledger_id;

								}else{
									$commissionExpenseLedgerId = json_decode($commissionExpenseLedgerJson)[0]->ledger_id;
								}
								$transactionType[$commissionArrayRank] = $constantArray['paymentType'];
								$dataArray[$commissionArrayRank][1] = array(
									'amount' => (string)$dataArray[$commissionArrayRank][0]['amount'],
									'amountType' => $amountTypeArray['debitType'],
									'ledgerId' => $commissionExpenseLedgerId,
								);
							}
						}
					}
				}
			}
		}
		// Staff Commission Calculations Ends

		for ($multiJournalCreate=0; $multiJournalCreate < count($transactionType); $multiJournalCreate++) {
				//make data array for journal sale entry
			$journalArray = array();
			$journalArray= array(
				'jfId' => $jsonDecodedJfId,
				'data' => array(
				),
				'entryDate' => $tRequest['entry_date'],
				'companyId' => $tRequest['company_id'],
				'inventory' => array(
				),
				'transactionDate'=> $tRequest['entry_date'],
				'tax'=> $tRequest['tax'],
				'invoiceNumber'=>$tRequest['invoice_number']
			);
			$journalArray['inventory']=$tRequest[0];
			$method=$constantArray['postMethod'];
			$path=$constantArray['journalUrl']; 
			$journalArray['data']=$dataArray[$multiJournalCreate];
			$journalRequest = Request::create($path,$method,$journalArray);
			$journalRequest->headers->set('type',$transactionType[$multiJournalCreate]);
			$processedData = $journalController->store($journalRequest);
			if (strcmp($processedData, $msgArray['200']) != 0) {
				return $processedData;
			}
		}
		

		if(strcmp($processedData,$msgArray['200'])==0)
		{
			$productArray = array();
			$productArray['invoiceNumber']=$tRequest['invoice_number'];
			$productArray['transactionType']=$constantArray['journalOutward'];
			$productArray['companyId']=$tRequest['company_id'];
			$tInventoryArray = array();
			$itemizeBatch = array();
			// for($trimData=0;$trimData<count($request->input()['inventory']);$trimData++)
			// {
			// 	$tInventoryArray[$trimData] = array();

			// 	$tInventoryArray[$trimData][5] = array_key_exists('color', $request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['color']) : "XX";
			// 	$tInventoryArray[$trimData][6] = array_key_exists('frameNo', $request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['frameNo']) : "";
			// 	$tInventoryArray[$trimData][7] = array_key_exists('size', $request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['size']) : "ZZ";
			// 	$tInventoryArray[$trimData][8] = array_key_exists("cgstPercentage",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['cgstPercentage']):0;
			// 	$tInventoryArray[$trimData][9] = array_key_exists("cgstAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['cgstAmount']):0;
			// 	$tInventoryArray[$trimData][10] = array_key_exists("sgstPercentage",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['sgstPercentage']):0;
			// 	$tInventoryArray[$trimData][11] = array_key_exists("sgstAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['sgstAmount']):0;
			// 	$tInventoryArray[$trimData][12] = array_key_exists("igstPercentage",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['igstPercentage']):0;
			// 	$tInventoryArray[$trimData][13] = array_key_exists("igstAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['igstAmount']):0;
			// 	$tInventoryArray[$trimData][14] = array_key_exists("cessAmount",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['cessAmount']):0;
			// 	$tInventoryArray[$trimData][15] = array_key_exists("realQtyData",$request->input()['inventory'][$trimData]) ? trim($request->input()['inventory'][$trimData]['realQtyData']):0;

			// 	// insertion for itemize (IMEI/Serial) purchase bill
			// 	if (isset($request->input()['inventory'][$trimData]['itemizeDetail'])) {
			// 		$itemizeArray = json_decode($request->input()['inventory'][$trimData]['itemizeDetail']);
			// 		if (count($itemizeArray) > 0) {
			// 			$itemizeJsonArray = [];
			// 			$itemizeProduct =  $request->input()['inventory'][$trimData]['productId'];
			// 			foreach ($itemizeArray as $serialArray) {
			// 				$itemizeJsonArray[] = [
			// 					'imei_no' => trim($serialArray->imei_no),
			// 					'barcode_no' => trim($serialArray->barcode_no),
			// 					'qty' => trim($serialArray->qty)
			// 				];
			// 				$itemizeBatch[] = [
			// 					'product_id' => trim($itemizeProduct),
			// 					'imei_no' => trim($serialArray->imei_no),
			// 					'barcode_no' => trim($serialArray->barcode_no),
			// 					'qty' => trim($serialArray->qty)*-1,
			// 					'jfId' => $jsonDecodedJfId,
			// 					'purchase_bill_no' => $productArray['invoiceNumber']
			// 				];
			// 			}
			// 			$request->input()['inventory'][$trimData]['itemizeDetail'] = $itemizeJsonArray;
			// 		}
			// 	}
			// 	// end of insertion of itemize (IMEI/Serial)

			// 	array_push($request->input()['inventory'][$trimData],$tInventoryArray[$trimData]);
			// }

			$tempInvArray = $request->input()['inventory'];
			foreach ($tempInvArray as $singleInvKey => $singleInvArray) {
				// insertion for itemize (IMEI/Serial) purchase bill
				if (isset($singleInvArray['itemizeDetail'])) {
					$itemizeArray = json_decode($singleInvArray['itemizeDetail']);
					if (count($itemizeArray) > 0) {
						$itemizeJsonArray = [];
						$itemizeProduct =  $singleInvArray['productId'];
						foreach ($itemizeArray as $serialArray) {
							$itemizeJsonArray[] = [
								'imei_no' => trim($serialArray->imei_no),
								'barcode_no' => trim($serialArray->barcode_no),
								'qty' => trim($serialArray->qty)
							];
							$itemizeBatch[] = [
								'product_id' => trim($itemizeProduct),
								'imei_no' => trim($serialArray->imei_no),
								'barcode_no' => trim($serialArray->barcode_no),
								'qty' => trim($serialArray->qty)*-1,
								'jfId' => $jsonDecodedJfId,
								'sales_bill_no' => $productArray['invoiceNumber']
							];
						}
						$singleInvArray['itemizeDetail'] = $itemizeJsonArray;
					}
				}
				$productArray['inventory'][$singleInvKey] = $singleInvArray;
				// end of insertion of itemize (IMEI/Serial)
			}
			$documentPath = $constantArray['billDocumentUrl'];
			
			if(in_array(true,$request->file()) || array_key_exists('scanFile',$request->input()))
			{
				$documentController =new DocumentController(new Container());
				$processedData = $documentController->insertUpdate($request,$documentPath);
				if(is_array($processedData))
				{
					$docFlag=1;
				}
				else
				{
					return $processedData;
				}
			}
			if (!empty($itemizeBatch) && count($itemizeBatch) > 0) {
				$productService = new ProductService();
				$itemizeBatchInsertion = $productService->insertInOutwardItemizeData($itemizeBatch);
				if (strcmp($itemizeBatchInsertion, $msgArray['200']) != 0) {
					return $itemizeBatchInsertion;
				}
			}
			//entry date/service date conversion
			$transformEntryDate = Carbon\Carbon::createFromFormat('d-m-Y', $tRequest['entry_date'])->format('Y-m-d');
			$transformServiceDate = $tRequest['service_date']=="" ? "0000-00-00":
													Carbon\Carbon::createFromFormat('d-m-Y', $tRequest['service_date'])->format('Y-m-d');
			$billPersistable = new BillPersistable();
			$billPersistable->setProductArray(json_encode($productArray));
			$billPersistable->setPaymentMode($tRequest['payment_mode']);
			$billPersistable->setBankName($tRequest['bank_name']);
			$billPersistable->setInvoiceNumber($tRequest['invoice_number']);
			$billPersistable->setJobCardNumber($tRequest['job_card_number']);
			$billPersistable->setCheckNumber($tRequest['check_number']);
			$billPersistable->setTotal($tRequest['total']);
			$billPersistable->setExtraCharge($tRequest['extra_charge']);
			$billPersistable->setTax($tRequest['tax']);
			$billPersistable->setGrandTotal($tRequest['grand_total']);
			$billPersistable->setAdvance($tRequest['advance']);
			$billPersistable->setBalance($tRequest['balance']);
			$billPersistable->setRemark($tRequest['remark']);
			$billPersistable->setEntryDate($transformEntryDate);
			$billPersistable->setServiceDate($transformServiceDate);
			$billPersistable->setClientId($clientId);
			$billPersistable->setCompanyId($tRequest['company_id']);
			$billPersistable->setBranchId($tRequest['branch_id']);
			$billPersistable->setTotalDiscounttype($tRequest['total_discounttype']);
			$billPersistable->setTotalDiscount($tRequest['total_discount']);
			$billPersistable->setTotalCgstPercentage($tRequest['totalCgstPercentage']);
			$billPersistable->setTotalSgstPercentage($tRequest['totalSgstPercentage']);
			$billPersistable->setTotalIgstPercentage($tRequest['totalIgstPercentage']);
			$billPersistable->setPoNumber($tRequest['po_number']);
			$billPersistable->setUserId($tRequest['user_id']);
			$billPersistable->setExpense($tRequest['expense']);
			$billPersistable->setCreatedBy($tRequest['created_by']);
			$billPersistable->setJfId($jsonDecodedJfId);
			
			// if(strcmp($request->header()['salestype'][0],$salesTypeEnumArray['retailSales'])==0 || strcmp($request->header()['salestype'][0],$salesTypeEnumArray['wholesales'])==0)
			// {
				$billPersistable->setSalesType($salesTypeEnumArray['wholesales']);
			// }
			// else
			// {
				// return $msgArray['content'];
			// }
			if($docFlag==1)
			{
				$array1 = array();
				array_push($processedData,$billPersistable);
				return $processedData;	
			}
			else
			{
				return $billPersistable;
			}
		}
		else
		{
			return $processedData;
		}
	}
	
	/**
     * get the fromDate-toDate data and set into the persistable object
     * $param Request object [Request $request]
     * @return Bill Persistable object
     */	
	public function getPersistableData($requestHeader)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();

		//trim an input 
		$billTransformer = new BillTransformer();
		$tRequest = $billTransformer->trimFromToDateData($requestHeader);
		
		if(is_array($tRequest))
		{
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$tRequest['fromDate']))
			{
				return "from-date is not valid";
			}
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$tRequest['toDate']))
			{
				return "to-date is not valid";
			}
			// set data in persistable object
			$billPersistable = new BillPersistable();
			$billPersistable->setSalesType($tRequest['salesType']);
			$billPersistable->setFromDate($tRequest['fromDate']);
			if ($tRequest['isSalesOrder'] != '' && $tRequest['isSalesOrder'] != 'not') {
				$billPersistable->setIsSalesOrder($tRequest['isSalesOrder']);
			}
			$billPersistable->setToDate($tRequest['toDate']);
			if($tRequest['branchId'] != '' && $tRequest['branchId'] != 0){
				$billPersistable->setBranchId($tRequest['branchId']);
			}
			return $billPersistable;
		}
		else
		{
			return $tRequest;
		}
	}
	
	/**
     * get request data and set into the persistable object
     * $param Request object [Request $request] and sale-id
     * @return Bill Persistable object
     */	
	public function getPersistablePaymentData(Request $request,$saleId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		
		$amountTypeEnum = new AmountTypeEnum();
		$amountTypeArray = $amountTypeEnum->enumArrays();
		
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//trim an input 
		$billTransformer = new BillTransformer();
		$tRequest = $billTransformer->trimPaymentData($request);
		if(is_array($tRequest))
		{
			//validate entry-date
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$tRequest['entry_date']))
			{
				return "entry-date is not valid";
			}
			//get bill data as per given sale-id(get company id)
			$billModel = new BillModel();
			$saleIdData = $billModel->getSaleIdData($saleId);
			
			if(strcmp($saleIdData,$msgArray['404'])!=0)
			{				
				$decodedBillData = json_decode($saleIdData);
				$companyId = $decodedBillData[0]->company_id;
				
				//get latest jf-id
				$journalController = new JournalController(new Container());
				$journalMethod=$constantArray['getMethod'];
				$journalPath=$constantArray['journalUrl'];
				$journalDataArray = array();
			

				$journalJfIdRequest = Request::create($journalPath,$journalMethod,$journalDataArray);
				$jfIdData = $journalController->getData($journalJfIdRequest);
				$ledgerModel = new LedgerModel();
			
				if (strcmp($tRequest['payment_mode'],$constantArray['cashLedger'])==0)
				{
					$ledgerResult = $ledgerModel->getLedgerId($companyId,$tRequest['payment_mode']);
					if(strcmp($ledgerResult,$msgArray['404'])==0)
					{					
						return $msgArray['404'];
					}

					if(is_array(json_decode($ledgerResult)))
					{
						$decodedLedgerId = json_decode($ledgerResult)[0]->ledger_id;
					}
				}
				else{
					$decodedLedgerId = $tRequest['bank_ledger_id'];
				}

				// $ledgerModel = new LedgerModel();
				// $ledgerData = $ledgerModel->getLedgerId($companyId,$tRequest['payment_mode']);		
				// $decodedLedgerId = json_decode($ledgerData)[0]->ledger_id;				
				// if(strcmp($ledgerData,$msgArray['404'])==0)
				// {					
				// 	return $msgArray['404'];
				// }			

				if(strcmp($jfIdData,$msgArray['404'])!=0)
				{
					$nextJfId = json_decode($jfIdData)->nextValue;

					//process of making a journal entry 
					if(strcmp($tRequest['payment_transaction'],$constantArray['paymentType'])==0)
					{
						//get personal a/c ledgerId
						$ledgerPersonalIdData = $ledgerModel->getPersonalAccLedgerId($companyId,$decodedBillData[0]->jf_id);
						if(strcmp($ledgerPersonalIdData,$msgArray['404'])==0)
						{
							return $msgArray['404'];
						}
						if($decodedBillData[0]->balance<$tRequest['amount'])
						{
							return $msgArray['content'];
						}

						$decodedPersonalAccData = json_decode($ledgerPersonalIdData)[0]->ledger_id;
						$dataArray = array();
						$journalArray = array();
						$dataArray[0]=array(
							"amount"=>$tRequest['amount'],
							"amountType"=>$amountTypeArray['debitType'],
							"ledgerId"=>$decodedLedgerId,
						);
						$dataArray[1]=array(
							"amount"=>$tRequest['amount'],
							"amountType"=>$amountTypeArray['creditType'],
							"ledgerId"=>$decodedPersonalAccData,
						);
						$journalArray= array(
							'jfId' => $nextJfId,
							'data' => array(
							),
							'entryDate' => $request->input()['entryDate'],
							'companyId' => $companyId
						);
						$journalArray['data']=$dataArray;
						$method=$constantArray['postMethod'];
						$path=$constantArray['journalUrl'];
						
						$journalRequest = Request::create($path,$method,$journalArray);
						$journalRequest->headers->set('type',$constantArray['receiptType']);
						$processedData = $journalController->store($journalRequest);
						if(strcmp($processedData,$msgArray['200'])!=0)
						{
							return $processedData;
						}

						$billArray = array();
						$billArray['sale_id'] = $saleId;
						$billArray['payment_mode'] = $tRequest['payment_mode'];
						$billArray['advance'] = $decodedBillData[0]->advance+$tRequest['amount'];
						$billArray['balance'] = $decodedBillData[0]->balance-$tRequest['amount'];
						$billArray['refund'] = 0;
						$billArray['entry_date'] = $tRequest['entry_date'];
						$billArray['payment_transaction'] = $constantArray['receiptType'];

						if(strcmp($tRequest['payment_mode'],"cash")!=0)
						{
							$billArray['bank_name'] = $tRequest['bank_name'];
							$billArray['check_number'] = $tRequest['check_number'];
							$billArray['bank_ledger_id'] = $tRequest['bank_ledger_id'];
						}

						// set data in persistable object
						$billPersistable = new BillPersistable();
						$billPersistable->setBillArray(json_encode($billArray));
						return $billPersistable;
					}
					else if(strcmp($tRequest['payment_transaction'],$constantArray['refundType'])==0)
					{						
						// type refund
						//get salesReturn ledgerId
						$salesLedgerData = $ledgerModel->getLedgerId($companyId,$constantArray['salesReturnType']);
						$decodedSalesLedgerId = json_decode($salesLedgerData)[0]->ledger_id;
						if(strcmp($salesLedgerData,$msgArray['404'])==0)
						{
							return $msgArray['404'];
						}
						if($decodedBillData[0]->advance<$tRequest['amount'])
						{
							return $msgArray['content'];
						}
						$dataArray = array();
						$journalArray = array();
						$dataArray[0]=array(
							"amount"=>$tRequest['amount'],
							"amountType"=>$amountTypeArray['debitType'],
							"ledgerId"=>$decodedSalesLedgerId,
						);
						$dataArray[1]=array(
							"amount"=>$tRequest['amount'],
							"amountType"=>$amountTypeArray['creditType'],
							"ledgerId"=>$decodedLedgerId,
						);						
						$journalArray= array(
							'jfId' => $nextJfId,
							'data' => array(
							),
							'entryDate' => $request->input()['entryDate'],
							'companyId' => $companyId
						);
						$journalArray['data']=$dataArray;
						$method=$constantArray['postMethod'];
						$path=$constantArray['journalUrl'];
						
						$journalRequest = Request::create($path,$method,$journalArray);
						$journalRequest->headers->set('type',$constantArray['paymentType']);
						$processedData = $journalController->store($journalRequest);
						if(strcmp($processedData,$msgArray['200'])!=0)
						{
							return $processedData;
						}
						$billArray = array();
						$billArray['sale_id'] = $saleId;
						$billArray['payment_mode'] = $tRequest['payment_mode'];
						$billArray['refund'] = $tRequest['amount']+$decodedBillData[0]->refund;
						$billArray['advance'] = $decodedBillData[0]->advance;
						$billArray['balance'] = $decodedBillData[0]->balance+$tRequest['amount'];
						$billArray['entry_date'] = $tRequest['entry_date'];
						$billArray['payment_transaction'] = $tRequest['payment_transaction'];
						
						if(strcmp($tRequest['payment_mode'],"cash")!=0)
						{
							$billArray['bank_name'] = $tRequest['bank_name'];
							$billArray['check_number'] = $tRequest['check_number'];
							$billArray['bank_ledger_id'] = $tRequest['bank_ledger_id'];
						}
												
						// set data in persistable object
						$billPersistable = new BillPersistable();
						$billPersistable->setBillArray(json_encode($billArray));
						return $billPersistable;
					}
					else
					{
						return $msgArray['content'];
					}
				}
				else
				{
					return $jfIdData;
				}
			}
			else
			{
				return $saleIdData;
			}
		}
		else
		{
			return $tRequest;
		}
	}
	/**
     * get request data and set into the persistable object
     * $param Request object [Request $request] and sale-id
     * @return Bill Persistable object
     */	
	public function getPersistableStatusData(Request $request,$saleData)
	{
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		
		$amountTypeEnum = new AmountTypeEnum();
		$amountTypeArray = $amountTypeEnum->enumArrays();
		
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//trim an input 
		$billTransformer = new BillTransformer();
		$tRequest = $billTransformer->trimStatusData($request,$saleData);
		return $tRequest;
	}
	/**
     * get request data & sale-id and set into the persistable object
     * $param Request object [Request $request] and sale-id and billdata
     * @return Bill Persistable object/error message
     */
	public function createPersistableChange(Request $request,$saleId,$billData)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		//trim bill data
		$billTransformer = new BillTransformer();
		$billTrimData = $billTransformer->trimBillUpdateData($request,$saleId);

		if(!is_array($billTrimData))
		{
			if(strcmp($billTrimData,$msgArray['content'])==0)
			{
				return $msgArray['content'];
			}
		}
		$ledgerModel = new LedgerModel();
		$clientArray = new ClientArray();
		$clientArrayData = $clientArray->getClientArrayDataForBill();
		$clientData = array();
		foreach($clientArrayData as $key => $value)
		{
			if(array_key_exists($key,$billTrimData))
			{
				$clientData[$value] = $billTrimData[$key];
			}
		}	

		$contactFlag=0;
		$clientModel = new ClientModel();
		$ledgerModel = new LedgerModel();
		//get clientId as per given saleId
		$billData = json_decode($billData);
		$journalController = new JournalController(new Container());
		
		//get client-data as per given client-id for getting client contact_no
		$clientIdData = $clientModel->getData($billData[0]->client_id);
		$decodedClientData = (json_decode($clientIdData));
		$contactNo = $decodedClientData->clientData[0]->contact_no;
		
		$ledgerData = $ledgerModel->getDataAsPerContactNo($billData[0]->company_id,$contactNo);
		if(is_array(json_decode($ledgerData)))
		{
			$ledgerId = json_decode($ledgerData)[0]->ledger_id;
		}

		if(count($clientData)!=0)
		{
			//check contact_no exist or not
			if(array_key_exists("contact_no",$clientData))
			{
				$contactNo = $clientData['contact_no'];
			}
			//get client-data as per contact-no
			$clientDataAsPerContactNo = $clientModel->getClientData($contactNo);
			if(strcmp($clientDataAsPerContactNo,$msgArray['200'])!=0)
			{
				$clientDecodedData = json_decode($clientDataAsPerContactNo);
				//contact-no already exist...update client-data ..check ledger
				//update client-data and check ledger
				$ledgerData = $ledgerModel->getDataAsPerContactNo($billData[0]->company_id,$contactNo);
				if(is_array(json_decode($ledgerData)))
				{
					//update client-ledger
					$ledgerId = json_decode($ledgerData)[0]->ledger_id;
					//update client-data
					$encodedClientData = $clientDecodedData->clientData;
					$clientId = $encodedClientData[0]->client_id;
					$clientUpdateResult = $this->clientUpdate($clientData,$clientId ,$clientDataAsPerContactNo);

					if(strcmp($clientUpdateResult,$msgArray['200'])!=0)
					{
						return $clientUpdateResult;
					}
					//update ledger-data
					$ledgerValidationResult = $this->ledgerUpdate($clientData,$ledgerId,$clientId,$ledgerData);
					if(strcmp($ledgerValidationResult,$msgArray['200'])!=0)
					{
						return $ledgerValidationResult;
					}
				}
				else
				{
					//ledger validation
					$result = $this->ledgerValidationOfInsertion($billData[0]->company_id,$clientData['client_name'],$contactNo);
					if(is_array($result))
					{
						//update client-data
						$encodedClientData = $clientDecodedData->clientData;
						$clientId = $encodedClientData[0]->client_id;
						$clientUpdateResult = $this->clientUpdate($clientData,$clientId,$clientDataAsPerContactNo);
						if(strcmp($clientUpdateResult,$msgArray['200'])!=0)
						{
							return $clientUpdateResult;
						}
						//ledger insertion
						$ledgerInsertionResult = $this->ledgerInsertion($clientData,$clientId,$billData[0]->invoice_number,$billData[0]->company_id);
						//ledger insertion (|| $processedData[0][0]=='[' error while validation error occur)
						if(strcmp($msgArray['500'],$ledgerInsertionResult)==0 || strcmp($msgArray['content'],$ledgerInsertionResult)==0)
						{
							return $ledgerInsertionResult;
						}
						$ledgerId = json_decode($ledgerInsertionResult)[0]->ledger_id;
					}
					else
					{
						return $result;
					}
				}
			}
			else
			{
				//client insertion and ledger check
				$ledgerData = $ledgerModel->getDataAsPerContactNo($billData[0]->company_id,$contactNo);
				if(is_array(json_decode($ledgerData)))
				{
					$ledgerId = json_decode($ledgerData)[0]->ledger_id;
					//client insert and ledger update
					//client insertion
					$clientResult = $this->clientInsertion($clientData);
					if(strcmp($clientResult,$msgArray['content'])==0)
					{
						return $clientResult;
					}
					$clientId = json_decode($clientResult)->clientId;
					//update ledger-data
					$ledgerValidationResult = $this->ledgerUpdate($clientData,$ledgerId,$clientId,$ledgerData);
					if(strcmp($ledgerValidationResult,$msgArray['200'])!=0)
					{
						return $ledgerValidationResult;
					}
				}
				else
				{
					//client insert and ledger insert
					//ledger validation
					$result = $this->ledgerValidationOfInsertion($billData[0]->company_id,$clientData['client_name'],$contactNo);
					if(is_array($result))
					{
						//client insertion
						$clientResult = $this->clientInsertion($clientData);
						if(strcmp($clientResult,$msgArray['content'])==0)
						{
							return $clientResult;
						}
						$clientId = json_decode($clientResult)->clientId;
						$ledgerInsertionResult = $this->ledgerInsertion($clientData,$clientId,$billData[0]->invoice_number,$billData[0]->company_id);
						
						//ledger insertion (|| $processedData[0][0]=='[' error while validation error occur)
						if(strcmp($msgArray['500'],$ledgerInsertionResult)==0 || strcmp($msgArray['content'],$ledgerInsertionResult)==0)
						{
							return $ledgerInsertionResult;
						}
						$ledgerId = json_decode($ledgerInsertionResult)[0]->ledger_id;
					}
					else
					{
						return $result;
					}
				}
			}
		}
		if(array_key_exists('inventory',$billTrimData))
		{
			if(array_key_exists('payment_mode',$billTrimData))
			{
				$paymentMode = $billTrimData['payment_mode'];
			}
			else
			{
				$paymentMode = $billData[0]->payment_mode;
			}
			if(strcmp($paymentMode,$constantArray['credit'])==0)
			{
				if($billTrimData['total']!=$billTrimData['advance'])
				{
					$ledgerResult = $ledgerModel->getLedgerId($billData[0]->company_id,$constantArray['cashLedger']);
					if(is_array(json_decode($ledgerResult)))
					{
						$paymentLedgerId = json_decode($ledgerResult)[0]->ledger_id;
					}
				}
				else
				{
					return $msgArray['paymentMode'];
				}
			}
			else
			{
				if (strcmp($paymentMode,$constantArray['cashLedger'])==0)
				{
					$ledgerResult = $ledgerModel->getLedgerId($billData[0]->company_id,$paymentMode);
					if(is_array(json_decode($ledgerResult)))
					{
						$paymentLedgerId = json_decode($ledgerResult)[0]->ledger_id;
					}
				} else{
					$paymentLedgerId =  $billTrimData['bank_ledger_id'];
				}
			}
			
			//get jf_id
			$journalMethod=$constantArray['getMethod'];
			$journalPath=$constantArray['journalUrl'];
			$journalDataArray = array();
			$journalJfIdRequest = Request::create($journalPath,$journalMethod,$journalDataArray);
			$jfId = $journalController->getData($journalJfIdRequest);
			$jsonDecodedJfId = json_decode($jfId)->nextValue;
			//get general ledger array data
			$generalLedgerData = $ledgerModel->getLedger($billData[0]->company_id);
			$generalLedgerArray = json_decode($generalLedgerData);
			$salesTypeEnum = new SalesTypeEnum();
			$salesTypeEnumArray = $salesTypeEnum->enumArrays();		
			if(strcmp($billData[0]->sales_type,$salesTypeEnumArray['retailSales'])==0)
			{
				//get ledger-id of retail_sales as per given company_id
				$ledgerIdData = $ledgerModel->getLedgerId($billData[0]->company_id,$salesTypeEnumArray['retailSales']);
				$decodedLedgerId = json_decode($ledgerIdData);
			}
			else if(strcmp($billData[0]->sales_type,$salesTypeEnumArray['wholesales'])==0)
			{
				//get ledger-id of whole sales as per given company_id
				$ledgerIdData = $ledgerModel->getLedgerId($billData[0]->company_id,$salesTypeEnumArray['wholesales']);
				$decodedLedgerId = json_decode($ledgerIdData);
			}
			$ledgerTaxAcId = $generalLedgerArray[0][0]->ledger_id;
			$ledgerSaleAcId = $decodedLedgerId[0]->ledger_id;
			$ledgerDiscountAcId = $generalLedgerArray[1][0]->ledger_id;
			// if(count($decodedLedgerData)!=0)
			// {
				// $ledgerId = $decodedLedgerData[0]->ledger_id;
			// }
			$amountTypeEnum = new AmountTypeEnum();
			$amountTypeArray = $amountTypeEnum->enumArrays();
			$ledgerAmount = $billTrimData['total']-$billTrimData['advance'];		
			$discountTotal=0;
			$inventoryArray = $billTrimData['inventory'];	
			for($discountArray=0;$discountArray<count($inventoryArray);$discountArray++)
			{
				if(strcmp($inventoryArray[$discountArray]['discountType'],"flat")==0)
				{
					$discount = $inventoryArray[$discountArray]['discount'];
				}
				else
				{
					$discount = ($inventoryArray[$discountArray]['discount']/100)*$inventoryArray[$discountArray]['price'];
				}	
				$discountTotal = $discount+$discountTotal;
			}
			// if(strcmp($billTrimData['total_discounttype'],'flat')==0)
			// {
				// $discountTotal = $billTrimData['total_discount'];
			// }
			// else
			// {
				// $discountTotal = ($billTrimData['total_discount']/100)*$billTrimData['total'];
			// }
			// $discountTotal = $discountTotal+$discountTotal1;

			$totalSaleAmount = $discountTotal+$billTrimData['total'];
			$totalDebitAmount = $billTrimData['tax']+$billTrimData['total'];

			$transactionType[0] = $constantArray['sales'];
			if ($billTrimData['total'] == $billTrimData['advance']) {
				$dataArray[0][0] = [
					"amount"=>$billTrimData['advance'],
					"amountType"=>$amountTypeArray['debitType'],
					"ledgerId"=>$paymentLedgerId
				];
			}else{
				$dataArray[0][0] = [
					"amount"=>$billTrimData['total'],
					"amountType"=>$amountTypeArray['debitType'],
					"ledgerId"=>$ledgerId
				];
						//  Oth Array is for Sales Txn 1st Array is for Cash or Advance Payment done by Client on Bill Generation
				if ($request->input()['advance']!="" && $billTrimData['advance']!=0) {
					$transactionType[1] = $constantArray['receiptType'];
					$dataArray[1][0] = [
						"amount"=>$billTrimData['advance'],
						"amountType"=>$amountTypeArray['debitType'],
						"ledgerId"=>$paymentLedgerId
					];
					$dataArray[1][1] = [
						"amount"=>$billTrimData['advance'],
						"amountType"=>$amountTypeArray['creditType'],
						"ledgerId"=>$ledgerId
					];
				}
			}
			if ($discountTotal != 0) {
				$dataArray[0][] = [
					"amount"=>$discountTotal,
					"amountType"=>$amountTypeArray['debitType'],
					"ledgerId"=>$ledgerDiscountAcId
				];
			}
			if ($billTrimData['tax'] != 0) {
				$dataArray[0][] = [
					"amount"=>$billTrimData['total']+$discountTotal-$billTrimData['tax'],
					"amountType"=>$amountTypeArray['creditType'],
					"ledgerId"=>$ledgerSaleAcId
				];
				$dataArray[0][] = [
					"amount"=>$billTrimData['tax'],
					"amountType"=>$amountTypeArray['creditType'],
					"ledgerId"=>$ledgerTaxAcId
				];
			}else{
				$dataArray[0][] = [
					"amount"=>$billTrimData['total']+$discountTotal,
					"amountType"=>$amountTypeArray['creditType'],
					"ledgerId"=>$ledgerSaleAcId
				];
			}
			//make data array for journal sale entry
			$dataArrayCount = count($dataArray);
			for ($multiJournalCreate=0; $multiJournalCreate < $dataArrayCount; $multiJournalCreate++) {
				// make data array for journal sale entry
				$journalArray = array();
				$journalArray= array(
					'data' => array(
					),
					'inventory' => array(
					),
					'tax'=> $billTrimData['tax']
				);
				if(array_key_exists('entry_date',$billTrimData))
				{
					$journalArray['entryDate'] = $billTrimData['entry_date'];
				}else{
					$journalArray['entryDate'] = Carbon\Carbon::createFromFormat('Y-m-d', $billData[0]->entry_date)->format('d-m-Y');
				}
				if(array_key_exists('transaction_date',$billTrimData))
				{
					$journalArray['transactionDate'] = $billTrimData['transaction_date'];
				}else{
					$journalArray['transactionDate'] = Carbon\Carbon::createFromFormat('Y-m-d', $billData[0]->transaction_date)->format('d-m-Y');
				}
				
				if(array_key_exists('invoiceNumber',$billTrimData))
				{
					$journalArray['invoiceNumber'] = $billTrimData['invoice_number'];
				}else{
					$journalArray['invoiceNumber'] = $billData[0]->invoice_number;
				}
				$journalArray['data']=$dataArray[$multiJournalCreate];
				$journalArray['inventory']=$billTrimData['inventory'];

				$method=$constantArray['postMethod'];

				if($multiJournalCreate > 0)
				{
					$journalArray['jfId'] = $billData[0]->jf_id;
					$journalArray['companyId'] = $billData[0]->company_id;
					$path=$constantArray['journalUrl'];
					$journalRequest = Request::create($path,$method,$journalArray);
					$journalRequest->headers->set('type',$transactionType[$multiJournalCreate]);
					$processedData = $journalController->store($journalRequest);
					if(strcmp($processedData,$msgArray['200'])!=0)
					{
						return $processedData;
					}
				}
				else
				{
					$path=$constantArray['journalUrl'].'/'.$billData[0]->jf_id;
					$journalRequest = Request::create($path,$method,$journalArray);
					$journalRequest->headers->set('type',$transactionType[$multiJournalCreate]);
					$processedData = $journalController->update($journalRequest,$billData[0]->jf_id);
					if(strcmp($processedData,$msgArray['200'])!=0)
					{
						return $processedData;
					}
				}
			}
			// $journalArray['data']=$dataArray;
			// $journalArray['inventory']=$billTrimData['inventory'];
			// $method=$constantArray['postMethod'];
			// $path=$constantArray['journalUrl'].'/'.$billData[0]->jf_id;
			// $journalRequest = Request::create($path,$method,$journalArray);
			// $journalRequest->headers->set('type',$constantArray['sales']);
			// $processedData = $journalController->update($journalRequest,$billData[0]->jf_id);

			// if(strcmp($processedData,$msgArray['200'])!=0)
			// {
			// 	return $processedData;
			// }
		}
		else if(array_key_exists('payment_mode',$billTrimData))
		{
			//update journal data
			if(strcmp($billTrimData['payment_mode'],$billData[0]->payment_mode)!=0)
			{
				//get jf_id journal-data
				$journalModel = new JournalModel();
				$journalData = $journalModel->getJfIdArrayData($billData[0]->jf_id);
				$decodedJournalData = json_decode($journalData);
				

				if(strcmp($billData[0]->payment_mode,$constantArray['credit'])==0 || strcmp($billData[0]->payment_mode,$constantArray['cash'])==0)
				{
					//get payment-id of previous payment-mode
					$previousLedgerResult = $ledgerModel->getLedgerId($billData[0]->company_id,$billData[0]->payment_mode);
					if(is_array(json_decode($previousLedgerResult)))
					{
						$previousPaymentLedgerId = json_decode($previousLedgerResult)[0]->ledger_id;
					}
				}
				else
				{
					$previousPaymentLedgerId =  $billData[0]->bank_ledger_id;
				}

				//get payment-id of previous payment-mode
				// $previousLedgerResult = $ledgerModel->getLedgerId($billData[0]->company_id,$billData[0]->payment_mode);
				// if(is_array(json_decode($previousLedgerResult)))
				// {
				// 	$previousPaymentLedgerId = json_decode($previousLedgerResult)[0]->ledger_id;
				// }

				if(strcmp($billTrimData['payment_mode'],$constantArray['credit'])==0 || strcmp($billTrimData['payment_mode'],$constantArray['cash'])==0)
				{
					//get payment-id
					$ledgerResult = $ledgerModel->getLedgerId($billData[0]->company_id,$billTrimData['payment_mode']);
					if(is_array(json_decode($ledgerResult)))
					{
						$paymentLedgerId = json_decode($ledgerResult)[0]->ledger_id;
					}
				}
				else
				{
					$paymentLedgerId =  $billTrimData['bank_ledger_id'];
				}

				//get payment-id
				// $ledgerResult = $ledgerModel->getLedgerId($billData[0]->company_id,$billTrimData['payment_mode']);
				// if(is_array(json_decode($ledgerResult)))
				// {
				// 	$paymentLedgerId = json_decode($ledgerResult)[0]->ledger_id;
				// }

				// $journalArrayData = array();
				for($arrayData=0;$arrayData<count($decodedJournalData);$arrayData++)
				{
					if(strcmp($decodedJournalData[$arrayData]->ledger_id,$previousPaymentLedgerId)==0)
					{
						$decodedJournalData[$arrayData]->ledger_id = $paymentLedgerId;
					}
					$journalArrayData[$arrayData]=array(
						'amount'=>$decodedJournalData[$arrayData]->amount,
						'amountType'=>$decodedJournalData[$arrayData]->amount_type,
						'ledgerId'=>$decodedJournalData[$arrayData]->ledger_id,
					);
				}
				//make data array for journal sale entry
				$journalArray = array();
				$journalArray= array(
					'data' => array(
					)
				);
				$journalArray['data']=$journalArrayData;
				$method=$constantArray['postMethod'];
				$path=$constantArray['journalUrl'].'/'.$billData[0]->jf_id;
				$journalRequest = Request::create($path,$method,$journalArray);
				$journalRequest->headers->set('type',$constantArray['sales']);
				$processedData = $journalController->update($journalRequest,$billData[0]->jf_id);
				if(strcmp($processedData,$msgArray['200'])!=0)
				{
					return $processedData;
				}
			}
		}
		$dateFlag=0;
		if(count($billTrimData)==1 && array_key_exists('entry_date',$billTrimData))
		{
			$dateFlag=1;
		}
		//validate bill data
		//........pending
		$invFlag=0;
		//set bill data into persistable object
		$billPersistable = array();
		$clientBillArrayData = $clientArray->getBillClientArrayData();
		
		//splice data from trim array
		for($index=0;$index<count($clientBillArrayData);$index++)
		{
			for($innerIndex=0;$innerIndex<count($billTrimData);$innerIndex++)
			{
				if(strcmp('inventory',array_keys($billTrimData)[$innerIndex])!=0)
				{
					if(strcmp(array_keys($billTrimData)[$innerIndex],array_keys($clientBillArrayData)[$index])==0)
					{
						array_splice($billTrimData,$innerIndex,1);
						break;
					}
				}
			}
		}
	
		for($billArrayData=0;$billArrayData<count($billTrimData);$billArrayData++)
		{
			// making an object of persistable
			$billPersistable[$billArrayData] = new BillPersistable();
			if(strcmp('inventory',array_keys($billTrimData)[$billArrayData])!=0)
			{
				$str = str_replace(' ', '', ucwords(str_replace('_', ' ', array_keys($billTrimData)[$billArrayData])));	
				$setFuncName = "set".$str;
				$getFuncName = "get".$str;
				$billPersistable[$billArrayData]->$setFuncName($billTrimData[array_keys($billTrimData)[$billArrayData]]);
				$billPersistable[$billArrayData]->setName($getFuncName);
				$billPersistable[$billArrayData]->setKey(array_keys($billTrimData)[$billArrayData]);
				$billPersistable[$billArrayData]->setSaleId($saleId);
			}
			else
			{
				$decodedProductArrayData = json_decode($billData[0]->product_array);
				$productArray = array();
				$productArray['invoiceNumber'] = $decodedProductArrayData->invoiceNumber;
				$productArray['transactionType'] = $decodedProductArrayData->transactionType;
				$productArray['companyId'] = $decodedProductArrayData->companyId;

				$itemizeBatch = [];
				for($inventoryData=0;$inventoryData<count($request->input()['inventory']);$inventoryData++)
				{
					$billTrimData['inventory'][$inventoryData]['amount'] = trim($request->input()['inventory'][$inventoryData]['amount']);
					$billTrimData['inventory'][$inventoryData]['productName'] = trim($request->input()['inventory'][$inventoryData]['productName']);

					if (isset($request->input()['inventory'][$inventoryData]['measurementUnit']))
					{
						$billTrimData['inventory'][$inventoryData]['measurementUnit'] = trim($request->input()['inventory'][$inventoryData]['measurementUnit']);
					}
					
					$billTrimData['inventory'][$inventoryData]['cgstPercentage'] = array_key_exists("cgstPercentage",$request->input()['inventory'][$inventoryData])?trim($request->input()['inventory'][$inventoryData]['cgstPercentage']):0;
					$billTrimData['inventory'][$inventoryData]['cgstAmount'] = array_key_exists("cgstAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['cgstAmount']):0;
					$billTrimData['inventory'][$inventoryData]['sgstPercentage'] = array_key_exists("sgstPercentage",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['sgstPercentage']):0;
					$billTrimData['inventory'][$inventoryData]['sgstAmount'] = array_key_exists("sgstAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['sgstAmount']):0;
					$billTrimData['inventory'][$inventoryData]['igstPercentage'] = array_key_exists("igstPercentage",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['igstPercentage']):0;
					$billTrimData['inventory'][$inventoryData]['igstAmount'] = array_key_exists("igstAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['igstAmount']):0;
					$billTrimData['inventory'][$inventoryData]['cessAmount'] = array_key_exists("cessAmount",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['cessAmount']):0;
					$billTrimData['inventory'][$inventoryData]['realQtyData'] = array_key_exists("realQtyData",$request->input()['inventory'][$inventoryData]) ? trim($request->input()['inventory'][$inventoryData]['realQtyData']):0;

					if (array_key_exists('itemizeDetail', $request->input()['inventory'][$inventoryData])) {
						$itemizeDtlJson = trim($request->input()['inventory'][$inventoryData]['itemizeDetail']);
						$itemizeDtlArray = json_decode($itemizeDtlJson);
						if (count($itemizeDtlArray) > 0) {
							$returnItemize = [];
							foreach ($itemizeDtlArray as $itemizeDtl) {
								$returnItemize[] = [
									'imei_no'=>trim($itemizeDtl->imei_no),
									'barcode_no'=>trim($itemizeDtl->barcode_no),
									'qty'=>trim($itemizeDtl->qty)
								];
								$itemizeProduct = $billTrimData['inventory'][$inventoryData]['productId'];
								$itemizeBatch[] = [
									'product_id' => $itemizeProduct,
									'imei_no' =>trim($itemizeDtl->imei_no),
									'barcode_no' =>trim($itemizeDtl->barcode_no),
									'qty' =>trim($itemizeDtl->qty)*(-1),
									'jfId' => $billData[0]->jf_id,
									'sales_bill_no' => $productArray['invoiceNumber']
								];
							}
							$billTrimData['inventory'][$inventoryData]['itemizeDetail'] = $returnItemize;
						}
					}
				}
				$invFlag=1;
				$productArray['inventory'] = $billTrimData['inventory'];

				if (!empty($itemizeBatch) && count($itemizeBatch) > 0) {
					$productService = new ProductService();
					$itemizeBatchInsertion = $productService->updateInOutwardItemizeData($itemizeBatch,$billData[0]->jf_id,$billData[0]->created_at);
					if (strcmp($itemizeBatchInsertion, $msgArray['200']) != 0) {
						return $itemizeBatchInsertion;
					}
				}else{
					$productService = new ProductService();
					$itemizeDelete = $productService->deleteInOutwardItemizeData($jfId,$constantArray['sales']);
					if (strcmp($itemizeDelete, $msgArray['200']) != 0) {
						return $itemizeDelete;
					}
				}
				$billPersistable[$billArrayData]->setProductArray(json_encode($productArray));
				$billPersistable[$billArrayData]->setName('getProductArray');
				$billPersistable[$billArrayData]->setKey('product_array');
				$billPersistable[$billArrayData]->setSaleId($saleId);
			}
		}
		if($invFlag==1)
		{
			// $billPersistable[count($billPersistable)] = 'flag';
		}

		$documentPath = $constantArray['billDocumentUrl'];
		$docFlag=0;
		if(in_array(true,$request->file()) || array_key_exists('scanFile',$request->input()))
		{
			$documentController = new DocumentController(new Container());
			$processedData = $documentController->insertUpdate($request,$documentPath);
			if(is_array($processedData))
			{
				$docFlag=1;
			}
			else
			{
				return $processedData;
			}
		}
		if($dateFlag==1)
		{
			$billPersistable = new BillPersistable();
			$billPersistable->setEntryDate($billTrimData['entry_date']);
			$billPersistable->setSaleId($saleId);
		}

		if (isset($clientId))
		{
			if ($clientId != '' && $clientId != 0)
			{
				if (count($billPersistable) == 0){
					$billPersistable = new BillPersistable();
					$billPersistable->setClientId($clientId);
				} else {
					$lastCount = count($billPersistable);
					$billPersistable[$lastCount] = new BillPersistable();
					$billPersistable[$lastCount]->setClientId($clientId);
					$billPersistable[$lastCount]->setName('getClientId');
					$billPersistable[$lastCount]->setKey('client_id');
					$billPersistable[$lastCount]->setSaleId($saleId);
				}
			}
		}

		if($docFlag==1)
		{
			$array1 = array();
			array_push($processedData,$billPersistable);
			return $processedData;
		}
		else
		{
			return $billPersistable;
		}
	}
	
	/**
     * ledger validation for insert ledger-data
     * $param company-id,ledger-name,contact-no
     * @return result array/error-message
     */	
	public function ledgerValidationOfInsertion($companyId,$ledgerName,$contactNo)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$tRequest = array();
		$businessResult = array();
		$buisnessLogic = new BuisnessLogic();
		$businessResult = $buisnessLogic->validateLedgerData($companyId,$ledgerName,$contactNo);
		if(!is_array($businessResult))
		{
			$ledgerName = $ledgerName.$contactNo;
			$innerBusinessResult = $buisnessLogic->validateLedgerData($companyId,$ledgerName,$contactNo);
			if(!is_array($innerBusinessResult))
			{
				return $exceptionArray['content'];
			}
		}
		return $tRequest;
	}
	
	/**
     * ledger validation for update ledger-data
     * $param contact-no,ledger-name,ledger-id,trim request array
     * @return result array/error-message
     */	
	public function ledgerValidationOfUpdate($contactNo,$ledgerName,$ledgerId,$inputArray)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$tRequest = array();
		$buisnessLogic = new BuisnessLogic();
		$businessResult = $buisnessLogic->validateUpdateLedgerData($ledgerName,$ledgerId,$inputArray);
		if(!is_array($businessResult))
		{
			$ledgerName = $ledgerName.$contactNo;
			$innerBusinessResult = $buisnessLogic->validateUpdateLedgerData($ledgerName,$ledgerId,$inputArray);
			if(!is_array($innerBusinessResult))
			{
				return $exceptionArray['content'];
			}
		}
		return $tRequest;
	}
	
	/**
     * client insertion
     * $param trim request array
     * @return result array/error-message
     */	
	public function clientInsertion($tRequest)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$clientArray = array();
		$clientArray['clientName']=$tRequest['client_name'];
		$clientArray['companyName']=array_key_exists('company_name',$tRequest)?$tRequest['company_name']:'';
		$clientArray['emailId']=array_key_exists('email_id',$tRequest)?$tRequest['email_id']:'';
		$clientArray['gst']=array_key_exists('gst',$tRequest)?$tRequest['gst']:'';
		$clientArray['contactNo']=$tRequest['contact_no'];
		$clientArray['contactNo1']=array_key_exists('contact_no1',$tRequest)?$tRequest['contact_no1']:'';
		$clientArray['address1']=array_key_exists('address1',$tRequest)?$tRequest['address1']:'';
		$clientArray['birthDate']=array_key_exists('birth_date',$tRequest)?$tRequest['birth_date']:'0000-00-00';
		$clientArray['anniversaryDate']=array_key_exists('anniversary_date',$tRequest)?$tRequest['anniversary_date']:'0000-00-00';
		$clientArray['otherDate']=array_key_exists('other_date',$tRequest)?$tRequest['other_date']:'0000-00-00';
		$clientArray['isDisplay']=array_key_exists('is_display',$tRequest)?$tRequest['is_display']:$constantArray['isDisplayYes'];
		$clientArray['stateAbb']=array_key_exists('state_abb',$tRequest)?$tRequest['state_abb']:'';
		$clientArray['cityId']=array_key_exists('city_id',$tRequest)?$tRequest['city_id']:'';
		if(array_key_exists('profession_id',$tRequest))
		{
			$clientArray['professionId']=$tRequest['profession_id'];
		}
		$clientController = new ClientController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['clientUrl'];
		$clientRequest = Request::create($path,$method,$clientArray);
		$processedData = $clientController->store($clientRequest);
		return $processedData;
	}
	
	/**
     * ledger insertion
     * $param trim request array,client_id
     * @return result array/error-message
     */	
	public function ledgerInsertion($tRequest,$clientId,$invoiceNumber,$companyId)
	{
		$amountTypeEnum = new AmountTypeEnum();
		$enumAmountTypeArray = $amountTypeEnum->enumArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$ledgerArray=array();
		$ledgerArray['ledgerName']=$tRequest['client_name'];
		$ledgerArray['address1']=array_key_exists('address1',$tRequest)?$tRequest['address1']:'';
		$ledgerArray['address2']='';
		$ledgerArray['contactNo']=$tRequest['contact_no'];
		$ledgerArray['emailId']=array_key_exists('email_id',$tRequest)?$tRequest['email_id']:'';
		$ledgerArray['invoiceNumber']=$invoiceNumber;
		$ledgerArray['stateAbb']=array_key_exists('state_abb',$tRequest) ? $tRequest['state_abb']:'';
		$ledgerArray['cityId']=array_key_exists('city_id',$tRequest) ? $tRequest['city_id']:'';
		$ledgerArray['companyId']=$companyId;
		$ledgerArray['balanceFlag']=$constantArray['openingBalance'];
		$ledgerArray['amount']=0;
		$ledgerArray['amountType']=$constantArray['credit'];
		$ledgerArray['ledgerGroupId']=$constantArray['ledgerGroupSundryDebitors'];
		$ledgerArray['clientName']=$tRequest['client_name'];
		$ledgerArray['outstandingLimit']='0.0000';
		$ledgerArray['outstandingLimit']=$enumAmountTypeArray['creditType'];
		$ledgerArray['clientId']=$clientId;
		$ledgerController = new LedgerController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['ledgerUrl'];
		$ledgerRequest = Request::create($path,$method,$ledgerArray);
		$processedData = $ledgerController->store($ledgerRequest);
		return $processedData;
	}
	/**
     * ledger insertion
     * $param trim request array,user_id
     * @return result array/error-message
     */	
	public function staffLedgerInsertion($staffArray,$userId,$invoiceNumber,$companyId)
	{
		$amountTypeEnum = new AmountTypeEnum();
		$enumAmountTypeArray = $amountTypeEnum->enumArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$ledgerArray=array();
		$ledgerArray['ledgerName']=$staffArray->user_name;
		$ledgerArray['address1']=$staffArray->address;
		$ledgerArray['address2']='';
		$ledgerArray['contactNo']=$staffArray->contact_no;
		$ledgerArray['emailId']=$staffArray->email_id;
		$ledgerArray['invoiceNumber']=$invoiceNumber;
		$ledgerArray['stateAbb']=$staffArray->state_abb;
		$ledgerArray['cityId']=$staffArray->city_id;
		$ledgerArray['companyId']=$companyId;
		$ledgerArray['balanceFlag']=$constantArray['openingBalance'];
		$ledgerArray['amount']=0;
		$ledgerArray['amountType']=$constantArray['debit'];
		$ledgerArray['ledgerGroupId']=$constantArray['ledgerGroupSundryCreditors'];
		$ledgerArray['clientName']=$staffArray->user_name;
		$ledgerArray['outstandingLimit']='0.0000';
		$ledgerArray['outstandingLimit']=$enumAmountTypeArray['creditType'];
		$ledgerArray['userId']=$userId;
		$ledgerController = new LedgerController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['ledgerUrl'];
		$ledgerRequest = Request::create($path,$method,$ledgerArray);
		$processedData = $ledgerController->store($ledgerRequest);
		return $processedData;
	}
	
	/**
     * client update
     * $param trim request array,client_id
     * @return result array/error-message
     */	
	public function clientUpdate($tRequest,$clientId,$clientData)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$clientInfo = json_decode($clientData,true)['clientData'][0];
		// update client data
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$clientArray = array();
		$updateFlag = 0;
		if(array_key_exists('client_name',$tRequest))
		{
			$clientArray['clientName']=$tRequest['client_name'];
			if (isset($clientInfo['client_name']) && $clientInfo['client_name'] != $clientArray['clientName']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('company_name',$tRequest))
		{
			$clientArray['companyName']=$tRequest['company_name'];

			if (isset($clientInfo['company_name']) && $clientInfo['company_name'] != $clientArray['companyName']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('email_id',$tRequest))
		{
			$clientArray['emailId']=$tRequest['email_id'];

			if (isset($clientInfo['email_id']) && $clientInfo['email_id'] != $clientArray['emailId']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('gst',$tRequest))
		{
			$clientArray['gst']=$tRequest['gst'];

			if (isset($clientInfo['gst']) && $clientInfo['gst'] != $clientArray['gst']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('contact_no',$tRequest))
		{
			$clientArray['contactNo']=$tRequest['contact_no'];

			if (isset($clientInfo['contact_no']) && $clientInfo['contact_no'] != $clientArray['contactNo']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('contact_no1',$tRequest))
		{
			$clientArray['contactNo1']=$tRequest['contact_no1'];

			if (isset($clientInfo['contact_no1']) && $clientInfo['contact_no1'] != $clientArray['contactNo1']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('address1',$tRequest))
		{
			$clientArray['address1']=$tRequest['address1'];

			if (isset($clientInfo['address1']) && $clientInfo['address1'] != $clientArray['address1']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('is_display',$tRequest))
		{
			$clientArray['isDisplay']=$tRequest['is_display'];

			if (isset($clientInfo['is_display']) && $clientInfo['is_display'] != $clientArray['isDisplay']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('state_abb',$tRequest))
		{
			$clientArray['stateAbb']=$tRequest['state_abb'];

			if (isset($clientInfo['state_abb']) && $clientInfo['state_abb'] != $clientArray['stateAbb']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('profession_id',$tRequest))
		{
			$clientArray['professionId']=$tRequest['profession_id'];

			if (isset($clientInfo['profession_id']) && $clientInfo['profession_id'] != $clientArray['professionId']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('city_id',$tRequest))
		{
			$clientArray['cityId']=$tRequest['city_id'];

			if (isset($clientInfo['city_id']) && $clientInfo['city_id'] != $clientArray['cityId']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('birth_date',$tRequest))
		{
			$clientArray['birthDate']=$tRequest['birth_date'];

			if (isset($clientInfo['birth_date']) && $clientInfo['birth_date'] != $clientArray['birthDate']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('anniversary_date',$tRequest))
		{
			$clientArray['anniversaryDate']=$tRequest['anniversary_date'];

			if (isset($clientInfo['anniversary_date']) && $clientInfo['anniversary_date'] != $clientArray['anniversaryDate']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('other_date',$tRequest))
		{
			$clientArray['otherDate']=$tRequest['other_date'];

			if (isset($clientInfo['other_date']) && $clientInfo['other_date'] != $clientArray['otherDate']) {
				$updateFlag = 1;
			}
		}
		if ($updateFlag == 1) {
			$clientController = new ClientController(new Container());
			$method=$constantArray['postMethod'];
			$path=$constantArray['clientUrl'].'/'.$clientId;
			$clientRequest = Request::create($path,$method,$clientArray);
			$processedData = $clientController->updateData($clientRequest,$clientId);
			return $processedData;
		}else{
			return $msgArray['200'];
		}
		
	}
	
	/**
     * ledger update
     * $param trim request array,ledger_id,client_id
     * @return result array/error-message
     */	
	public function ledgerUpdate($tRequest,$ledgerId,$clientId,$ledgerData)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$ledgerDecoded = json_decode($ledgerData,true)[0];
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$updateFlag = 0;
		//update ledger data
		$ledgerArray=array();
		// $ledgerArray['ledgerName']=$tRequest['client_name'];
		if(array_key_exists('address1',$tRequest))
		{
			$ledgerArray['address1']=$tRequest['address1'];
			if (isset($ledgerDecoded['address1']) && $ledgerDecoded['address1'] != $tRequest['address1']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('contact_no',$tRequest))
		{
			$ledgerArray['contactNo']=$tRequest['contact_no'];

			if (isset($ledgerDecoded['contact_no']) && $ledgerDecoded['contact_no'] != $tRequest['contact_no']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('email_id',$tRequest))
		{
			$ledgerArray['emailId']=$tRequest['email_id'];

			if (isset($ledgerDecoded['email_id']) && $ledgerDecoded['email_id'] != $tRequest['email_id']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('invoice_number',$tRequest))
		{
			$ledgerArray['invoiceNumber']=$tRequest['invoice_number'];

			if (isset($ledgerDecoded['invoice_number']) && $ledgerDecoded['invoice_number'] != $tRequest['invoice_number']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('state_abb',$tRequest))
		{
			$ledgerArray['stateAbb']=$tRequest['state_abb'];

			if (isset($ledgerDecoded['state_abb']) && $ledgerDecoded['state_abb'] != $tRequest['state_abb']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('city_id',$tRequest))
		{
			$ledgerArray['cityId']=$tRequest['city_id'];

			if (isset($ledgerDecoded['city_id']) && $ledgerDecoded['city_id'] != $tRequest['city_id']) {
				$updateFlag = 1;
			}
		}
		if(array_key_exists('company_id',$tRequest))
		{
			$ledgerArray['companyId']=$tRequest['company_id'];

			if (isset($ledgerDecoded['company_id']) && $ledgerDecoded['company_id'] != $tRequest['company_id']) {
				$updateFlag = 1;
			}
		}
		// if(array_key_exists('branch_id',$tRequest))
		// {
		// 	$ledgerArray['branchId']=$tRequest['branch_id'];
		// }
		if(array_key_exists('client_name',$tRequest))
		{
			$ledgerArray['clientName']=$tRequest['client_name'];

			if (isset($ledgerDecoded['client_name']) && $ledgerDecoded['client_name'] != $tRequest['client_name']) {
				$updateFlag = 1;
			}
		}
		$ledgerArray['clientId']=$clientId;
		if ($updateFlag==1) {
			$ledgerController = new LedgerController(new Container());
			$method=$constantArray['postMethod'];
			$path=$constantArray['ledgerUrl'].'/'.$ledgerId;
			$ledgerRequest = Request::create($path,$method,$ledgerArray);
			$processedData = $ledgerController->update($ledgerRequest,$ledgerId);
			return $processedData;
		}else{
			return $msgArray['200'];
		}
	}
	
	/**
     * ledger update
     * $param trim request array,ledger_id,user_id
     * @return result array/error-message
     */	
	public function staffLedgerUpdate($staffArray,$ledgerId,$userId,$ledgerData)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$updateFlag = 0;
		//update ledger data
		$ledgerArray=array();
		$ledgerArray['ledgerName']=$staffArray->user_name;

		if (isset($ledgerData['ledger_name']) && $ledgerData['ledger_name'] != $ledgerArray['ledgerName']) {
			$updateFlag = 1;
		}
		$ledgerArray['address1']=$staffArray->address;

		if (isset($ledgerData['address1']) && $ledgerData['address1'] != $ledgerArray['address1']) {
			$updateFlag = 1;
		}
		$ledgerArray['address2']='';
		$ledgerArray['contactNo']=$staffArray->contact_no;

		if (isset($ledgerData['contact_no']) && $ledgerData['contact_no'] != $ledgerArray['contactNo']) {
			$updateFlag = 1;
		}
		$ledgerArray['emailId']=$staffArray->email_id;

		if (isset($ledgerData['email_id']) && $ledgerData['email_id'] != $ledgerArray['emailId']) {
			$updateFlag = 1;
		}
		$ledgerArray['stateAbb']=$staffArray->state_abb;

		if (isset($ledgerData['state_abb']) && $ledgerData['state_abb'] != $ledgerArray['stateAbb']) {
			$updateFlag = 1;
		}
		$ledgerArray['cityId']=$staffArray->city_id;

		if (isset($ledgerData['city_id']) && $ledgerData['city_id'] != $ledgerArray['cityId']) {
			$updateFlag = 1;
		}
		$ledgerArray['balanceFlag']=$constantArray['openingBalance'];
		$ledgerArray['amount']=0;
		$ledgerArray['outstandingLimit']='0.0000';
		$ledgerArray['userId']=$userId;
		if ($updateFlag==1) {
			$ledgerController = new LedgerController(new Container());
			$method=$constantArray['postMethod'];
			$path=$constantArray['ledgerUrl'].'/'.$ledgerId;
			$ledgerRequest = Request::create($path,$method,$ledgerArray);
			$processedData = $ledgerController->update($ledgerRequest,$ledgerId);
			return $processedData;
		}else{
			return $msgArray['200'];
		}
		
	}

	/** 
	* @param company id, ledger_type
	* @return result ledger_id/ error-message
	*/
	public function insertCommissionLedger($companyId,$ledgerName)
	{
		$amountTypeEnum = new AmountTypeEnum();
		$enumAmountTypeArray = $amountTypeEnum->enumArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$ledgerArray=array();
		$companyService = new CompanyService();
		$exceptionMessage = new ExceptionMessage();
		$exceptionArray = $exceptionMessage->messageArrays();
		$companyJson = $companyService->getCompanyData($companyId);
		if (strcmp($companyJson,$exceptionArray['404'])==0) {
			return $companyJson;
		}
		$companyDataArray = json_decode($companyJson);
		$ledgerTypeArray = new LedgerArray();
		$ledgerTypeDataArray = $ledgerTypeArray->ledgerArrays();
		$ledgerGrpArray = $ledgerTypeArray->ledgerGrpArray();
		$ledgerIndex = array_search($ledgerName, $ledgerTypeDataArray);
		if ($ledgerIndex < 0) {
			return $exceptionArray['content'];
		}
		$ledgerArray['ledgerName']=$ledgerName;
		$ledgerArray['address1']='';
		$ledgerArray['address2']='';
		$ledgerArray['contactNo']='';
		$ledgerArray['emailId']='';
		$ledgerArray['invoiceNumber']='';
		$ledgerArray['stateAbb']= $companyDataArray->city->stateAbb;
		$ledgerArray['cityId']=$companyDataArray->city->cityId;
		$ledgerArray['companyId']=$companyId;
		$ledgerArray['balanceFlag']=$constantArray['openingBalance'];
		$ledgerArray['amount']=0;
		$ledgerArray['amountType']=$constantArray['credit'];
		$ledgerArray['ledgerGroupId']=$ledgerGrpArray[$ledgerIndex];
		$ledgerArray['clientName']='';
		$ledgerArray['outstandingLimit']='0.0000';
		$ledgerArray['outstandingLimit']=$enumAmountTypeArray['creditType'];
		$ledgerController = new LedgerController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['ledgerUrl'];
		$ledgerRequest = Request::create($path,$method,$ledgerArray);
		$processedData = $ledgerController->store($ledgerRequest);
		return $processedData;
	}
	/** 
	* @param inv Array, company Id
	* @return result itemwise Commission Amount
	*/
	public function itemWiseCommissionCalc($invArray,$companyId)
	{
		$commissionService = new CommissionService();
		$commissionAmount = 0;
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		foreach ($invArray as $product) {
			$productId = $product['productId'];
			$qty = $product['qty'];
			$rate = $product['price'];
			$itemWise = $commissionService->getItemwiseByProduct($productId,$companyId);
			if (strcmp($exceptionArray['404'],$itemWise)==0) {
				continue;
			}else{
				$itemWiseArray = json_decode($itemWise,true);
				$itemWiseCount = count($itemWiseArray);
				$cAmtArray = [];
				while ($itemWiseCount--) {
					if ($itemWiseArray[$itemWiseCount]['commissionFromQty']<= $qty) {
					 	if ($itemWiseArray[$itemWiseCount]['commissionToQty']>= $qty) {
					 		$cAmtArray = $itemWiseArray[$itemWiseCount];
					 		break;
					 	}else{
					 		if (!count($cAmtArray)) {
					 			$cAmtArray = $itemWiseArray[$itemWiseCount];
					 		}elseif ($itemWiseArray[$itemWiseCount]['commissionToQty']<= $qty) {
						 		if ($itemWiseArray[$itemWiseCount]['commissionToQty'] >= $cAmtArray['commissionToQty']) {
						 			$cAmtArray = $itemWiseArray[$itemWiseCount];
						 		}
						 	}
					 	}
					}
				}
				if (count($cAmtArray)) {
					if ($cAmtArray['commissionRateType'] == 'Flat') {
						$commissionAmount += (float)$qty * (float)$cAmtArray['commissionRate'];
					}else{
						if (strtolower($cAmtArray['commissionCalcOn']) == 'mrp') {
							$commissionAmount += (float)$qty * (float)$cAmtArray['mrp'] * (float)$cAmtArray['commissionRate'] / 100;
						}else{
							$commissionAmount += (float)$qty * (float)$rate * (float)$cAmtArray['commissionRate'] / 100;
						}
					}
				}else{
					continue;
				}
			}
		}
		return $commissionAmount;
	}
}