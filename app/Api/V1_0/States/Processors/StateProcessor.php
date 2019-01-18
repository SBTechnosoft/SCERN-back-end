<?php
namespace ERP\Api\V1_0\States\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\States\Persistables\StatePersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\States\Validations\StateValidate;
use ERP\Api\V1_0\States\Transformers\StateTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class StateProcessor extends BaseProcessor
{
	/**
     * @var statePersistable
	 * @var request
     */
	private $statePersistable;
	private $request;    
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return State Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		$stateArray = array();
		$stateValue = array();
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
			$stateTransformer = new StateTransformer();
			$tRequest = $stateTransformer->trimInsertData($this->request);
			
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//get data from trim array
				$tStateAbb = $tRequest['state_abb'];
				$tStateName = $tRequest['state_name'];
				$tIsDisplay = $tRequest['is_display'];
				$tStateCode = $tRequest['state_code'];
				
				//validation
				$stateValidate = new StateValidate();
				$status = $stateValidate->validate($tRequest);
				
				//if form-data is valid then return status 'Success' otherwise return with error message
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$stateValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$stateValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$stateValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($stateValue);$data++)
					{
						//set the data in persistable object
						$statePersistable = new StatePersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$statePersistable->$setFuncName($stateValue[$data]);
						$statePersistable->setName($getFuncName[$data]);
						$statePersistable->setKey($keyName[$data]);
						$stateArray[$data] = array($statePersistable);
					}
					return $stateArray;
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
     * $param state_abb
     * @return State Persistable object
     */
	public function createPersistableChange(Request $request,$stateAbb)
	{
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		// update
		if($requestMethod == 'POST')
		{
			$statePersistable;
			$stateArray = array();
			$stateValidate = new StateValidate();
			$status;
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
		
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
					$statePersistable = new StatePersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$stateTransformer = new StateTransformer();
					$tRequest = $stateTransformer->trimUpdateData($key[$data],$value[$data]);
					
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
						$status = $stateValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						
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
								$statePersistable->$setFuncName($tValue[$data]);
								$statePersistable->setName($getFuncName[$data]);
								$statePersistable->setKey($tKeyValue[$data]);
								$statePersistable->setStateAbb($stateAbb);
								$stateArray[$data] = array($statePersistable);
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
								return $stateArray;
							}
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$statePersistable = new StatePersistable();		
			$statePersistable->setStateAbb($stateAbb);			
			return $statePersistable;
		}
	}
	
}