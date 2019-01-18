<?php
namespace ERP\Api\V1_0\Users\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Users\Persistables\UserPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Users\Validations\UserValidate;
use ERP\Api\V1_0\Users\Transformers\UserTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserProcessor extends BaseProcessor
{
	/**
     * @var userPersistable
	 * @var request
     */
	private $userPersistable;
	private $request;    
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return User Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		$userArray = array();
		$userValue = array();
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
			$userValidate = new UserValidate();
			//trim an input 
			$userTransformer = new UserTransformer();
			$tRequest = $userTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//check emailId is exist or not?
				$emailIdResult = $userValidate->emailIdCheck($tRequest,$this->request);
				if(!is_array($emailIdResult))
				{
					$constantClass = new ConstantClass();
					$constantResult = $constantClass->getCommentMessage();
					return $constantResult->emailIdExists;
				}
			}
			if(is_array($emailIdResult))
			{
				//validation
				$status = $userValidate->validate($tRequest);
				
				//if form-data is valid then return status 'Success' otherwise return with error message
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$userValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$userValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$userValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					// set data to the persistable object
					for($data=0;$data<count($userValue);$data++)
					{
						//set the data in persistable object
						$userPersistable = new UserPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$userPersistable->$setFuncName($userValue[$data]);
						$userPersistable->setName($getFuncName[$data]);
						$userPersistable->setKey($keyName[$data]);
						$userArray[$data] = array($userPersistable);
					}
					return $userArray;
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
	public function createPersistableChange(Request $request,$userId)
	{
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		// update
		if($requestMethod == 'POST')
		{
			$userPersistable;
			$userArray = array();
			$userValidate = new UserValidate();
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
					$userPersistable = new UserPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$userTransformer = new UserTransformer();
					$tRequest = $userTransformer->trimUpdateData($key[$data],$value[$data]);
					if($tRequest==1)
					{
						return $exceptionArray['content'];
					}
					else
					{
						//check emailId is exist or not?
						if(array_key_exists("email_id",$tRequest[0]))
						{
							$emailIdResult = $userValidate->emailIdCheck($tRequest[0],$request);
							if(!is_array($emailIdResult))
							{
								return $emailIdResult;
							}
						}
					}
					//get key value from trim array
					$tKeyValue[$data] = array_keys($tRequest[0])[0];
					$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
					
					//validation
					$status = $userValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
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
							$userPersistable->$setFuncName($tValue[$data]);
							$userPersistable->setName($getFuncName[$data]);
							$userPersistable->setKey($tKeyValue[$data]);
							$userPersistable->setUserId($userId);
							$userArray[$data] = array($userPersistable);
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
							return $userArray;
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$userPersistable = new UserPersistable();		
			$userPersistable->setUserId($userId);			
			return $userPersistable;
		}
	}
	
}