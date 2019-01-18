<?php
namespace ERP\Api\V1_0\Crm\JobFormNumber\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Crm\JobFormNumber\Entities\JobFormTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumberTransformer
{
    /**
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$jobFormTypeFlag=0;
		
		//trim an input
		$tLabel = trim($request->input('jobCardNumberLabel'));
		$tType = trim($request->input('jobCardNumberType'));
		$tStartAt = trim($request->input('startAt'));
		$tEndAt = trim($request->input('endAt'));
		$tCompanyId = trim($request->input('companyId'));
		
		$enumJobFormArray = array();
		$jobFormEnum = new JobFormTypeEnum();
		$enumJobFormArray = $jobFormEnum->enumArrays();
		
		foreach ($enumJobFormArray as $key => $value)
		{
			if(strcmp($value,$tType)==0)
			{
				$jobFormTypeFlag=1;
				break;
			}
			else
			{
				$jobFormTypeFlag=2;
			}
		}
		
		if($jobFormTypeFlag==2)
		{
			return "1";
		}
		else
		{
			// make an array
			$data = array();
			$data['job_card_number_label'] = $tLabel;
			$data['job_card_number_type'] = $tType;
			$data['start_at'] = $tStartAt;
			$data['end_at'] = $tEndAt;
			$data['company_id'] = $tCompanyId;
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