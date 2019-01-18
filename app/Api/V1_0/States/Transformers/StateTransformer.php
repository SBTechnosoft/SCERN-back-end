<?php
namespace ERP\Api\V1_0\States\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class StateTransformer
{
   /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		$stateName = $request->input('stateName'); 
		$stateAbb = $request->input('stateAbb'); 
		$isDisplay = $request->input('isDisplay'); 
		$stateCode = $request->input('stateCode'); 
		//trim an input
		$tStateName = trim($stateName);
		$tStateAbb = trim($stateAbb);
		$tIsDisplay = trim($isDisplay);
		$tStateCode = trim($stateCode);
		
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
		if($isDisplayFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['state_name'] = $tStateName;
			$data['state_abb'] = $tStateAbb;
			$data['is_display'] = $tIsDisplay;
			$data['state_code'] = $tStateCode;
			return $data;
		}
	}
	
	/**
     * @param key and value
     * @return array
     */
	public function trimUpdateData()
	{
		$isDisplayFlag=0;
		$tStateArray = array();
		$stateValue;
		$convertedValue="";
		$keyValue = func_get_arg(0);
		$stateEnumArray = array();
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
		$stateValue = func_get_arg(1);
		for($data=0;$data<count($stateValue);$data++)
		{
			$tStateArray[$data]= array($convertedValue=> trim($stateValue));
			$stateEnumArray = array_keys($tStateArray[$data])[0];
		}
		
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($stateEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tStateArray[0]['is_display'],$value)==0)
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
		if($isDisplayFlag==2)
		{
			return "1";
		}
		else
		{
			return $tStateArray;
		}
	}
}