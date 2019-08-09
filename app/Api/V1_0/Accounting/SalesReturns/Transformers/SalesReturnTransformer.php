<?php
namespace ERP\Api\V1_0\Accounting\SalesReturns\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Accounting\Bills\Entities\PaymentModeEnum;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Core\Products\Services\ProductService;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class SalesReturnTransformer
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
		$tInvoiceNumber = trim($billArrayData['invoiceNumber']);
		$tSaleId = trim($billArrayData['saleId']);
		$tBranchId = array_key_exists('branchId',$billArrayData) ? trim($billArrayData['branchId']) : 0;
		$tEntryDate = trim($billArrayData['entryDate']);
		$tContactNo = array_key_exists('contactNo',$billArrayData)?trim($billArrayData['contactNo']):"";
		$tClientId = array_key_exists('clientId',$billArrayData)?trim($billArrayData['clientId']):"";
		$tExpense = array_key_exists('expense',$billArrayData)?json_encode($billArrayData['expense']):"";
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
			$data['sale_id'] = $tSaleId;
			$data['invoice_number'] = $tInvoiceNumber;
			$data['entry_date'] = $tEntryDate;
			$data['contact_no'] = $tContactNo;
			$data['client_id'] = $tClientId;
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
			$data['remark'] = $tRemark;
			$data['total_discounttype'] = $tTotalDiscounttype;
			$data['total_discount'] = $tTotalDiscount;
			$data['total_cgst_percentage'] = $tTotalCgstPercentage;
			$data['total_sgst_percentage'] = $tTotalSgstPercentage;
			$data['total_igst_percentage'] = $tTotalIgstPercentage;
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