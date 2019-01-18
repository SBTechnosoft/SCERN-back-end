<?php
namespace ERP\Api\V1_0\Crm\JobForm\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Crm\JobForm\Entities\ServiceTypeEnum;
use ERP\Core\Accounting\Bills\Entities\PaymentModeEnum;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormTransformer
{
    /**
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$serviceTypeFlag=0;
		$paymentModeFlag=0;
		
		// data get from body and trim an input
		$tClientName = trim($request->input('clientName'));
		$tAddress = trim($request->input('address'));
		$tContactNo = trim($request->input('contactNo'));
		$tEmailId = trim($request->input('emailId'));
		$tJobCardNo = trim($request->input('jobCardNumber'));
		$tLabourCharge = trim($request->input('labourCharge'));
		$tServiceType = trim($request->input('serviceType'));
		$tEntryDate = trim($request->input('entryDate'));
		$tDeliveryDate = trim($request->input('deliveryDate'));
		$tAdvance = trim($request->input('advance'));
		$tTotal = trim($request->input('total'));
		$tTax = trim($request->input('tax'));
		$tPaymentMode = trim($request->input('paymentMode'));
		$tStateAbb = trim($request->input('stateAbb'));
		$tCityId = trim($request->input('cityId'));
		$tCompanyId = trim($request->input('companyId'));
		
		if(strcmp($tPaymentMode,'bank')==0)
		{
			$tBankName = trim($request->input('bankName'));
			$tChequeNo = trim($request->input('chequeNo'));
		}
		else
		{
			$tBankName = "";
			$tChequeNo = "";
		}
		$enumServiceTypeArray = array();
		$serviceTypeEnum = new ServiceTypeEnum();
		$enumServiceTypeArray = $serviceTypeEnum->enumArrays();
		
		foreach ($enumServiceTypeArray as $key => $value)
		{
			if(strcmp($value,$tServiceType)==0)
			{
				$serviceTypeFlag=1;
				break;
			}
			else
			{
				$serviceTypeFlag=2;
			}
		}
		$paymentModeArray = array();
		$paymentModeEnum = new PaymentModeEnum();
		$paymentModeArray = $paymentModeEnum->enumArrays();
		foreach ($paymentModeArray as $key => $value)
		{
			if(strcmp($value,$tPaymentMode)==0)
			{
				$paymentModeFlag=1;
				break;
			}
			else
			{
				$paymentModeFlag=2;
			}
		}
		//entry date conversion
		$splitedDate = explode("-",$tEntryDate);
		$transformEntryDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		
		//delivery date conversion
		$splitedDeliveryDate = explode("-",$tDeliveryDate);
		$transformDeliveryDate = $splitedDeliveryDate[2]."-".$splitedDeliveryDate[1]."-".$splitedDeliveryDate[0];
		
		$numberOfArray = count($request->input('product'));
		$tempArray = array();
		for($arrayData=0;$arrayData<$numberOfArray;$arrayData++)
		{
			$discountTypeFlag=0;
			$tempArray[$arrayData] = array();
			$tempArray[$arrayData][0] = trim($request->input()['product'][$arrayData]['productId']);
			$tempArray[$arrayData][1] = trim($request->input()['product'][$arrayData]['productName']);
			$tempArray[$arrayData][2] = trim($request->input()['product'][$arrayData]['productInformation']);
			$tempArray[$arrayData][3] = trim($request->input()['product'][$arrayData]['qty']);
			$tempArray[$arrayData][4] = trim($request->input()['product'][$arrayData]['price']);
			$tempArray[$arrayData][5] = trim($request->input()['product'][$arrayData]['discountType']);
			$tempArray[$arrayData][6] = trim($request->input()['product'][$arrayData]['discount']);
			
			//check enum type[amount-type]
			$enumDiscountTypeArray = array();
			$discountTypeEnum = new DiscountTypeEnum();
			$enumDiscountTypeArray = $discountTypeEnum->enumArrays();
			foreach ($enumDiscountTypeArray as $key => $value)
			{
				if(strcmp($value,$tempArray[$arrayData][5])==0)
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
				return "1";
			}
		}
		if($serviceTypeFlag==2 || $paymentModeFlag==2)
		{
			return "1";
		}
		else
		{
			// make an array
			$data = array();
			$data['clientName'] = $tClientName;
			$data['address'] = $tAddress;
			$data['contactNo'] = $tContactNo;
			$data['emailId'] = $tEmailId;
			$data['jobCardNo'] = $tJobCardNo;
			$data['labourCharge'] = $tLabourCharge;
			$data['serviceType'] = $tServiceType;
			$data['entryDate'] = $transformEntryDate;
			$data['deliveryDate'] = $transformDeliveryDate;
			$data['advance'] = $tAdvance;
			$data['total'] = $tTotal;
			$data['tax'] = $tTax;
			$data['paymentMode'] = $tPaymentMode;
			$data['stateAbb'] = $tStateAbb;
			$data['cityId'] = $tCityId;
			$data['companyId'] = $tCompanyId;
			$data['bankName'] = $tBankName;
			$data['chequeNo'] = $tChequeNo;
			$trimArray = array();
			for($arrayData=0;$arrayData<$numberOfArray;$arrayData++)
			{
				$trimArray[$arrayData]= array(
					'productId' => $tempArray[$arrayData][0],
					'productName' => $tempArray[$arrayData][1],
					'productInformation' => $tempArray[$arrayData][2],
					'qty' => $tempArray[$arrayData][3],
					'price' => $tempArray[$arrayData][4],
					'discountType' => $tempArray[$arrayData][5],
					'discount' => $tempArray[$arrayData][6]
				);
			}
			array_push($data,$trimArray);
			return $data;
		}
	}
	
	public function trimUpdateData()
	{
		$isDisplayFlag=0;
		$isDefaultFlag=0;
		$tCompanyArray = array();
		$companyEnumArray = array();
		$companyValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		for($asciiChar=0;$asciiChar<strlen($keyValue);$asciiChar++)
		{
			if(ord($keyValue[$asciiChar])<=90 && ord($keyValue[$asciiChar])>=65) 
			{
				$convertedValue1 = "_".chr(ord($keyValue[$asciiChar])+32);
				$convertedValue=$convertedValue.$convertedValue1;
			}
			else
			{
				$convertedValue=$convertedValue.$keyValue[$asciiChar];
			}
		}
		$companyValue = func_get_arg(1);
		for($data=0;$data<count($companyValue);$data++)
		{
			$tCompanyArray[$data]= array($convertedValue=> trim($companyValue));
			$companyEnumArray = array_keys($tCompanyArray[$data])[0];
		}
		
		$enumIsDefArray = array();
		$isDefEnum = new IsDefaultEnum();
		$enumIsDefArray = $isDefEnum->enumArrays();
		if(strcmp($companyEnumArray,'is_default')==0)
		{
			foreach ($enumIsDefArray as $key => $value)
			{
				if(strcmp($tCompanyArray[0]['is_default'],$value)==0)
				{
					$isDefaultFlag=1;
					break;
				}
				else
				{
					$isDefaultFlag=2;
				}
			}
		}
		
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($companyEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tCompanyArray[0]['is_display'],$value)==0)
				{
					$isDisplayFlag=1;
					break;
				}
				else
				{
					$isDisplayFlag=2;
				}
			}
		}
		if($isDisplayFlag==2 || $isDefaultFlag==2)
		{
			return "1";
		}
		else
		{
			return $tCompanyArray;
		}
	}
}