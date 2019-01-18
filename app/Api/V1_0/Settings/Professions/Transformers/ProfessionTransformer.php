<?php
namespace ERP\Api\V1_0\Settings\Professions\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfessionTransformer
{
	/**
     * @param Request Object
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		//data get from body
		$professionName = $request->input('professionName'); 
		$description = $request->input('description'); 
		$professionParentId = $request->input('professionParentId'); 
		
		//trim an input
		$tProfessionName = trim($professionName);
		$tDescription = trim($description);
		$tProfessionParentId = trim($professionParentId);
		
		//make an array
		$data = array();
		$data['profession_name'] = $tProfessionName;
		$data['description'] = $tDescription;
		$data['profession_parent_id'] = $tProfessionParentId;
		return $data;
		
	}
	
    /**
     * @param Request Object
     * @return array
     */
   public function trimUpdateData()
	{
		$tProfessionArray = array();
		$professionValue;
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
		$professionValue = func_get_arg(1);
		for($data=0;$data<count($professionValue);$data++)
		{
			$tProfessionArray[$data]= array($convertedValue=> trim($professionValue));
		}
		return $tProfessionArray;
		
	}
}