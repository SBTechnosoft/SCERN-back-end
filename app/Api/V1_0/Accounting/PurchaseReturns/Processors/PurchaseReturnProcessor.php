<?php
namespace ERP\Api\V1_0\Accounting\PurchaseReturns\Processors;
// common deps
use ERP\Api\V1_0\Support\BaseProcessor;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
// Direct deps
use ERP\Api\V1_0\Accounting\PurchaseReturns\Transformers\PurchaseReturnTransformer;
use ERP\Core\Accounting\PurchaseBills\Persistables\PurchaseBillPersistable;
use ERP\Core\Accounting\PurchaseBills\Validations\PurchaseBillValidate;
use ERP\Model\Accounting\PurchaseReturns\PurchaseReturnModel;
// Journal deps
use ERP\Model\Accounting\PurchaseBills\PurchaseBillModel;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
use ERP\Api\V1_0\Accounting\Journals\Controllers\JournalController;
use Illuminate\Container\Container;
// Other deps
use ERP\Core\Products\Services\ProductService;

// ends
/**
 * @author Hiren Faldu <hiren.f@siliconbrain.in>
 */
class PurchaseReturnProcessor extends BaseProcessor
{
	/**
     * @var persistable
	 * @var request
	*/
	private $persistable;
	private $request;
	private $transformer;
	private $validate;
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Purchase Return Persistable object
     */	
	public function createPersistable(Request $request, $purchaseId)
	{
		$this->request = $request;
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		$this->transformer = new PurchaseReturnTransformer();
		$tRequest = $this->transformer->trimInsertData($this->request);
		if($tRequest==1)
		{
			return $exceptionArray['content'];
		}
		else if(!is_array($tRequest)) {
			return $tRequest;
		}

		$this->validate = new PurchaseBillValidate();
		$status = $this->validate->validate($tRequest);
		if(strcmp($status, $exceptionArray['200']) != 0)
		{
			return $status;
		}

		$value = array();
		$data=0;

		$journalResult = $this->makeJournalArray($tRequest,'insert',$purchaseId);
		if(!is_numeric($journalResult))
		{
			return $journalResult;
		}
		$tRequest['purchaseId'] = $purchaseId;
		$inventoryData['inventory'] = $tRequest['inventory'];
		$inventoryData['billNumber'] = $tRequest['billNumber'];
		$inventoryData['transactionType'] = $constantArray['journalOutward'];
		$inventoryData['companyId'] = $tRequest['companyId'];
		

		// insertion for itemize (IMEI/Serial) purchase bill
		$inventoryArrayData = $inventoryData['inventory'];
		$inventoryCount = count($inventoryArrayData);
		$inventoryInc = 0;
		$itemizeBatch = array();
		$itemizeBillNo = $inventoryData['billNumber'];
		while ($inventoryInc < $inventoryCount) 
		{
			if (isset($inventoryArrayData[$inventoryInc]['itemizeDetail'])) 
			{
				$itemizeArray = $inventoryArrayData[$inventoryInc]['itemizeDetail'];
				if (count($itemizeArray) > 0) 
				{
					$itemizeProduct =  $inventoryArrayData[$inventoryInc]['productId'];
					foreach ($itemizeArray as $serialArray) 
					{
						$itemizeBatch[] = [
							'product_id' => $itemizeProduct,
							'imei_no' => $serialArray['imei_no'],
							'barcode_no' => $serialArray['barcode_no'],
							'qty' => $serialArray['qty'] * -1,
							'jfId' => $journalResult,
							'purchase_bill_no' => $itemizeBillNo
						];
					}
				}
			}
			$inventoryInc++;
		}

		$requestData = array_except($tRequest,['inventory']);

		if (!empty($itemizeBatch) && count($itemizeBatch) > 0) 
		{
			$productService = new ProductService();
			$itemizeBatchInsertion = $productService->insertInOutwardItemizeData($itemizeBatch);
			if (strcmp($itemizeBatchInsertion, $exceptionArray['200']) != 0) 
			{
				return $itemizeBatchInsertion;
			}
		}

		$this->persistable = new PurchaseBillPersistable();
		$getNameArray = [];
		foreach ($requestData as $key => $value) {
			if (!is_numeric($key)) 
			{
				if (strpos($value, '\'') !== FALSE)
				{
					$value= str_replace("'","\'",$value);
				}
				$conversion= preg_replace('/(?<!\ )[A-Z]/', '_$0', $key);
				$lowerCase = strtolower($conversion);
				$setFuncName = 'set'.$key;
				$getFuncName = 'get'.$key;
				$this->persistable->$setFuncName($value);
				$getNameArray[$lowerCase] = $getFuncName;
			}
		}
		$this->persistable->setProductArray(json_encode($inventoryData));
		$this->persistable->setJfId($journalResult);

		return [$getNameArray, $this->persistable];
	}

