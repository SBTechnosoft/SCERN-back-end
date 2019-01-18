<?php
namespace ERP\Api\V1_0\Cities\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityTransformer
{
   /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		//data get from body
		$cityName = $request->input('cityName'); 
		$stateAbb = $request->input('stateAbb'); 
		$isDisplay = $request->input('isDisplay');
		
		//trim an input
		$tCityName = trim($cityName);
		$tStateAbb = trim($stateAbb);
		$tIsDisplay = trim($isDisplay);
		
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
			$data['city_name'] = $tCityName;
			$data['state_abb'] = $tStateAbb;
			$data['is_display'] = $tIsDisplay;
			return $data;
		}
	}
	
	/**
     * @param key and value
     * @return array
     */
	public function trimUpdateData()
	{
		$tCityArray = array();
		$cityValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$cityEnumArray = array();
		$isDisplayFlag=0;
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
		
		$cityValue = func_get_arg(1);
		
		for($data=0;$data<count($cityValue);$data++)
		{
			$tCityArray[$data]= array($convertedValue=> trim($cityValue));
			$cityEnumArray = array_keys($tCityArray[$data])[0];
		}
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($cityEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tCityArray[0]['is_display'],$value)==0)
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
			return $tCityArray;
		}
	}
}