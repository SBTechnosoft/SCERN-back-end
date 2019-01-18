<?php
namespace ERP\Api\V1_0\ProductCategories\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\ProductCategories\Persistables\ProductCategoryPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\ProductCategories\Validations\ProductCategoryValidate;
use ERP\Api\V1_0\ProductCategories\Transformers\ProductCategoryTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategoryProcessor extends BaseProcessor
{
	/**
     * @var productCatPersistable
	 * @var stateName
	 * @var stateAbb
	 * @var isDisplay
	 * @var request
     */
	private $productCatPersistable;
	private $stateName;
	private $stateAbb;   
	private $isDisplay;   
	private $request;    
	
    /**
     * Insert
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return State Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$productCatValue = array();
		$tKeyValue = array();
		$keyName = array();
		$value = array();
		$data=0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		if(count($_POST)==0)
		{
			return $msgArray['204'];
		}
		else
		{
			//trim an input 
			$productCategoryTransformer = new ProductCategoryTransformer();
			$tRequest = $productCategoryTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$productCategoryValidate = new ProductCategoryValidate();
				$status = $productCategoryValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$productCatValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$productCatValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$productCatValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					// set data to the persistable object
					for($data=0;$data<count($productCatValue);$data++)
					{
						//set the data in persistable object
						$productCategoryPersistable = new ProductCategoryPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$productCategoryPersistable->$setFuncName($productCatValue[$data]);
						$productCategoryPersistable->setName($getFuncName[$data]);
						$productCategoryPersistable->setKey($keyName[$data]);
						$productCatArray[$data] = array($productCategoryPersistable);
					}
					return $productCatArray;
				}
				else
				{
					return $status;
				}
			}
		}
	}
	
	 /**
     * Insert
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return State Persistable object
     */	
    public function createPersistableBatchData(Request $request)
	{	
		$this->request = $request;	
		$tKeyValue = array();
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		if(count($_POST)==0)
		{
			return $msgArray['204'];
		}
		else
		{
			//trim an input 
			$productCategoryTransformer = new ProductCategoryTransformer();
			$trimData = $productCategoryTransformer->trimInsertBatchData($this->request);
			if(is_array($trimData))
			{
				$tRequestData = $trimData['dataArray'];
				$totalErrorArray = count($trimData['errorArray']);
				$countRequestedData = count($tRequestData);
				
				$newProductArray = array();
				for($dataArray=0;$dataArray<$countRequestedData;$dataArray++)
				{
					$productCatValue = array();
					$keyName = array();
					$value = array();
					$data=0;
					$tRequest = $tRequestData[$dataArray];
					
					//validation
					$productCategoryValidate = new ProductCategoryValidate();
					$status = $productCategoryValidate->validate($tRequest);
					if($status=="Success")
					{
						foreach ($tRequest as $key => $value)
						{
							if(!is_numeric($value))
							{
								if (strpos($value, '\'') !== FALSE)
								{
									$productCatValue[$data]= str_replace("'","\'",$value);
									$keyName[$data] = $key;
								}
								else
								{
									$productCatValue[$data] = $value;
									$keyName[$data] = $key;
								}
							}
							else
							{
								$productCatValue[$data]= $value;
								$keyName[$data] = $key;
							}
							$data++;
						}
						$productCatArray = array();
						$getFuncName = array();
						// set data to the persistable object
						for($data=0;$data<count($productCatValue);$data++)
						{
							//set the data in persistable object
							$productCategoryPersistable = new ProductCategoryPersistable();	
							$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
							//make function name dynamically
							$setFuncName = 'set'.$str;
							$getFuncName[$data] = 'get'.$str;
							$productCategoryPersistable->$setFuncName($productCatValue[$data]);
							$productCategoryPersistable->setName($getFuncName[$data]);
							$productCategoryPersistable->setKey($keyName[$data]);
							$productCatArray[$data] = array($productCategoryPersistable);
						}
						array_push($newProductArray,$productCatArray);
					}
					else
					{
						$decodedArrayStatus = json_decode($status);
						//convert object to array
						$decodedArray = (array)$decodedArrayStatus[0];
		
						$trimData['errorArray'][$totalErrorArray]['productCategoryName'] = $trimData['dataArray'][$dataArray]['product_category_name'];
						$trimData['errorArray'][$totalErrorArray]['productCategoryDescription'] = $trimData['dataArray'][$dataArray]['product_category_description'];
						$trimData['errorArray'][$totalErrorArray]['isDisplay'] = $trimData['dataArray'][$dataArray]['is_display'];
						$trimData['errorArray'][$totalErrorArray]['productParentCategoryId'] = $trimData['dataArray'][$dataArray]['product_parent_category_id'];
						$trimData['errorArray'][$totalErrorArray]['remark'] = $decodedArray[array_keys($decodedArray)[0]];
						$totalErrorArray++;
					}
				}
				unset($trimData['dataArray']);
				$trimData['dataArray'] = $newProductArray;
				return $trimData;
			}
			else
			{
				$errorResult = array();
				$errorResult['mapping_error'] = $trimData;
				$encodeResult = json_encode($errorResult);
				return $encodeResult;
			}
		}
	}
	
	public function createPersistableChange(Request $request,$productCatId)
	{
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		// update
		if($requestMethod == 'POST')
		{
			$productValue = array();
			$productCatPersistable;
			$productCatArray = array();
			$productCategoryValidate = new ProductCategoryValidate();
			$status;
			//if data is not available in update request
			if(count($_POST)==0)
			{
				$status = $exceptionArray['204'];
				return $status;
			}
			//data is avalilable for update
			else
			{
				for($data=0;$data<count($_POST);$data++)
				{
					//data get from body
					$productCatPersistable = new ProductCategoryPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$productCategoryTransformer = new ProductCategoryTransformer();
					$tRequest = $productCategoryTransformer->trimUpdateData($key[$data],$value[$data]);
					
					if($tRequest==1)
					{
						return $exceptionArray['content'];
					}
					else
					{
						//get key value from trim array
						$tKeyValue[$data] = array_keys($tRequest[0])[0];
						$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
						
						//validation
						$status = $productCategoryValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$productValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$productValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$productValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$productCatPersistable->$setFuncName($productValue[$data]);
								$productCatPersistable->setName($getFuncName[$data]);
								$productCatPersistable->setKey($tKeyValue[$data]);
								$productCatPersistable->setProductCategoryId($productCatId);
								$productCatArray[$data] = array($productCatPersistable);
							}
						}
						//enter data is not valid
						else
						{
							//if flag==1 then enter data is not valid ..so error return(consider one data at a time)
							$flag=1;
							if(!empty($status[0]))
							{
								$errorStatus[$errorCount]=$status[0];
								$errorCount++;
							}
						}
						if($data==(count($_POST)-1))
						{
							if($flag==1)
							{
								return json_encode($errorStatus);
							}
							else
							{
								return $productCatArray;
							}
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$productCatPersistable = new ProductCategoryPersistable();		
			$productCatPersistable->setId($productCatId);			
			return $productCatPersistable;
		}
	}	
}