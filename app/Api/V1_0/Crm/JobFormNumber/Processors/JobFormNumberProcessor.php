<?php
namespace ERP\Api\V1_0\Crm\JobFormNumber\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Crm\JobFormNumber\Persistables\JobFormNumberPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Crm\JobFormNumber\Validations\JobFormNumberValidate;
use ERP\Api\V1_0\Crm\JobFormNumber\Transformers\JobFormNumberTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumberProcessor extends BaseProcessor
{
	/**
     * @var jobFormNumberPersistable
	 * @var request
     */
	private $jobFormNumberPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return JobFormNumber Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$jobFormNumberArray = array();
		$jobFormNumberValue = array();
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
			$jobFormNumberTransformer = new JobFormNumberTransformer();
			$tRequest = $jobFormNumberTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$jobFormNumberValidate = new JobFormNumberValidate();
				$status = $jobFormNumberValidate->validate($tRequest);
			
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$jobFormNumberValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$jobFormNumberValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$jobFormNumberValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					// set data to the persistable object
					for($data=0;$data<count($jobFormNumberValue);$data++)
					{
						// set the data in persistable object
						$jobFormNumberPersistable = new JobFormNumberPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						// make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$jobFormNumberPersistable->$setFuncName($jobFormNumberValue[$data]);
						$jobFormNumberPersistable->setName($getFuncName[$data]);
						$jobFormNumberPersistable->setKey($keyName[$data]);
						$jobFormNumberArray[$data] = array($jobFormNumberPersistable);
					}
					return $jobFormNumberArray;
				}
				else
				{
					return $status;
				}
			}
		}
	}
	
	 /**
     * update data
     * $param Request object [Request $request] and Branch Id
     * @return Branch Array / Error Message Array / Exception Message
     */
	public function createPersistableChange(Request $request,$branchId)
	{
		$branchValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$branchPersistable;
		$branchArray = array();
		$branchValidate = new BranchValidate();
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
				return $exceptionArray['204'];
			}
			//data is avalilable for update
			else
			{
				for($data=0;$data<count($_POST);$data++)
				{
					//data get from body
					$branchPersistable = new BranchPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$branchTransformer = new BranchTransformer();
					$tRequest = $branchTransformer->trimUpdateData($key[$data],$value[$data]);
					
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
						$status = $branchValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$branchValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$branchValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$branchValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$branchPersistable->$setFuncName($branchValue[$data]);
								$branchPersistable->setName($getFuncName[$data]);
								$branchPersistable->setKey($tKeyValue[$data]);
								$branchPersistable->setBranchId($branchId);
								$branchArray[$data] = array($branchPersistable);
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
								return $branchArray;
							}
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$branchPersistable = new BranchPersistable();		
			$branchPersistable->setBranchId($branchId);			
			return $branchPersistable;
		}
	}	
}