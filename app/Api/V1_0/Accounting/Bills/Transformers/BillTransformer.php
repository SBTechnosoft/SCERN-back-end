<?php
namespace ERP\Api\V1_0\Accounting\Bills\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Accounting\Bills\Entities\PaymentModeEnum;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Core\Accounting\Bills\Entities\SalesTypeEnum;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
use ERP\Core\Accounting\Bills\Entities\PaymentTransactionEnum;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Accounting\Bills\BillModel;

use ERP\Core\Products\Services\ProductService;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BillTransformer
{
    /**
     * @param Request Object
     * @return array/error message
     */
    public function trimInsertData(Request $request)
    {
		$paymentModeFlag=0;
		$isDisplayFlag=0;
		$billArrayData=array();
		//data get from body
		$billArrayData = $request->input();
		$paymentModeArray = array();
		$paymentModeEnum = new PaymentModeEnum();
		$paymentModeArray = $paymentModeEnum->enumArrays();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		//trim an input
		$tCompanyId = trim($billArrayData['companyId']);
		$tBranchId = array_key_exists('branchId',$billArrayData) ? trim($billArrayData['branchId']) : 0;
		$tEntryDate = trim($billArrayData['entryDate']);
		$tProfessionId = array_key_exists('professionId',$billArrayData)?trim($billArrayData['professionId']):"";
		$tContactNo = array_key_exists('contactNo',$billArrayData)?trim($billArrayData['contactNo']):"";
		$tEmailId = array_key_exists('emailId',$billArrayData)?trim($billArrayData['emailId']):"";
		$tContactNo1 = array_key_exists('contactNo1',$billArrayData)?trim($billArrayData['contactNo1']):"";
		$tCompanyName = array_key_exists('companyName',$billArrayData)?trim($billArrayData['companyName']):"";
		$tJobCardNumber = array_key_exists('jobCardNumber',$billArrayData)?trim($billArrayData['jobCardNumber']):"";
		$tAddress1 = array_key_exists('address1',$billArrayData)?trim($billArrayData['address1']):"";
		$tPoNumber = array_key_exists('poNumber',$billArrayData)?trim($billArrayData['poNumber']):"";
		$tUserId = array_key_exists('userId',$billArrayData)?trim($billArrayData['userId']):"";
		$tServiceDate = array_key_exists('serviceDate',$billArrayData)?trim($billArrayData['serviceDate']):"";
		$tExpense = array_key_exists('expense',$billArrayData)?json_encode($billArrayData['expense']):"";
		$tClientName = trim($billArrayData['clientName']);
		$tInvoiceNumber = trim($billArrayData['invoiceNumber']);
		$tStateAbb = array_key_exists('stateAbb',$billArrayData)? $this->checkStringValue(trim($billArrayData['stateAbb'])):"";
		$tCityId = array_key_exists('cityId',$billArrayData)? $this->checkValue(trim($billArrayData['cityId'])):"";
		$tTotal = trim($billArrayData['total']);
		if(!array_key_exists('totalDiscounttype',$request->input()) && !array_key_exists('totalDiscount',$request->input()))
		{
			$tTotalDiscounttype = 'flat';
			$tTotalDiscount = 0;
		}
		else
		{
			if($billArrayData['totalDiscounttype']=='flat' || $billArrayData['totalDiscounttype']=='percentage')
			{
				$tTotalDiscounttype = trim($billArrayData['totalDiscounttype']);
				$tTotalDiscount = $this->checkValue(trim($billArrayData['totalDiscount']));
			}
			else
			{
				return "1";
			}
		}
		if(!array_key_exists('extraCharge',$request->input()))
		{
			$tExtraCharge = 0;
		}
		else
		{
			$tExtraCharge = $this->checkValue(trim($billArrayData['extraCharge']));
		}

		$tTotalCgstPercentage = array_key_exists('totalCgstPercentage',$billArrayData)? $this->checkValue(trim($billArrayData['totalCgstPercentage'])):0;
		$tTotalSgstPercentage = array_key_exists('totalSgstPercentage',$billArrayData)? $this->checkValue(trim($billArrayData['totalSgstPercentage'])):0;
		$tTotalIgstPercentage = array_key_exists('totalIgstPercentage',$billArrayData)? $this->checkValue(trim($billArrayData['totalIgstPercentage'])):0;

		$tTax = $this->checkValue(trim($billArrayData['tax']));
		$tGrandTotal = $this->checkValue(trim($billArrayData['grandTotal']));
		$tAdvance = $this->checkValue(trim($billArrayData['advance']));
		$tBalance = $this->checkValue(trim($billArrayData['balance']));
		$tPaymentMode = trim($billArrayData['paymentMode']);
		if(strcmp($tPaymentMode,$paymentModeArray['bankPayment'])==0 || 
			strcmp($tPaymentMode,$paymentModeArray['neftPayment'])==0 ||
			strcmp($tPaymentMode,$paymentModeArray['rtgsPayment'])==0 ||
			strcmp($tPaymentMode,$paymentModeArray['impsPayment'])==0 ||
			strcmp($tPaymentMode,$paymentModeArray['nachPayment'])==0 ||
			strcmp($tPaymentMode,$paymentModeArray['achPayment'])==0)
		{
			$tBankName = trim($billArrayData['bankName']);
			$tCheckNumber = trim($billArrayData['checkNumber']);

			$tBankLedgerId = trim($billArrayData['bankLedgerId']);
			//validate cheque number
			$billModel = new BillModel();
			$result = $billModel->validateChequeNo($tCheckNumber);
			if(strcmp($result,$exceptionArray['500'])==0)
			{
				return "1";
			}
		}
		else
		{
			$tBankName="";	
			$tCheckNumber="";
			$tBankLedgerId=0;
			if($tPaymentMode=="")
			{
				$tPaymentMode=$paymentModeArray['cashPayment'];
			}
		}
		$tRemark = array_key_exists("remark",$billArrayData) ? trim($billArrayData['remark']) :"";
		$tIsDisplay = array_key_exists("isDisplay",$billArrayData) ? trim($billArrayData['isDisplay']):"";
		$isDisplayEnum = new IsDisplayEnum();
		$isDisplayArray = $isDisplayEnum->enumArrays();
		if($tIsDisplay=="")
		{
			$tIsDisplay=$isDisplayArray['display'];
		}
		else
		{
			//check is-display enum type
			foreach ($isDisplayArray as $key => $value)
			{
				if(strcmp($value,$tIsDisplay)==0)
				{
					$isDisplayFlag=1;
					break;
				}
			}
			if($isDisplayFlag==0)
			{
				return "1";
			}
		}
		$discountFlag=0;
		$discountTypeEnum = new DiscountTypeEnum();
		$ProductService = new ProductService();
		for($trimInventory=0;$trimInventory<count($billArrayData['inventory']);$trimInventory++)
		{
			$discountTypeArray = array();
			$discountTypeArray = $discountTypeEnum->enumArrays();
			$discountTypeFlag=0;
			//check discount-type enum
			foreach ($discountTypeArray as $key => $value)
			{
				if(strcmp($value,$billArrayData['inventory'][$trimInventory]['discountType'])==0)
				{
					$discountTypeFlag=1;
					break;
				}
			}
			if($discountTypeFlag==0)
			{
				$discountFlag=2;
				break;
			}
			$tInventoryArray[$trimInventory] = array();
			$tInventoryArray[$trimInventory][0] = trim($billArrayData['inventory'][$trimInventory]['productId']);
			$tInventoryArray[$trimInventory][1] = trim($billArrayData['inventory'][$trimInventory]['discount']);
			$tInventoryArray[$trimInventory][2] = trim($billArrayData['inventory'][$trimInventory]['discountType']);
			$tInventoryArray[$trimInventory][3] = $this->checkValue(trim($billArrayData['inventory'][$trimInventory]['price']));
			$tInventoryArray[$trimInventory][4] = trim($billArrayData['inventory'][$trimInventory]['qty']);
			if (array_key_exists('measurementUnit', $billArrayData['inventory'][$trimInventory])) {
				// Get Product Units to tranform Qty into primary unit Qty
				$productTransformData = json_decode($ProductService->getProductData($billArrayData['inventory'][$trimInventory]['productId']));
				$highestMeasurementUnit = $productTransformData->highestMeasurementUnitId;
				$higherMeasurementUnit = $productTransformData->higherMeasurementUnitId;
				$mediumMeasurementUnit = $productTransformData->mediumMeasurementUnitId;
				$mediumLowerMeasurementUnit = $productTransformData->mediumLowerMeasurementUnitId;
				$lowerMeasurementUnit = $productTransformData->lowerMeasurementUnitId;
				$lowestMeasurementUnit = $productTransformData->measurementUnitId;
				$primaryMeasurement = $productTransformData->primaryMeasureUnit;
				$currentQty = trim($billArrayData['inventory'][$trimInventory]['qty']);
				$currentMeasurementUnit = $billArrayData['inventory'][$trimInventory]['measurementUnit'];
				switch ($currentMeasurementUnit) {
					case $highestMeasurementUnit:
							$currentQty = round($currentQty * $productTransformData->highestMouConv);
						break;
					case $higherMeasurementUnit:
							$currentQty = round($currentQty * $productTransformData->higherMouConv);
						break;
					case $mediumMeasurementUnit:
							$currentQty = round($currentQty * $productTransformData->mediumMouConv);
						break;
					case $mediumLowerMeasurementUnit:
							$currentQty = round($currentQty * $productTransformData->mediumLowerMouConv);
						break;
					case $lowerMeasurementUnit:
							$currentQty = round($currentQty * $productTransformData->lowerMouConv);
						break;
					
					default:
							$currentQty = round($currentQty * $productTransformData->lowestMouConv);
						break;
				}
				$tInventoryArray[$trimInventory][4] = $currentQty;
			}
			
		}
		//check paymentmode enum type
		foreach ($paymentModeArray as $key => $value)
		{
			if(strcmp($value,$tPaymentMode)==0)
			{
				$paymentModeFlag=1;
				break;
			}
		}
		
		if($paymentModeFlag==0 || $discountFlag==2)
		{
			return "1";
		}
		else
		{
			// make an array
			$data = array();
			$data['company_id'] = $tCompanyId;
			$data['branch_id'] = $tBranchId;
			$data['profession_id'] = $tProfessionId;
			$data['entry_date'] = $tEntryDate;
			$data['service_date'] = $tServiceDate;
			$data['contact_no'] = $tContactNo;
			$data['contact_no1'] = $tContactNo1;
			$data['email_id'] = $tEmailId;
			$data['is_display'] = $tIsDisplay;
			$data['company_name'] = $tCompanyName;
			$data['client_name'] = $tClientName;
			$data['invoice_number'] = $tInvoiceNumber;
			$data['job_card_number'] = $tJobCardNumber;
			$data['address1'] = $tAddress1;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
			$data['total'] = $tTotal;
			$data['extra_charge'] = $tExtraCharge;
			$data['tax'] = $tTax;
			$data['grand_total'] = $tGrandTotal;
			$data['advance'] = $tAdvance;
			$data['balance'] = $tBalance;
			$data['bank_name'] = $tBankName;
			$data['bank_ledger_id'] = $tBankLedgerId;
			$data['payment_mode'] = $tPaymentMode;
			$data['check_number'] = $tCheckNumber;
			$data['po_number'] = $tPoNumber;
			$data['user_id'] = $tUserId;
			$data['remark'] = $tRemark;
			$data['total_discounttype'] = $tTotalDiscounttype;
			$data['total_discount'] = $tTotalDiscount;
			$data['totalCgstPercentage'] = $tTotalCgstPercentage;
			$data['totalSgstPercentage'] = $tTotalSgstPercentage;
			$data['totalIgstPercentage'] = $tTotalIgstPercentage;
			$data['expense'] = $tExpense;
			$trimArray=array();
			for($inventoryArray=0;$inventoryArray<count($billArrayData['inventory']);$inventoryArray++)
			{
				$trimArray[$inventoryArray]=array(
					'productId' => $tInventoryArray[$inventoryArray][0],
					'discount' => $tInventoryArray[$inventoryArray][1],
					'discountType' => $tInventoryArray[$inventoryArray][2],
					'price' => $tInventoryArray[$inventoryArray][3],
					'qty' => $tInventoryArray[$inventoryArray][4]
				);
			}
			array_push($data,$trimArray);
			return $data;
		}
	}
	
	 /**
	 *  trim data -> conversion date -> make an array of transform data
	 * @param request header
     * @return array/error message
     */
	public function trimFromToDateData($headerData)
	{
		//get date from header and trim data
		$salesType = trim($headerData['salestype'][0]);
		$fromDate = trim($headerData['fromdate'][0]);
		$toDate = trim($headerData['todate'][0]);
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		
		//check enum-type data
		$salesTypeEnum = new SalesTypeEnum();
		$salesTypeArray = $salesTypeEnum->enumArrays();
		if(strcmp($salesType,$salesTypeArray['retailSales'])==0 || strcmp($salesType,$salesTypeArray['wholesales'])==0)
		{
			if(strcmp($fromDate,'00-00-0000')==0)
			{
				$transformFromDate = '0000-00-00';
			}
			else
			{
				//from-date conversion
				$splitedFromDate = explode("-",$fromDate);
				$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			}
			if(strcmp($toDate,'00-00-0000')==0)
			{
				$transformToDate = '0000-00-00';
			}
			else
			{
				//to-date conversion
				$splitedToDate = explode("-",$toDate);
				$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			}

			$branchId = array_key_exists('branchid', $headerData) ? $headerData['branchid'][0] : '';
			
			$trimArray = array();	
			$trimArray['salesType'] = $salesType;	
			$trimArray['fromDate'] = $transformFromDate;	
			$trimArray['toDate'] = $transformToDate;	
			$trimArray['branchId'] = $branchId;	
			return $trimArray;
		}
		else
		{
			return $msgArray['content'];
		}
	}
	
	/**
     * trim data and check enum data type
	 * @param request data 
     * @return array/error message
     */
	public function trimPaymentData(Request $request)
	{
		$paymentModeFlag=0;
		$paymentTrnFlag=0;
		$tEntryDate = trim($request->input()['entryDate']);
		$tAmount = trim($request->input()['amount']);
		$tPaymentTransaction = trim($request->input()['paymentTransaction']);
		$tPaymentMode = trim($request->input()['paymentMode']);
		
		//entry-date conversion
		$splitedEntryDate = explode("-",$tEntryDate);
		$tEntryDate = $splitedEntryDate[2]."-".$splitedEntryDate[1]."-".$splitedEntryDate[0];
		
		$paymentTrnArray = array();
		$paymentTrnEnum = new PaymentTransactionEnum();
		$paymentTrnArray = $paymentTrnEnum->enumArrays();
		
		//check paymentmode enum type
		foreach ($paymentTrnArray as $key => $value)
		{
			if(strcmp($value,$tPaymentTransaction)==0)
			{
				$paymentTrnFlag=1;
				break;
			}
		}
		
		$paymentModeArray = array();
		$paymentModeEnum = new PaymentModeEnum();
		$paymentModeArray = $paymentModeEnum->enumArrays();
		
		//check paymentmode enum type
		foreach ($paymentModeArray as $key => $value)
		{
			if(strcmp($value,$tPaymentMode)==0)
			{
				$paymentModeFlag=1;
				break;
			}
		}
		if($paymentModeFlag==0 || $paymentTrnFlag==0)
		{
			return "1";
		}
		else
		{
			if(strcmp($tPaymentMode,"bank")==0)
			{
				$tBankName = trim($request->input()['bankName']);
				$tCheckNumber = trim($request->input()['checkNumber']);
				$tBankLedgerId = trim($request->input()['bankLedgerId']);
			}
			else
			{
				$tBankName="";	
				$tCheckNumber="";
				$tBankLedgerId="";
				if($tPaymentMode=="")
				{
					$tPaymentMode=$paymentModeArray['cashPayment'];
				}
			}
			$trimArray = array();
			$trimArray['entry_date'] = $tEntryDate;
			$trimArray['amount'] = $tAmount;
			$trimArray['payment_transaction'] = $tPaymentTransaction;
			$trimArray['payment_mode'] = $tPaymentMode;
			$trimArray['bank_name'] = $tBankName;
			$trimArray['check_number'] = $tCheckNumber;
			$trimArray['bank_ledger_id'] = $tBankLedgerId;
			return $trimArray;
		}
	}
	
	/**
     * trim bill update data and check enum data type
	 * @param request data 
     * @return array/error message
     */
	public function trimBillUpdateData(Request $request,$saleId)
	{
		$convertedValue="";
		$dataFlag=0;
		$discountTypeFlag=0;
		$paymentModeFlag = 0;
		$isDisplayFlag = 0;
		$tempArrayFlag=0;
		$tempArray = array();
		$tBillArray = array();
		$billArrayData = $request->input();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$paymentModeEnum = new PaymentModeEnum();
		$paymentModeArray = $paymentModeEnum->enumArrays();
		if(array_key_exists('paymentMode',$billArrayData))
		{
			if(strcmp($billArrayData['paymentMode'],$paymentModeArray['cashPayment'])==0 || strcmp($billArrayData['paymentMode'],$paymentModeArray['creditPayment'])==0)
			{
				$billArrayData['bankName'] = "";
				$billArrayData['checkNumber'] = "";
				$billArrayData['bankLedgerId'] = 0;
			}
		}
		if(array_key_exists('checkNumber',$billArrayData))		
		{
			if ($billArrayData['checkNumber'] != ""){
				//validate cheque number
				$billModel = new BillModel();
				$result = $billModel->validateChequeNo($billArrayData['checkNumber'],$saleId);
				if(strcmp($result,$exceptionArray['500'])==0)
				{
					return $exceptionArray['content'];
				}
			}
		}	
		for($inputArrayData=0;$inputArrayData<count($billArrayData);$inputArrayData++)
		{
			if(strcmp(array_keys($billArrayData)[$inputArrayData],'inventory')==0)
			{
				$enumDiscountTypeArray = array();
				$discountTypeEnum = new DiscountTypeEnum();
				$enumDiscountTypeArray = $discountTypeEnum->enumArrays();
				$ProductService = new ProductService();
				for($inventoryArray=0;$inventoryArray<count($billArrayData['inventory']);$inventoryArray++)
				{
					$tempArrayFlag=1;
					$tempArray[$inventoryArray] = array();
					$tempArray[$inventoryArray]['productId'] = trim($billArrayData['inventory'][$inventoryArray]['productId']);
					$tempArray[$inventoryArray]['discount'] = trim($billArrayData['inventory'][$inventoryArray]['discount']);
					$tempArray[$inventoryArray]['discountType'] = trim($billArrayData['inventory'][$inventoryArray]['discountType']);
					$tempArray[$inventoryArray]['price'] = trim($billArrayData['inventory'][$inventoryArray]['price']);
					$tempArray[$inventoryArray]['qty'] = trim($billArrayData['inventory'][$inventoryArray]['qty']);
					$tempArray[$inventoryArray]['measurementUnit'] = trim($billArrayData['inventory'][$inventoryArray]['measurementUnit']);

					$tempArray[$inventoryArray]['color'] = array_key_exists("color",$billArrayData['inventory'][$inventoryArray]) ? trim($billArrayData['inventory'][$inventoryArray]['color']) : "XX";
					$tempArray[$inventoryArray]['frameNo'] = array_key_exists('frameNo',$billArrayData['inventory'][$inventoryArray]) ? trim($billArrayData['inventory'][$inventoryArray]['frameNo']) : "";
					$tempArray[$inventoryArray]['size'] = array_key_exists("size",$billArrayData['inventory'][$inventoryArray]) ? trim($billArrayData['inventory'][$inventoryArray]['size']) : "ZZ";
					foreach ($enumDiscountTypeArray as $key => $value)
					{
						if(strcmp($value,$tempArray[$inventoryArray]['discountType'])==0)
						{
							$discountTypeFlag=1;
							break;
						}
						else
						{
							$discountTypeFlag=0;
						}
					}
					if($discountTypeFlag==0)
					{
						return $exceptionArray['content'];
					}
				}
			}
			else
			{
				$key = array_keys($billArrayData)[$inputArrayData];
				if(strcmp("scanFile",$key)!=0)
				{
					$dataFlag=1;
					$key = array_keys($billArrayData)[$inputArrayData];
					$value = $billArrayData[$key];
					for($asciiChar=0;$asciiChar<strlen($key);$asciiChar++)
					{
						if(ord($key[$asciiChar])<=90 && ord($key[$asciiChar])>=65) 
						{
							$convertedValue1 = "_".chr(ord($key[$asciiChar])+32);
							$convertedValue=$convertedValue.$convertedValue1;
						}
						else
						{
							$convertedValue=$convertedValue.$key[$asciiChar];
						}
					}
					//check is_display and payment-mode
					//check enum type of payment-mode
					if(strcmp('payment_mode',$convertedValue)==0)
					{
						$paymentModeArray = array();
						$paymentModeEnum = new PaymentModeEnum();
						$paymentModeArray = $paymentModeEnum->enumArrays();
						
						$tBillArray[$convertedValue]=trim($value);
						foreach ($paymentModeArray as $key => $value)
						{
							if(strcmp($value,$tBillArray[$convertedValue])==0)
							{
								$paymentModeFlag=1;
								break;
							}
						}
						if($paymentModeFlag==0)
						{
							return $exceptionArray['content'];
						}
					}
					else if(strcmp('is_display',$convertedValue)==0)
					{
						$isDisplayEnum = new IsDisplayEnum();
						$isDisplayArray = $isDisplayEnum->enumArrays();
						
						$tBillArray[$convertedValue]=trim($value);
						foreach ($isDisplayArray as $key => $value)
						{
							if(strcmp($value,$tBillArray[$convertedValue])==0)
							{
								$isDisplayFlag=1;
								break;
							}
						}
						if($isDisplayFlag==0)
						{
							return $exceptionArray['content'];
						}
					}
					else if(strcmp('expense',$convertedValue)==0)
					{
						$tBillArray[$convertedValue]=json_encode($value);
					}
					else if(strcmp('state_abb',$convertedValue)==0)
					{
						$tBillArray[$convertedValue]=$this->checkStringValue(trim($value));
					}
					else if(strcmp('city_id',$convertedValue)==0)
					{
						$tBillArray[$convertedValue]=$this->checkValue(trim($value));
					}
					else
					{
						$tBillArray[$convertedValue]=trim($value);
					}
					$convertedValue="";
				}
			}
		}
		if($tempArrayFlag==1 && $dataFlag==1)
		{
			$tBillArray['inventory'] = $tempArray;
		}
		else if($tempArrayFlag==1)
		{
			$tBillArray['inventory'] = $tempArray;
		}
		
		return $tBillArray;
	}
	
	/**
	* check value
	* @param integer value
	* @return tax-value/0
	*/
	public function checkValue($tax)
	{
		if($tax=='' || strcmp($tax,'undefined')==0 || is_NaN(floatval($tax)) || $tax==null)
		{
			return 0;
		}
		return $tax;	
	}

	/**
	* check value
	* @param integer value
	* @return tax-value/0
	*/
	public function checkStringValue($string)
	{
		if($string=='' || strcmp($string,'undefined')==0 || $string==null)
		{
			return '';
		}
		return $string;	
	}
}