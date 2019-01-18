<?php
namespace ERP\Api\V1_0\ProductGroups\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Exceptions\ExceptionMessage;
use stdClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupTransformer  extends ExceptionMessage
{
    /**
     * @param Request object
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		//data get from body
		$productGroupName = $request->input('productGroupName'); 
		$productGroupDesc = $request->input('productGroupDescription'); 
		$productGroupParentId = $request->input('productGroupParentId'); 
		$isDisplay = $request->input('isDisplay'); 			
		
		//trim an input
		$tProductGroupName = trim($productGroupName);
		$tProductGroupDesc = trim($productGroupDesc);
		$tProductGroupParentId = trim($productGroupParentId);
		$tIsDisplay = trim($isDisplay);
		
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if($tIsDisplay=="")
		{
			$tIsDisplay = $enumIsDispArray['display'];
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
			$data['product_group_name'] = $tProductGroupName;
			$data['product_group_description'] = $tProductGroupDesc;
			$data['product_group_parent_id'] = $tProductGroupParentId;
			$data['is_display'] = $tIsDisplay;
			return $data;
		}
	}
	
	/**
     * @param Request object
     * @return array
     */
    public function trimInsertBatchData(Request $request)
    {
		$transformerClass = new ProductGroupTransformer();
		$exceptionArray = $transformerClass->messageArrays();
		
		//data mapping
		$mappingResult = $this->mappingData($request->input());
		if(is_array($mappingResult))
		{
			$data = array();
			$errorArray = array();
			$requestInputData = $mappingResult;
			$errorIndex = 0;
			$dataIndex = 0;
			for($arrayData=0;$arrayData<count($requestInputData);$arrayData++)
			{
				$isDisplayFlag=0;
				//data get from body
				$productGroupName = $requestInputData[$arrayData]['productGroupName']; 
				$productGroupDesc = $requestInputData[$arrayData]['productGroupDescription']; 
				$productGroupParentId = $requestInputData[$arrayData]['productGroupParentId']; 
				$isDisplay = $requestInputData[$arrayData]['isDisplay']; 			
				
				//trim an input
				$tProductGroupName = trim($productGroupName);
				$tProductGroupDesc = trim($productGroupDesc);
				$tProductGroupParentId = trim($productGroupParentId);
				$tIsDisplay = trim($isDisplay);
				
				$enumIsDispArray = array();
				$isDispEnum = new IsDisplayEnum();
				$enumIsDispArray = $isDispEnum->enumArrays();
				if($tIsDisplay=="")
				{
					$tIsDisplay = $enumIsDispArray['display'];
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
					$errorArray[$errorIndex] = array();
					$errorArray[$errorIndex]['productGroupName'] = $tProductGroupName;
					$errorArray[$errorIndex]['productGroupDescription'] = $tProductGroupDesc;
					$errorArray[$errorIndex]['isDisplay'] = $tIsDisplay;
					$errorArray[$errorIndex]['productGroupParentId'] = $tProductGroupParentId;
					$errorArray[$errorIndex]['remark'] = $exceptionArray['isDisplayEnum'];
					$errorIndex++;
				}
				else
				{
					//make an array
					$data[$dataIndex] = array();
					$data[$dataIndex]['product_group_name'] = $tProductGroupName;
					$data[$dataIndex]['product_group_description'] = $tProductGroupDesc;
					$data[$dataIndex]['is_display'] = $tIsDisplay;
					$data[$dataIndex]['product_group_parent_id'] = $tProductGroupParentId;
					$dataIndex++;
				}
			}
			$trimArray = array();
			$trimArray['errorArray']= $errorArray;
			$trimArray['dataArray'] = $data;
			return $trimArray;
		}
		else
		{
			return $mappingResult;
		}
	}
	
	/**
     * @param request array
     * @return array/error-message
     */
	public function mappingData()
	{
		$transformerClass = new ProductGroupTransformer();
		$exceptionArray = $transformerClass->messageArrays();
		
		$rquestArray = func_get_arg(0);
		
		$mappingArray = $rquestArray['mapping'];
		$dataArray = $rquestArray['data'];
		
		$keyNameCount = array_count_values($mappingArray);
		//searching data in mapping array ..it is duplicate or not?
		for($index=0;$index<count($keyNameCount);$index++)
		{
			$value = $keyNameCount[array_keys($keyNameCount)[$index]];
			if($value>1 || array_keys($keyNameCount)[$index]=="")
			{
				return $exceptionArray['mapping'];
			}
		}
		if(count($mappingArray)!=4)
		{
			return $exceptionArray['missingField'];
		}
		
		$requestArray = array();
		//make an requested array
		for($arrayData=0;$arrayData<count($dataArray);$arrayData++)
		{
			$requestArray[$arrayData] = array();
			$requestArray[$arrayData][array_keys($keyNameCount)[0]] = $dataArray[$arrayData][0];
			$requestArray[$arrayData][array_keys($keyNameCount)[1]] = $dataArray[$arrayData][1];
			$requestArray[$arrayData][array_keys($keyNameCount)[2]] = $dataArray[$arrayData][2];
			$requestArray[$arrayData][array_keys($keyNameCount)[3]] = $dataArray[$arrayData][3];
		}
		return $requestArray;
	}
	
	/**
     * @param request array
     * @return array/error-message
     */
	public function trimUpdateData()
	{
		$tProductGroupArray = array();
		$productGroupValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$productGrpEnumArray = array();
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
		$productGroupValue = func_get_arg(1);
		for($data=0;$data<count($productGroupValue);$data++)
		{
			$tproductGroupArray[$data]= array($convertedValue=> trim($productGroupValue));
			$productGrpEnumArray = array_keys($tproductGroupArray[$data])[0];
		}
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($productGrpEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tproductGroupArray[0]['is_display'],$value)==0)
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
			return $tproductGroupArray;
		}
	}
}