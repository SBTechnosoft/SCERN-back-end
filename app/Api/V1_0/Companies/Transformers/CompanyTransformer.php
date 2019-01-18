<?php
namespace ERP\Api\V1_0\Companies\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Entities\EnumClasses\IsDefaultEnum;
use ERP\Core\Companies\Entities\PrintEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyTransformer
{
    /**
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		$isDefaultFlag=0;
		$printFlag=0;
		//data get from body
		$companyName = $request->input('companyName'); 
		$companyDispName = $request->input('companyDisplayName'); 
		$websiteName = $request->input('websiteName');
		$address1 = $request->input('address1'); 
		$address2 = $request->input('address2'); 
		$emailId = $request->input('emailId'); 
		$customerCare = $request->input('customerCare'); 
		$pincode = $request->input('pincode'); 
		$pan = $request->input('pan'); 
		$tin = $request->input('tin'); 
		$vatNo = $request->input('vatNo'); 
		$sgst = $request->input('sgst'); 
		$cgst = $request->input('cgst'); 
		$cess = $request->input('cess'); 
		$serviceTaxNo = $request->input('serviceTaxNo'); 
		$basicCurrencySymbol = $request->input('basicCurrencySymbol'); 			
		$formalName = $request->input('formalName'); 			
		$noOfDecimalPoints = $request->input('noOfDecimalPoints'); 			
		$currencySymbol = $request->input('currencySymbol'); 			
		$isDisplay = $request->input('isDisplay'); 			
		$isDefault = $request->input('isDefault'); 			
		$printType = $request->input('printType'); 			
		$stateAbb = $request->input('stateAbb'); 			
		$cityId = $request->input('cityId');  
		//trim an input
		$tCompanyName = trim($companyName);
		$tCompanyDispName = trim($companyDispName);
		$tWebsiteName = trim($websiteName);
		$tAddress1 = trim($address1);
		$tAddress2 = trim($address2);
		$tEmailId = trim($emailId);
		$tCustomerCare = trim($customerCare);
		$tPincode = trim($pincode);
		$tPan = trim($pan);
		$tTin = trim($tin);
		$tVatNo = trim($vatNo);
		$tSgst = trim($sgst);
		$tCgst = trim($cgst);
		$tCess = trim($cess);
		$tServiceTaxNo = trim($serviceTaxNo);
		$tBasicCurrencySymbol = trim($basicCurrencySymbol);
		$tFormalName = trim($formalName);
		$tNoOfDecimalPoints = trim($noOfDecimalPoints);
		$tCurrencySymbol = trim($currencySymbol);
		$tIsDisplay = trim($isDisplay);
		$tIsDefault = trim($isDefault);
		$tPrintType = trim($printType);
		$tStateAbb = trim($stateAbb);
		$tCityId = trim($cityId);
		
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if($tIsDisplay=="")
		{
			$tIsDisplay=$enumIsDispArray['display'];
		}
		else
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($value,$tIsDisplay)==0)
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
		$enumIsDefArray = array();
		$isDefEnum = new IsDefaultEnum();
		$enumIsDefArray = $isDefEnum->enumArrays();
		if($tIsDefault=="")
		{
			$tIsDefault=$enumIsDefArray['notDefault'];
		}
		else
		{
			foreach ($enumIsDefArray as $key => $value)
			{
				if(strcmp($value,$tIsDefault)==0)
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
		$printEnum = new PrintEnum();
		$printEnumArray = $printEnum->enumArrays();
		if($tPrintType=="")
		{
			$tPrintType=$printEnumArray['print'];
		}
		else
		{
			foreach ($printEnumArray as $key => $value)
			{
				if(strcmp($value,$tPrintType)==0)
				{
					$printFlag=1;
					break;
				}
				else
				{
					$printFlag=2;
				}
			}
		}
		if($isDisplayFlag==2 || $isDefaultFlag==2 || $printFlag==2)
		{
			return "1";
		}
		else
		{
			// make an array
			$data = array();
			$data['company_name'] = $tCompanyName;
			$data['company_display_name'] = $tCompanyDispName;
			$data['website_name'] = $tWebsiteName;
			$data['address1'] = $tAddress1;
			$data['address2'] = $tAddress2;
			$data['email_id'] = $tEmailId;
			$data['customer_care'] = $tCustomerCare;
			$data['pincode'] = $tPincode;
			$data['pan'] = $tPan;
			$data['tin'] = $tTin;
			$data['vat_no'] = $tVatNo;
			$data['sgst'] = $tSgst;
			$data['cgst'] = $tCgst;
			$data['cess'] = $tCess;
			$data['service_tax_no'] = $tServiceTaxNo;
			$data['basic_currency_symbol'] = $tBasicCurrencySymbol;
			$data['formal_name'] = $tFormalName;
			$data['no_of_decimal_points'] = $tNoOfDecimalPoints;
			$data['currency_symbol'] = $tCurrencySymbol;
			$data['is_display'] = $tIsDisplay;
			$data['is_default'] = $tIsDefault;
			$data['print_type'] = $tPrintType;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
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