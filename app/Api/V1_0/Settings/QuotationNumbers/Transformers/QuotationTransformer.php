<?php
namespace ERP\Api\V1_0\Settings\QuotationNumbers\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Settings\QuotationNumbers\Entities\QuotationTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationTransformer
{
    /**
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$quotationTypeFlag=0;
		//data get from body
		$quotationLabel = $request->input('quotationLabel'); 
		$quotationType = $request->input('quotationType'); 
		$startAt = $request->input('startAt'); 
		$endAt = $request->input('endAt'); 
		$companyId = $request->input('companyId');  
		
		//trim an input
		$tQuotationLabel = trim($quotationLabel);
		$tQuotationType = trim($quotationType);
		$tStartAt = trim($startAt);
		$tEndAt = trim($endAt);
		$tCompanyId = trim($companyId);
		if($tQuotationType!="")
		{
			$enumQuotationTypeArray = array();
			$quotationTypeEnum = new QuotationTypeEnum();
			$enumQuotationTypeArray = $quotationTypeEnum->enumArrays();
			foreach ($enumQuotationTypeArray as $key => $value)
			{
				if(strcmp($value,$tQuotationType)==0)
				{
					$quotationTypeFlag=1;
					break;
				}
				else
				{
					$quotationTypeFlag=2;
				}
			}
		}
		if($quotationTypeFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['quotation_label'] = $tQuotationLabel;
			$data['quotation_type'] = $tQuotationType;
			$data['start_at'] = $tStartAt;
			$data['end_at'] = $tEndAt;
			$data['company_id'] = $tCompanyId;
			return $data;
		}
	}
	
	public function trimUpdateData()
	{
		$tQuotationArray = array();
		$quotationValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$quotationTypeEnumArray = array();
		$quotationTypeFlag=0;
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
		$quotationValue = func_get_arg(1);
		for($data=0;$data<count($quotationValue);$data++)
		{
			$tQuotationArray[$data]= array($convertedValue=> trim($quotationValue));
			$quotationTypeEnumArray = array_keys($tQuotationArray[$data])[0];
		}
		$enumQuotationTypeArray = array();
		$quotationTypeEnum = new QuotationTypeEnum();
		$enumQuotationTypeArray = $quotationTypeEnum->enumArrays();
		if(strcmp($quotationTypeEnumArray,'quotation_type')==0)
		{
			foreach ($enumQuotationTypeArray as $key => $value)
			{
				if(strcmp($tQuotationArray[0]['quotation_type'],$value)==0)
				{
					$quotationTypeFlag=1;
					break;
				}
				else
				{
					$quotationTypeFlag=2;
				}
			}
		}
		
		if($quotationTypeFlag==2)
		{
			return "1";
		}
		else
		{
			return $tQuotationArray;
		}
	}
}