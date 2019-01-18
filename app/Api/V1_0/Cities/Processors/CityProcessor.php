<?php
namespace ERP\Api\V1_0\Cities\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Cities\Persistables\CityPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Cities\Validations\CityValidate;
use ERP\Api\V1_0\Cities\Transformers\CityTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityProcessor extends BaseProcessor
{
	/**
     * @var cityPersistable
	 * @var request
     */
	private $cityPersistable;   
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return City Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		$cityArray = array();
		$cityValue = array();
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
			$cityTransformer = new CityTransformer();
			$tRequest = $cityTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
			
				//validation
				$cityValidate = new CityValidate();
				$status = $cityValidate->validate($tRequest);
				
				//if form-data is valid then return status 'Success' otherwise return with error message
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$cityValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$cityValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$cityValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($cityValue);$data++)
					{
						//set the data in persistable object
						$cityPersistable = new CityPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$cityPersistable->$setFuncName($cityValue[$data]);
						$cityPersistable->setName($getFuncName[$data]);
						$cityPersistable->setKey($keyName[$data]);
						$cityArray[$data] = array($cityPersistable);
					}
					return $cityArray;
				}		
				else
				{
					return $status;
				}
			}
		}
	}
	public function createPersistableChange(Request $request,$cityId)
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
			$cityPersistable;
			$cityArray = array();
			$cityValidate = new CityValidate();
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
					$cityPersistable = new CityPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$cityTransformer = new CityTransformer();
					$tRequest = $cityTransformer->trimUpdateData($key[$data],$value[$data]);
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
						$status = $cityValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$cityPersistable->$setFuncName($tValue[$data]);
								$cityPersistable->setName($getFuncName[$data]);
								$cityPersistable->setKey($tKeyValue[$data]);
								$cityPersistable->setCityId($cityId);
								$cityArray[$data] = array($cityPersistable);
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
								return $cityArray;
							}
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$cityPersistable = new CityPersistable();		
			$cityPersistable->setId($cityId);			
			return $cityPersistable;
		}
	}	
}