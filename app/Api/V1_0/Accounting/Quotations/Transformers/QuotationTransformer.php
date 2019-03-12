<?php
namespace ERP\Api\V1_0\Accounting\Quotations\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use  ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
use ERP\Exceptions\ExceptionMessage;
use Carbon;
use ERP\Core\Accounting\Bills\Entities\PaymentModeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationTransformer
{
    /**
     * @param Request Object
     * @return array/error message
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		$quotationArrayData=array();
		//data get from body
		$quotationArrayData = $request->input(); 
		$poNumber='';
		$tPaymentMode='';
		$tBankName='';
		$tCheckNumber='';
		$invoiceNumber='';
		$paymentModeArray = array();
		$paymentModeEnum = new PaymentModeEnum();
		$paymentModeArray = $paymentModeEnum->enumArrays();
		//trim an input
		$poNumber = array_key_exists('poNumber',$quotationArrayData) ? trim($quotationArrayData['poNumber']):"";
		if(array_key_exists('paymentMode',$quotationArrayData))
		{
			$tPaymentMode = trim($quotationArrayData['paymentMode']);
			if(strcmp($tPaymentMode,$paymentModeArray['bankPayment'])==0 || 
				strcmp($tPaymentMode,$paymentModeArray['neftPayment'])==0 ||
				strcmp($tPaymentMode,$paymentModeArray['rtgsPayment'])==0 ||
				strcmp($tPaymentMode,$paymentModeArray['impsPayment'])==0 ||
				strcmp($tPaymentMode,$paymentModeArray['nachPayment'])==0 ||
				strcmp($tPaymentMode,$paymentModeArray['achPayment'])==0)
			{
				$tBankName = trim($quotationArrayData['bankName']);
				$tCheckNumber = trim($quotationArrayData['checkNumber']);
			}
			else
			{
				$tBankName='';
				$tCheckNumber='';
			}
		}
		else
		{
			$tPaymentMode='';
		}

		$invoiceNumber = array_key_exists('invoiceNumber',$quotationArrayData)?trim($quotationArrayData['invoiceNumber']):"";
		$tCompanyId = trim($quotationArrayData['companyId']);
		$tBranchId = array_key_exists('branchId',$quotationArrayData) ? trim($quotationArrayData['branchId']):0;

		$tEntryDate = trim($quotationArrayData['entryDate']);
		if(!array_key_exists('professionId',$request->input()))
		{
			$tProfessionId = "";
		}
		else
		{
			$tProfessionId = trim($quotationArrayData['professionId']);
		}
		if(!array_key_exists('contactNo',$request->input()))
		{
			$tContactNo = "";
		}
		else
		{
			$tContactNo = trim($quotationArrayData['contactNo']);
		}

		$tContactNo1 = array_key_exists('contactNo1',$request->input())?trim($quotationArrayData['contactNo1']) :'';
		if(!array_key_exists('emailId',$request->input()))
		{
			$tEmailId = "";
		}
		else
		{
			$tEmailId = trim($quotationArrayData['emailId']);
		}
		if(!array_key_exists('companyName',$request->input()))
		{
			$tCompanyName = "";
		}
		else
		{
			$tCompanyName = trim($quotationArrayData['companyName']);
		}
		$tClientName = trim($quotationArrayData['clientName']);
		if(array_key_exists("quotationNumber",$quotationArrayData))
		{
			$tQuotationNumber = trim($quotationArrayData['quotationNumber']);
		}
		else
		{
			$tQuotationNumber='';
		}
		if(!array_key_exists('address1',$request->input()))
		{
			$tAddress1 = "";
		}
		else
		{
			$tAddress1 = trim($quotationArrayData['address1']);
		}
		$tStateAbb = trim($quotationArrayData['stateAbb']);
		$tCityId = trim($quotationArrayData['cityId']);
		$tTotal = trim($quotationArrayData['total']);
		if(!array_key_exists('totalDiscounttype',$request->input()) && !array_key_exists('totalDiscount',$request->input()))
		{
			$tTotalDiscounttype = 'flat';
			$tTotalDiscount = 0;
		}
		else
		{
			if($quotationArrayData['totalDiscounttype']=='flat' || $quotationArrayData['totalDiscounttype']=='percentage')
			{
				$tTotalDiscounttype = trim($quotationArrayData['totalDiscounttype']);
				$tTotalDiscount = trim($quotationArrayData['totalDiscount']);
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
			$tExtraCharge = trim($quotationArrayData['extraCharge']);
		}

		$tTotalCgstPercentage = array_key_exists('totalCgstPercentage',$quotationArrayData)? $this->checkValue(trim($quotationArrayData['totalCgstPercentage'])):0;
		$tTotalSgstPercentage = array_key_exists('totalSgstPercentage',$quotationArrayData)? $this->checkValue(trim($quotationArrayData['totalSgstPercentage'])):0;
		$tTotalIgstPercentage = array_key_exists('totalIgstPercentage',$quotationArrayData)? $this->checkValue(trim($quotationArrayData['totalIgstPercentage'])):0;
		
		$tTax = trim($quotationArrayData['tax']);
		
		if(array_key_exists("grandTotal",$quotationArrayData))
		{
			$tGrandTotal = trim($quotationArrayData['grandTotal']);
		}
		else
		{
			$tGrandTotal =0;
		}
	
		if(array_key_exists("remark",$quotationArrayData))
		{
			$tRemark = trim($quotationArrayData['remark']);
		}
		else
		{
			$tRemark ="";
		}
		if(array_key_exists('isDisplay',$request->input()))
		{
			$tIsDisplay = trim($quotationArrayData['isDisplay']);
		}
		else
		{
			$tIsDisplay="yes";
		}
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
		for($trimInventory=0;$trimInventory<count($quotationArrayData['inventory']);$trimInventory++)
		{
			$discountTypeArray = array();
			$discountTypeArray = $discountTypeEnum->enumArrays();
			$discountTypeFlag=0;
			//check discount-type enum
			foreach ($discountTypeArray as $key => $value)
			{
				if(strcmp($value,$quotationArrayData['inventory'][$trimInventory]['discountType'])==0)
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
			$tInventoryArray[$trimInventory][0] = trim($quotationArrayData['inventory'][$trimInventory]['productId']);
			$tInventoryArray[$trimInventory][1] = trim($quotationArrayData['inventory'][$trimInventory]['discount']);
			$tInventoryArray[$trimInventory][2] = trim($quotationArrayData['inventory'][$trimInventory]['discountType']);
			$tInventoryArray[$trimInventory][3] = trim($quotationArrayData['inventory'][$trimInventory]['price']);
			$tInventoryArray[$trimInventory][4] = trim($quotationArrayData['inventory'][$trimInventory]['qty']);
		}
		
		if($discountFlag==2)
		{
			return "1";
		}
		else
		{
			// make an array
			$data = array();
			$data['company_id'] = $tCompanyId;
			$data['branch_id'] = $tBranchId;
			$data['entry_date'] = $tEntryDate;
			$data['contact_no'] = $tContactNo;
			$data['contact_no1'] = $tContactNo1;
			$data['email_id'] = $tEmailId;
			$data['is_display'] = $tIsDisplay;
			$data['company_name'] = $tCompanyName;
			$data['client_name'] = $tClientName;
			$data['profession_id'] = $tProfessionId;
			$data['quotation_number'] = $tQuotationNumber;
			$data['address1'] = $tAddress1;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
			$data['total'] = $tTotal;
			$data['total_discounttype'] = $tTotalDiscounttype;
			$data['total_discount'] = $tTotalDiscount;
			$data['totalCgstPercentage'] = $tTotalCgstPercentage;
			$data['totalSgstPercentage'] = $tTotalSgstPercentage;
			$data['totalIgstPercentage'] = $tTotalIgstPercentage;
			$data['extra_charge'] = $tExtraCharge;
			$data['tax'] = $tTax;
			$data['grand_total'] = $tGrandTotal;
			$data['remark'] = $tRemark;
			$data['po_number'] = $poNumber;
			$data['payment_mode'] = $tPaymentMode;
			$data['invoice_number'] = $invoiceNumber;
			$data['bank_name'] = $tBankName;
			$data['check_number'] = $tCheckNumber;
			$trimArray=array();
			for($inventoryArray=0;$inventoryArray<count($quotationArrayData['inventory']);$inventoryArray++)
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
     * trim quotation update data and check enum data type
	 * @param request data 
     * @return array/error message
     */
	public function trimQuotationUpdateData(Request $request)
	{
		$convertedValue="";
		$dataFlag=0;
		$discountTypeFlag=0;
		$isDisplayFlag = 0;
		$tempArrayFlag=0;
		$tempArray = array();
		$tQuotationArray = array();
		$quotationArrayData = array_except($request->input(),['workflowStatus','assignedTo','assignedBy']);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();		
		for($inputArrayData=0;$inputArrayData<count($quotationArrayData);$inputArrayData++)
		{
			if(strcmp(array_keys($quotationArrayData)[$inputArrayData],'inventory')==0)
			{
				$enumDiscountTypeArray = array();
				$discountTypeEnum = new DiscountTypeEnum();
				$enumDiscountTypeArray = $discountTypeEnum->enumArrays();
				for($inventoryArray=0;$inventoryArray<count($quotationArrayData['inventory']);$inventoryArray++)
				{
					$tempArrayFlag=1;
					$tempArray[$inventoryArray] = array();
					$tempArray[$inventoryArray]['productId'] = trim($quotationArrayData['inventory'][$inventoryArray]['productId']);
					$tempArray[$inventoryArray]['discount'] = trim($quotationArrayData['inventory'][$inventoryArray]['discount']);
					$tempArray[$inventoryArray]['discountType'] = trim($quotationArrayData['inventory'][$inventoryArray]['discountType']);
					$tempArray[$inventoryArray]['price'] = trim($quotationArrayData['inventory'][$inventoryArray]['price']);
					$tempArray[$inventoryArray]['qty'] = trim($quotationArrayData['inventory'][$inventoryArray]['qty']);

					$tempArray[$inventoryArray]['color'] = array_key_exists("color",$quotationArrayData['inventory'][$inventoryArray]) ? trim($quotationArrayData['inventory'][$inventoryArray]['color']) : "XX";
					$tempArray[$inventoryArray]['frameNo'] = array_key_exists("frameNo",$quotationArrayData['inventory'][$inventoryArray]) ? trim($quotationArrayData['inventory'][$inventoryArray]['frameNo']) : "";
					$tempArray[$inventoryArray]['size'] = array_key_exists("size",$quotationArrayData['inventory'][$inventoryArray]) ? trim($quotationArrayData['inventory'][$inventoryArray]['size']) : "ZZ";
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
				$dataFlag=1;
				$key = array_keys($quotationArrayData)[$inputArrayData];
				$value = $quotationArrayData[$key];
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
				if(strcmp('is_display',$convertedValue)==0)
				{
					$isDisplayEnum = new IsDisplayEnum();
					$isDisplayArray = $isDisplayEnum->enumArrays();
					$tQuotationArray[$convertedValue]=trim($value);
					foreach ($isDisplayArray as $key => $value)
					{
						if(strcmp($value,$tQuotationArray[$convertedValue])==0)
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
				else
				{
					if(strcmp($convertedValue,'entry_date')==0)
					{
						//entry date conversion
						$value = Carbon\Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
					}
					$tQuotationArray[$convertedValue]=trim($value);
				}
				$convertedValue="";
			}
		}
		if($tempArrayFlag==1 && $dataFlag==1)
		{
			$tQuotationArray['inventory'] = $tempArray;
		}
		else if($tempArrayFlag==1)
		{
			$tQuotationArray['inventory'] = $tempArray;
		}
		
		return $tQuotationArray;
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