	/**
     * make an journal-array
     * @param trim request array
     * @return PurchaseBill Persistable object
     */
	public function makeJournalArray($trimRequest,$stringOperation,$purchaseId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		$purchaseIdArray = array();
		$purchaseIdArray['purchasebillid'][0] = $purchaseId;

		$purchaseModel = new PurchaseBillModel();
		$purchaseArrayData = $purchaseModel->getPurchaseBillData($purchaseIdArray);

		if(strcmp($purchaseArrayData,$exceptionArray['204'])==0)
		{
			return $exceptionArray['204'];
		}
		$purchaseArrayData = json_decode($purchaseArrayData);
		$trimRequest['companyId'] = $purchaseArrayData[0]->company_id;
		$trimRequest['vendorId'] = $purchaseArrayData[0]->vendor_id;
		$trimRequest['billNumber'] = $purchaseArrayData[0]->bill_number;

		if(!array_key_exists('transactionDate',$trimRequest) && $purchaseId!='')
		{
			$trimRequest['transactionDate'] = $purchaseArrayData[0]->transaction_date;
		}
		
		//get ledger of payment-mode
		$ledgerModel = new LedgerModel();
		$generalLedgerData = $ledgerModel->getGeneralLedgers($trimRequest['companyId']);
		$generalLedgerArray = json_decode($generalLedgerData);
		$legderCount = count($generalLedgerArray);
		$paymentLedgerId = '';
		$taxLedgerId = '';
		$discountLedgerId ='';
		$purchaseLedgerId = '';

		$amountTypeEnum = new AmountTypeEnum();
		$amountTypeArray = $amountTypeEnum->enumArrays();

		for($ledgerArray=0;$ledgerArray<$legderCount;$ledgerArray++)
		{
			if(strcmp($generalLedgerArray[$ledgerArray]->ledger_name,$trimRequest['paymentMode'])==0)
			{
				if ($trimRequest['paymentMode'] == 'cash' || $trimRequest['paymentMode'] == 'bank')
				{
					$paymentLedgerId = $generalLedgerArray[$ledgerArray]->ledger_id;
				}
				else
				{
					$paymentLedgerId = $trimRequest['bankLedgerId'];
				}
			}
			if(strcmp($generalLedgerArray[$ledgerArray]->ledger_name,'tax(input)')==0 )
			{$taxLedgerId = $generalLedgerArray[$ledgerArray]->ledger_id;}
			if(strcmp($generalLedgerArray[$ledgerArray]->ledger_name,'discount(income)')==0)
			{$discountLedgerId = $generalLedgerArray[$ledgerArray]->ledger_id;}
			if(strcmp($generalLedgerArray[$ledgerArray]->ledger_name,'purchase_return')==0)
			{$purchaseLedgerId = $generalLedgerArray[$ledgerArray]->ledger_id;}
		}
		// total discount calculation
		$finalTotalDiscount=0;
		$inventoryCount = count($trimRequest['inventory']);
		for($discountArray=0;$discountArray<$inventoryCount;$discountArray++)
		{
			if (@$trimRequest['inventory'][$discountArray]['discountType'])
			{
				$discount = strcmp($trimRequest['inventory'][$discountArray]['discountType'],$constantArray['Flatdiscount'])==0
						? $trimRequest['inventory'][$discountArray]['discount'] 
						: ($trimRequest['inventory'][$discountArray]['discount']/100)*
						($trimRequest['inventory'][$discountArray]['price']*$trimRequest['inventory'][$discountArray]['qty']);
			}
			else
			{
				$discount = 0;
			}
			
			$finalTotalDiscount = $discount+$finalTotalDiscount;
		}
		$grandTotal = $trimRequest['total']+$trimRequest['extraCharge'];

		$ledgerId = $trimRequest['vendorId'];
		$actualTotal  = $trimRequest['total'] - $trimRequest['tax'];
		$finalTotal = $actualTotal+$trimRequest['extraCharge'];
		$totalWithTaxAmount = $trimRequest['tax']+$actualTotal;
		$total = $totalWithTaxAmount+$trimRequest['extraCharge']-$finalTotalDiscount;
		$mAmount = $actualTotal+$trimRequest['extraCharge'];
		// calling function for display debit-credit
		$dataArray = [];
		$transactionType = [];

		$transactionType[0] = $constantArray['purchaseReturnType'];
		if($trimRequest['total'] == $trimRequest['advance']) 
		{
			$dataArray[0][0] = [
				"amount"=>$trimRequest['advance'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$paymentLedgerId
			];
		}
		else
		{
			$dataArray[0][0] = [
				"amount"=>$trimRequest['total'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$ledgerId
			];
			if(isset($trimRequest['advance']) && $trimRequest['advance'] != '' && $trimRequest['advance']!=0) 
			{
				$transactionType[1] = $constantArray['paymentType'];
				$dataArray[1][0] = [
				"amount"=>$trimRequest['advance'],
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$ledgerId
				];
				$dataArray[1][1] = [
					"amount"=>$trimRequest['advance'],
					"amountType"=>$amountTypeArray['debitType'],
					"ledgerId"=>$paymentLedgerId
				];
			}
		}
		if($finalTotalDiscount != 0) 
		{
			$dataArray[0][] = [
					"amount"=>$finalTotalDiscount,
					"amountType"=>$amountTypeArray['debitType'],
					"ledgerId"=>$discountLedgerId
				];
		}
		$purchaseLedgerAmount = $finalTotalDiscount+$trimRequest['total'];
		$purchaseLedgerTax = 0;
		if($trimRequest['tax'] != 0) 
		{
			$purchaseLedgerAmount -= $trimRequest['tax'];
			$purchaseLedgerTax = $trimRequest['tax'];
		}

		$dataArray[0][]=[
			"amount"=> $purchaseLedgerAmount,
			"amountType"=> $amountTypeArray['creditType'], 
			"ledgerId"=> $purchaseLedgerId
		];

		if ($purchaseLedgerTax != 0) 
		{
			$dataArray[0][] = [
				"amount"=>$purchaseLedgerTax,
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$taxLedgerId,
			];
		}
		
		// New Journal Logic Fixing Ends
		$journalController = new JournalController(new Container());

		$journalMethod=$constantArray['getMethod'];
		$journalPath=$constantArray['journalUrl'];
		$journalDataArray = array();
		$journalJfIdRequest = Request::create($journalPath,$journalMethod,$journalDataArray);
		$jfId = $journalController->getData($journalJfIdRequest);
		$jsonDecodedJfId = json_decode($jfId)->nextValue;

		// conversion of transaction-date
		$splitedDate = explode("-",trim($trimRequest['transactionDate']));
		$transactionDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		$inventoryCount = count($trimRequest['inventory']);
		$journalInventory = array();

		for($inventoryArray=0;$inventoryArray<$inventoryCount;$inventoryArray++)
		{
			$journalInventory[$inventoryArray]=$trimRequest['inventory'][$inventoryArray];
			$journalInventory[$inventoryArray]['productId']=$trimRequest['inventory'][$inventoryArray]['productId'];
			$journalInventory[$inventoryArray]['discount']=$trimRequest['inventory'][$inventoryArray]['discount'];
			$journalInventory[$inventoryArray]['price']=$trimRequest['inventory'][$inventoryArray]['price'];
			$journalInventory[$inventoryArray]['qty']=$trimRequest['inventory'][$inventoryArray]['qty'];
			$journalInventory[$inventoryArray]['measurementUnit']=$trimRequest['inventory'][$inventoryArray]['measurementUnit'];

			if (array_key_exists('discountType', $trimRequest['inventory'][$inventoryArray]))
			{
				$journalInventory[$inventoryArray]['discountType']= $trimRequest['inventory'][$inventoryArray]['discountType'];
			}
			else
			{
				$journalInventory[$inventoryArray]['discountType']= $constantArray['Flatdiscount'];
			}
		}
		$dataArrayCount = count($dataArray);
		for ($multiJournalCreate=0; $multiJournalCreate < $dataArrayCount; $multiJournalCreate++) 
		{
			// make data array for journal sale entry
			$journalArray = array();
			$journalArray= array(
				'jfId' => $jsonDecodedJfId,
				'data' => array(
				),
				'entryDate' => $transactionDate,
				'companyId' => $trimRequest['companyId']
			);
			$journalArray['data']=$dataArray[$multiJournalCreate];
			if (strcmp($transactionType[$multiJournalCreate],$constantArray['purchaseReturnType'])==0) 
			{
				$journalArray['inventory']=$journalInventory;
				$journalArray['transactionDate']=$transactionDate;
				$journalArray['tax']=$purchaseLedgerTax;
				$journalArray['billNumber']=$trimRequest['billNumber'];
			}
			
			$method=$constantArray['postMethod'];

			$journalArray['vendorId'] = $trimRequest['vendorId'];
			$path=$constantArray['journalUrl'];
			$journalRequest = Request::create($path,$method,$journalArray);
			$journalRequest->headers->set('type',$transactionType[$multiJournalCreate]);
			$processedData = $journalController->store($journalRequest);
			if(strcmp($processedData,$exceptionArray['200'])!=0)
			{
				print_r($journalArray);
				return $processedData;
			}
		}
		return $jsonDecodedJfId;
	}
}