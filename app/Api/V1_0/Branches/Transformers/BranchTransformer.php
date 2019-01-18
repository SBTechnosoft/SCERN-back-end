<?php
namespace ERP\Api\V1_0\Branches\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Entities\EnumClasses\IsDefaultEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BranchTransformer
{
    /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		$isDefaultFlag=0;
		//data get from body
		$branchName = $request->input('branchName'); 
		$address1 = $request->input('address1'); 
		$address2 = $request->input('address2'); 
		$pincode = $request->input('pincode'); 
		$isDisplay = $request->input('isDisplay'); 			
		$isDefault = $request->input('isDefault'); 			
		$stateAbb = $request->input('stateAbb'); 			
		$cityId = $request->input('cityId'); 			
		$companyId = $request->input('companyId');  
		
		//trim an input
		$tBranchName = trim($branchName);
		$tAddress1 = trim($address1);
		$tAddress2 = trim($address2);
		$tPincode = trim($pincode);
		$tIsDisplay = trim($isDisplay);
		$tIsDefault = trim($isDefault);
		$tStateAbb = trim($stateAbb);
		$tCityId = trim($cityId);
		$tCompanyId = trim($companyId);
		
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
		if($isDisplayFlag==2 || $isDefaultFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['branch_name'] = $tBranchName;
			$data['address1'] = $tAddress1;
			$data['address2'] = $tAddress2;
			$data['pincode'] = $tPincode;
			$data['is_display'] = $tIsDisplay;
			$data['is_default'] = $tIsDefault;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
			$data['company_id'] = $tCompanyId;
			return $data;
		}
	}
	
	/**
     * @param array of key and value of request data
     * @return array
     */
	public function trimUpdateData()
	{
		$tBranchArray = array();
		$branchValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$branchEnumArray = array();
		$isDisplayFlag=0;
		$isDefaultFlag=0;
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
		
		$branchValue = func_get_arg(1);
		for($data=0;$data<count($branchValue);$data++)
		{
			$tBranchArray[$data]= array($convertedValue=> trim($branchValue));
			$branchEnumArray = array_keys($tBranchArray[$data])[0];
		}
		
		$enumIsDefArray = array();
		$isDefEnum = new IsDefaultEnum();
		$enumIsDefArray = $isDefEnum->enumArrays();
		
		if(strcmp($branchEnumArray,'is_default')==0)
		{
			foreach ($enumIsDefArray as $key => $value)
			{
				if(strcmp($tBranchArray[0]['is_default'],$value)==0)
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
		if(strcmp($branchEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tBranchArray[0]['is_display'],$value)==0)
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
			return $tBranchArray;
		}
	}
}