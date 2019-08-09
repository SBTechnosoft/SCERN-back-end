<?php
namespace ERP\Api\V1_0\Accounting\SalesReturns\Processors;
use Carbon;
use ERP\Api\V1_0\Support\BaseProcessor;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Api\V1_0\Accounting\SalesReturns\Transformers\SalesReturnTransformer;
use ERP\Core\Accounting\SalesReturns\Validations\SalesReturnValidate;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Api\V1_0\Accounting\Journals\Controllers\JournalController;
use Illuminate\Container\Container;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Products\Services\ProductService;
use ERP\Core\Accounting\Bills\Persistables\BillPersistable;


class SalesReturnProcessor extends BaseProcessor
{
	/**
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
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();	
		//trim an input 
		$salesReturnTransformer = new SalesReturnTransformer();
		$tRequest = $salesReturnTransformer->trimInsertData($this->request);
		if($tRequest==1)
		{
			return $msgArray['content'];
		}
		$ledgerModel = new LedgerModel();
		//validation
		$salesReturnValidate = new SalesReturnValidate();
		$status = $salesReturnValidate->validate($tRequest);
		if($status==$constantArray['success'])
		{
			if(!array_key_exists($constantArray['contactNo'],$tRequest))
			{
				$contactNo="";
			}
			else
			{
				$contactNo = $tRequest['contact_no'];
			}
			$clientLedgerData = $ledgerModel->getDataAsPerClientId($tRequest['client_id']);
			if(strcmp($clientLedgerData,$msgArray['404'])==0)
			{
				return $clientLedgerData;
			}
			$ledgerId = json_decode($clientLedgerData)[0]->ledger_id;
		}else{
			return $status;
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
			else
			{
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
		$ledgerIdData = $ledgerModel->getLedgerId($tRequest['company_id'],$constantArray['salesReturnType']);
		$decodedLedgerId = json_decode($ledgerIdData);
		$companyDetail = new CompanyDetail();
		$companyDetails = $companyDetail->getCompanyDetails($tRequest['company_id']);
		$tRequest['total'] = number_format($tRequest['total'],$companyDetails['noOfDecimalPoints'],'.','');	
		$tRequest['advance'] = number_format($tRequest['advance'],$companyDetails['noOfDecimalPoints'],'.','');	
		$ledgerTaxAcId = $generalLedgerArray[0][0]->ledger_id;
		$ledgerSalesReturnId = $decodedLedgerId[0]->ledger_id;
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
		$totalSaleAmount = $discountTotal+$tRequest['total'];
		$totalDebitAmount = $tRequest['tax']+$tRequest['total'];
		$dataArray = [];
		$transactionType = [];
		$transactionType[0] = $constantArray['salesReturnType'];
		if ($tRequest['total'] == $tRequest['advance']) {
			$dataArray[0][0] = [
				"amount"=>$tRequest['advance'],
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$paymentLedgerId
			];
		}else{
			$dataArray[0][0] = [
				"amount"=>$tRequest['total'],
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$ledgerId
			];
			//  Oth Array is for Sales Txn 1st Array is for Cash or Advance Payment done by Client on Bill Generation
			if ($request->input()['advance']!="" && $tRequest['advance']!=0) {
				$transactionType[1] = $constantArray['refundType'];
				$dataArray[1][0] = [
				"amount"=>$tRequest['advance'],
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$paymentLedgerId
				];
				$dataArray[1][1] = [
					"amount"=>$tRequest['advance'],
					"amountType"=>$amountTypeArray['debitType'],
					"ledgerId"=>$ledgerId
				];
			}
		}
		if ($discountTotal != 0) {
			$dataArray[0][] = [
				"amount"=>$discountTotal,
				"amountType"=>$amountTypeArray['creditType'],
				"ledgerId"=>$ledgerDiscountAcId
			];
		}
		if ($tRequest['tax'] != 0) {
			$dataArray[0][] = [
				"amount"=>$tRequest['total']+$discountTotal-$tRequest['tax'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$ledgerSalesReturnId
			];
			$dataArray[0][] = [
				"amount"=>$tRequest['tax'],
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$ledgerTaxAcId
			];
		}else{
			$dataArray[0][] = [
				"amount"=>$tRequest['total']+$discountTotal,
				"amountType"=>$amountTypeArray['debitType'],
				"ledgerId"=>$ledgerSalesReturnId
			];
		}
		for ($multiJournalCreate=0; $multiJournalCreate < count($dataArray); $multiJournalCreate++) {
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
				'tax'=> $tRequest['tax']
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
		$productArray = array();
		$productArray['invoiceNumber']=$tRequest['invoice_number'];
		$productArray['transactionType']=$constantArray['journalInward'];
		$productArray['companyId']=$tRequest['company_id'];
		$tInventoryArray = array();
		$itemizeBatch = array();
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
							'qty' => trim($serialArray->qty),
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
		if (!empty($itemizeBatch) && count($itemizeBatch) > 0) {
			$productService = new ProductService();
			$itemizeBatchInsertion = $productService->insertInOutwardItemizeData($itemizeBatch);
			if (strcmp($itemizeBatchInsertion, $msgArray['200']) != 0) {
				return $itemizeBatchInsertion;
			}
		}
		$requestData = array_except($tRequest,['inventory','contact_no']);
		$billPersistable = new BillPersistable();
		$getNameArray = [];
		foreach ($requestData as $key => $value) {
			if (!is_numeric($key)) 
			{
				if (strpos($value, '\'') !== FALSE)
				{
					$value= str_replace("'","\'",$value);
				}
				$str = str_replace(' ', '', (ucwords(str_replace('_', ' ', $key))));
				$setFuncName = 'set'.$str;
				$getFuncName = 'get'.$str;
				$billPersistable->$setFuncName($value);
				$getNameArray[$key] = $getFuncName;
			}
		}
		$billPersistable->setProductArray(json_encode($productArray));
		$billPersistable->setJfId($jsonDecodedJfId);
		return [$getNameArray,$billPersistable];
	}
}