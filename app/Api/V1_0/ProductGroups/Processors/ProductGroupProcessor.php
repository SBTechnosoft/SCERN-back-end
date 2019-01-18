<?php
namespace ERP\Api\V1_0\ProductGroups\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\ProductGroups\Persistables\ProductGroupPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\ProductGroups\Validations\ProductGroupValidate;
use ERP\Api\V1_0\ProductGroups\Transformers\ProductGroupTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupProcessor extends BaseProcessor
{
	/**
     * @var productCatPersistable
	 * @var stateName
	 * @var stateAbb
	 * @var isDisplay
	 * @var request
     */
	private $productGroupPersistable;
	private $stateName;
	private $stateAbb;   
	private $isDisplay;   
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return State Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$productGroupValue = array();
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
			$productGroupTransformer = new ProductGroupTransformer();
			$tRequest = $productGroupTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$productGroupValidate = new ProductGroupValidate();
				$status = $productGroupValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$productGroupValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$productGroupValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$productGroupValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($productGroupValue);$data++)
					{
						//set the data in persistable object
						$productGroupPersistable = new ProductGroupPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$productGroupPersistable->$setFuncName($productGroupValue[$data]);
						$productGroupPersistable->setName($getFuncName[$data]);
						$productGroupPersistable->setKey($keyName[$data]);
						$productGroupArray[$data] = array($productGroupPersistable);
					}
					return $productGroupArray;
				}
				else
				{
					return $status;
				}
			}
		}
	}
	
	/**
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
			$productGroupTransformer = new ProductGroupTransformer();
			$trimData = $productGroupTransformer->trimInsertBatchData($this->request);
			if(is_array($trimData))
			{
				$tRequestData = $trimData['dataArray'];
				$totalErrorArray = count($trimData['errorArray']);
				$countRequestedData = count($tRequestData);
				
				$newProductArray = array();
				for($dataArray=0;$dataArray<$countRequestedData;$dataArray++)
				{
					$productGroupValue = array();
					$keyName = array();
					$value = array();
					$data=0;
					$tRequest = $tRequestData[$dataArray];
					//validation
					$productGroupValidate = new ProductGroupValidate();
					$status = $productGroupValidate->validate($tRequest);
					
					if($status=="Success")
					{
						foreach ($tRequest as $key => $value)
						{
							if(!is_numeric($value))
							{
								if (strpos($value, '\'') !== FALSE)
								{
									$productGroupValue[$data]= str_replace("'","\'",$value);
									$keyName[$data] = $key;
								}
								else
								{
									$productGroupValue[$data] = $value;
									$keyName[$data] = $key;
								}
							}
							else
							{
								$productGroupValue[$data]= $value;
								$keyName[$data] = $key;
							}
							$data++;
						}
						$getFuncName = array();
						// set data to the persistable object
						for($data=0;$data<count($productGroupValue);$data++)
						{
							//set the data in persistable object
							$productGroupPersistable = new ProductGroupPersistable();	
							$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
							//make function name dynamically
							$setFuncName = 'set'.$str;
							$getFuncName[$data] = 'get'.$str;
							$productGroupPersistable->$setFuncName($productGroupValue[$data]);
							$productGroupPersistable->setName($getFuncName[$data]);
							$productGroupPersistable->setKey($keyName[$data]);
							$productGroupArray[$data] = array($productGroupPersistable);
						}
						array_push($newProductArray,$productGroupArray);
					}
					else
					{
						$decodedArrayStatus = json_decode($status);
						//convert object to array
						$decodedArray = (array)$decodedArrayStatus[0];
		
						$trimData['errorArray'][$totalErrorArray]['productGroupName'] = $trimData['dataArray'][$dataArray]['product_group_name'];
						$trimData['errorArray'][$totalErrorArray]['productGroupDescription'] = $trimData['dataArray'][$dataArray]['product_group_description'];
						$trimData['errorArray'][$totalErrorArray]['isDisplay'] = $trimData['dataArray'][$dataArray]['is_display'];
						$trimData['errorArray'][$totalErrorArray]['productGroupParentId'] = $trimData['dataArray'][$dataArray]['product_group_parent_id'];
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
	
	public function createPersistableChange(Request $request,$productGrpId)
	{
		$productGrpValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$productGroupPersistable;
		$productGrpArray = array();
		$productGroupValidate = new ProductGroupValidate();
		$status;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		// update
		if($requestMethod == 'POST')
		{
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
					$productGroupPersistable = new ProductGroupPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$productGroupTransformer = new ProductGroupTransformer();
					$tRequest = $productGroupTransformer->trimUpdateData($key[$data],$value[$data]);
					
					if($tRequest==1)
					{
						return $exceptionArray['content'];
					}
					else
					{
						//get data from trim array
						$tKeyValue[$data] = array_keys($tRequest[0])[0];
						$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
						
						//validation
						$status = $productGroupValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$productGrpValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$productGrpValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$productGrpValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$productGroupPersistable->$setFuncName($productGrpValue[$data]);
								$productGroupPersistable->setName($getFuncName[$data]);
								$productGroupPersistable->setKey($tKeyValue[$data]);
								$productGroupPersistable->setProductGroupId($productGrpId);
								$productGrpArray[$data] = array($productGroupPersistable);
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
								return $productGrpArray;
							}
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$productGroupPersistable = new ProductGroupPersistable();		
			$productGroupPersistable->setId($productGrpId);			
			return $productGroupPersistable;
		}
	}	
}