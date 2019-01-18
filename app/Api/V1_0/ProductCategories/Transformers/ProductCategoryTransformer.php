<?php
namespace ERP\Api\V1_0\ProductCategories\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Exceptions\ExceptionMessage;
use stdClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategoryTransformer extends ExceptionMessage
{
    /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		//data get from body
		$productCatName = $request->input('productCategoryName'); 
		$productCatDesc = $request->input('productCategoryDescription'); 
		$isDisplay = $request->input('isDisplay'); 
		$productParentCatId = $request->input('productParentCategoryId');  
		//trim an input
		$tProductCatName = trim($productCatName);
		$tProductCatDesc = trim($productCatDesc);
		$tIsDisplay = trim($isDisplay);
		$tProductParentCatId= trim($productParentCatId);
		
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
			$data['product_category_name'] = $tProductCatName;
			$data['product_category_description'] = $tProductCatDesc;
			$data['is_display'] = $tIsDisplay;
			$data['product_parent_category_id'] = $tProductParentCatId;
			return $data;
		}
	}

	/**
     * @param Request $request
     * @return array
     */
    public function trimInsertBatchData(Request $request)
    {
		$transformerClass = new ProductCategoryTransformer();
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
				$productCatName = $requestInputData[$arrayData]['productCategoryName']; 
				$productCatDesc = $requestInputData[$arrayData]['productCategoryDescription']; 
				$isDisplay = $requestInputData[$arrayData]['isDisplay']; 
				$productParentCatId = $requestInputData[$arrayData]['productParentCategoryId'];  
				
				//trim an input
				$tProductCatName = trim($productCatName);
				$tProductCatDesc = trim($productCatDesc);
				$tIsDisplay = trim($isDisplay);
				$tProductParentCatId= trim($productParentCatId);
				
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
					$errorArray[$errorIndex]['productCategoryName'] = $tProductCatName;
					$errorArray[$errorIndex]['productCategoryDescription'] = $tProductCatDesc;
					$errorArray[$errorIndex]['isDisplay'] = $tIsDisplay;
					$errorArray[$errorIndex]['productParentCategoryId'] = $tProductParentCatId;
					$errorArray[$errorIndex]['remark'] = $exceptionArray['isDisplayEnum'];
					$errorIndex++;
				}
				else
				{
					//make an array
					$data[$dataIndex] = array();
					$data[$dataIndex]['product_category_name'] = $tProductCatName;
					$data[$dataIndex]['product_category_description'] = $tProductCatDesc;
					$data[$dataIndex]['is_display'] = $tIsDisplay;
					$data[$dataIndex]['product_parent_category_id'] = $tProductParentCatId;
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
		$transformerClass = new ProductCategoryTransformer();
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
     * @param key and value
     * @return array
     */
	public function trimUpdateData()
	{
		$tProductCatArray = array();
		$productCatValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$productCategoryEnumArray = array();
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
		$productCatValue = func_get_arg(1);
		for($data=0;$data<count($productCatValue);$data++)
		{
			$tProductCatArray[$data]= array($convertedValue=> trim($productCatValue));
			$productCategoryEnumArray = array_keys($tProductCatArray[$data])[0];
		}
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($productCategoryEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tProductCatArray[0]['is_display'],$value)==0)
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
			return $tProductCatArray;
		}
	}
}