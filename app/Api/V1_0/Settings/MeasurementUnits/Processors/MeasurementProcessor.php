<?php
namespace ERP\Api\V1_0\Settings\MeasurementUnits\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Settings\MeasurementUnits\Persistables\MeasurementPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Settings\MeasurementUnits\Validations\MeasurementValidate;
use ERP\Api\V1_0\Settings\MeasurementUnits\Transformers\MeasurementTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class MeasurementProcessor extends BaseProcessor
{
	/**
     * @var measurementPersistable
	 * @var request
     */
	private $measurementPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Measurement Array / Error Message Array / Exception Message
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$measurementArray = array();
		$measurementValue = array();
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
			$measurementTransformer = new MeasurementTransformer();
			$tRequest = $measurementTransformer->trimInsertData($this->request);
			
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$measurementValidate = new MeasurementValidate();
				$status = $measurementValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$measurementValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$measurementValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{	
							$measurementValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					// set data to the persistable object
					for($data=0;$data<count($measurementValue);$data++)
					{
						//set the data in persistable object
						$measurementPersistable = new MeasurementPersistable();
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						// make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$measurementPersistable->$setFuncName($measurementValue[$data]);
						$measurementPersistable->setName($getFuncName[$data]);
						$measurementPersistable->setKey($keyName[$data]);
						$measurementArray[$data] = array($measurementPersistable);
					}
					return $measurementArray;
				}
				else
				{
					return $measurementArray;
				}
			}
		}
	}
	
	 /**
     * update data
     * $param Request object [Request $request] and Measurement Id
     * @return Measurement Array / Error Message Array / Exception Message
     */
	public function createPersistableChange(Request $request,$measurementUnitId)
	{
		$measurementValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$measurementPersistable;
		$measurementArray = array();
		$measurementValidate = new MeasurementValidate();
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
					$measurementPersistable = new MeasurementPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					//trim an input 
					$measurementTransformer = new MeasurementTransformer();
					$tRequest = $measurementTransformer->trimUpdateData($key[$data],$value[$data]);
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
						$status = $measurementValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$measurementValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$measurementValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$measurementValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$measurementPersistable->$setFuncName($measurementValue[$data]);
								$measurementPersistable->setName($getFuncName[$data]);
								$measurementPersistable->setKey($tKeyValue[$data]);
								$measurementPersistable->setMeasurementUnitId($measurementUnitId);
								$measurementArray[$data] = array($measurementPersistable);
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
								return $measurementArray;
							}
						}
					}
				}
			}
		}
	}	
}