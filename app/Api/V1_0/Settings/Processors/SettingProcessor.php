<?php
namespace ERP\Api\V1_0\Settings\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Settings\Persistables\SettingPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Settings\Validations\SettingValidate;
use ERP\Api\V1_0\Settings\Transformers\SettingTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SettingProcessor extends BaseProcessor
{
	/**
     * @var settingPersistable
	 * @var request
     */
	private $settingPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return setting Array / Error Message Array / Exception Message
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$settingArray = array();
		$settingValue = array();
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
			$settingTransformer = new SettingTransformer();
			$tRequest = $settingTransformer->trimInsertData($this->request);
			if(!is_array($tRequest))
			{
				if(strcmp($msgArray['content'],$tRequest)==0)
				{
					return $tRequest;
				}
			}
			// validation
			$settingValidate = new SettingValidate();
			$status = $settingValidate->validate($tRequest);
			if($status=="Success")
			{
				foreach ($tRequest as $key => $value)
				{
					if(!is_numeric($value))
					{
						if (strpos($value, '\'') !== FALSE)
						{
							$settingValue[$data]= str_replace("'","\'",$value);
							$keyName[$data] = $key;
						}
						else
						{
							$settingValue[$data] = $value;
							$keyName[$data] = $key;
						}
					}
					else
					{
						$settingValue[$data]= $value;
						$keyName[$data] = $key;
					}
					$data++;
				}
				// set data to the persistable object
				for($data=0;$data<count($settingValue);$data++)
				{
					// set the data in persistable object
					$settingPersistable = new SettingPersistable();	
					$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
					// make function name dynamically
					 $setFuncName = 'set'.$str;
					$getFuncName[$data] = 'get'.$str;
					$settingPersistable->$setFuncName($settingValue[$data]);
					$settingPersistable->setName($getFuncName[$data]);
					$settingPersistable->setKey($keyName[$data]);
					$settingArray[$data] = array($settingPersistable);
				}
				return $settingArray;
			}
			else
			{
				return $settingArray;
			}
			
		}
	}
	
	 /**
     * update data
     * $param request-array
     * @return Setting Array / Error Message Array / Exception Message
     */
	public function createPersistableChange($requestData)
	{
		$settingValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$settingPersistable;
		$settingArray = array();
		$settingValidate = new SettingValidate();
		$status;
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		// update
		//if data is not available in update request
		if(count($requestData)==0)
		{
			$status = $exceptionArray['204'];
			return $status;
		}
		//data is avalilable for update
		else
		{
			for($data=0;$data<count($requestData);$data++)
			{
				//data get from body
				$settingPersistable = new SettingPersistable();
				$value[$data] = $requestData[array_keys($requestData)[$data]];
				$key[$data] = array_keys($requestData)[$data];
				
				//trim an input 
				$settingTransformer = new SettingTransformer();
				$tRequest = $settingTransformer->trimUpdateData($key[$data],$value[$data]);
				if(!is_array($tRequest))
				{
					return $exceptionArray['content'];
				}
				else
				{
					//get data from trim array
					$tKeyValue[$data] = array_keys($tRequest[0])[0];
					$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
					
					//validation
					$status = $settingValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
					
					//enter data is valid(one data validate status return)
					if($status=="Success")
					{
						// check data is string or not
						if(!is_numeric($tValue[$data]))
						{
							if (strpos($tValue[$data], '\'') !== FALSE)
							{
								$settingValue[$data] = str_replace("'","\'",$tValue[$data]);
							}
							else
							{
								$settingValue[$data] = $tValue[$data];
							}
						}
						else
						{
							$settingValue[$data] = $tValue[$data];
						}
						// flag=0...then data is valid(consider one data at a time)
						if($flag==0)
						{
							$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
							// make function name dynamically
							$setFuncName = 'set'.$str;
							$getFuncName[$data] = 'get'.$str;
							$settingPersistable->$setFuncName($settingValue[$data]);
							$settingPersistable->setName($getFuncName[$data]);
							$settingPersistable->setKey($tKeyValue[$data]);
							$settingArray[$data] = array($settingPersistable);
						}
					}
					//enter data is not valid
					else
					{
						// if flag==1 then enter data is not valid ..so error return(consider one data at a time)
						$flag=1;
						if(!empty($status[0]))
						{
							$errorStatus[$errorCount]=$status[0];
							$errorCount++;
						}
					}
					if($data==(count($requestData)-1))
					{
						if($flag==1)
						{
							return json_encode($errorStatus);
						}
						else
						{
							return $settingArray;
						}
					}
				}
			}
		}
	}	
}