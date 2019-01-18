<?php
namespace ERP\Api\V1_0\Settings\InvoiceNumbers\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Settings\InvoiceNumbers\Entities\InvoiceTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InvoiceTransformer
{
    /**
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$invoiceTypeFlag=0;
		//data get from body
		$invoiceLabel = $request->input('invoiceLabel'); 
		$invoiceType = $request->input('invoiceType'); 
		$startAt = $request->input('startAt'); 
		$endAt = $request->input('endAt'); 
		$companyId = $request->input('companyId');  
		
		//trim an input
		$tInvoiceLabel = trim($invoiceLabel);
		$tInvoiceType = trim($invoiceType);
		$tStartAt = trim($startAt);
		$tEndAt = trim($endAt);
		$tCompanyId = trim($companyId);
		if($tInvoiceType!="")
		{
			$enumInvoiceTypeArray = array();
			$invoiceTypeEnum = new InvoiceTypeEnum();
			$enumInvoiceTypeArray = $invoiceTypeEnum->enumArrays();
			foreach ($enumInvoiceTypeArray as $key => $value)
			{
				if(strcmp($value,$tInvoiceType)==0)
				{
					$invoiceTypeFlag=1;
					break;
				}
				else
				{
					$invoiceTypeFlag=2;
				}
			}
		}
		if($invoiceTypeFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['invoice_label'] = $tInvoiceLabel;
			$data['invoice_type'] = $tInvoiceType;
			$data['start_at'] = $tStartAt;
			$data['end_at'] = $tEndAt;
			$data['company_id'] = $tCompanyId;
			return $data;
		}
	}
	
	public function trimUpdateData()
	{
		$tInvoiceArray = array();
		$invoiceValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$invoiceTypeEnumArray = array();
		$invoiceTypeFlag=0;
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
		$invoiceValue = func_get_arg(1);
		for($data=0;$data<count($invoiceValue);$data++)
		{
			$tInvoiceArray[$data]= array($convertedValue=> trim($invoiceValue));
			$invoiceTypeEnumArray = array_keys($tInvoiceArray[$data])[0];
		}
		$enumInvoiceTypeArray = array();
		$invoiceTypeEnum = new InvoiceTypeEnum();
		$enumInvoiceTypeArray = $invoiceTypeEnum->enumArrays();
		if(strcmp($invoiceTypeEnumArray,'invoice_type')==0)
		{
			foreach ($enumInvoiceTypeArray as $key => $value)
			{
				if(strcmp($tInvoiceArray[0]['invoice_type'],$value)==0)
				{
					$invoiceTypeFlag=1;
					break;
				}
				else
				{
					$invoiceTypeFlag=2;
				}
			}
		}
		if($invoiceTypeFlag==2)
		{
			return "1";
		}
		else
		{
			return $tInvoiceArray;
		}
	}
}