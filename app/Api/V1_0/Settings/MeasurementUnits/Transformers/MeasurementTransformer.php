<?php
namespace ERP\Api\V1_0\Settings\MeasurementUnits\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class MeasurementTransformer
{
	/**
     * @param Request Object
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		//data get from body
		$data = array();
		$data['unit_name'] = trim($request->input('unitName'));
		return $data;
	}
	
    /**
     * @param Request Object
     * @return array
     */
	public function trimUpdateData()
	{
		$tMeasurementArray = array();
		$measurementValue;
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
		$measurementValue = func_get_arg(1);
		for($data=0;$data<count($measurementValue);$data++)
		{
			$tMeasurementArray[$data]= array($convertedValue=> trim($measurementValue));
		}
		return $tMeasurementArray;
	}
}