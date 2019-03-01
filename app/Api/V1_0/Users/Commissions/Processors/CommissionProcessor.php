<?php
namespace ERP\Api\V1_0\Users\Commissions\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Users\Commissions\Persistables\CommissionPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Users\Commissions\Validations\CommissionValidate;
use ERP\Api\V1_0\Users\Commissions\Transformers\CommissionTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CommissionProcessor extends BaseProcessor
{
	/**
     * @var commissionPersistable
	 * @var request
     */
	private $commissionPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Template Array / Error Message Array / Exception Message
     */	
    public function createPersistable(Request $request)
	{
		$this->request = $request;
		$commissionArray = array();
		$commissionValue = array();
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
		$commissionTransformer = new CommissionTransformer();
			$tRequest = $commissionTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}
			else
			{
				//validation
				$commissionValidate = new CommissionValidate();
				$status = $commissionValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$commissionValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$commissionValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$commissionValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					// set data to the persistable object
					for($data=0;$data<count($commissionValue);$data++)
					{
						//set the data in persistable object
						$commissionPersistable = new CommissionPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$commissionPersistable->$setFuncName($commissionValue[$data]);
						$commissionPersistable->setName($getFuncName[$data]);
						$commissionPersistable->setKey($keyName[$data]);
						$commissionArray[$data] = array($commissionPersistable);
					}
					return $commissionArray;
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
     * @return Template Array / Error Message Array / Exception Message
     */	
    public function createItemwisePersistable(Request $request)
	{
		$this->request = $request;
		$commissionArray = array();
		$commissionValue = array();
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
		$commissionTransformer = new CommissionTransformer();
			$tRequest = $commissionTransformer->trimItemwiseInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}
			else
			{
				//validation
				$commissionValidate = new CommissionValidate();
				$status = $commissionValidate->validateItemwise($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$commissionValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$commissionValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$commissionValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					// set data to the persistable object
					for($data=0;$data<count($commissionValue);$data++)
					{
						//set the data in persistable object
						$commissionPersistable = new CommissionPersistable();
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$commissionPersistable->$setFuncName($commissionValue[$data]);
						$commissionPersistable->setName($getFuncName[$data]);
						$commissionPersistable->setKey($keyName[$data]);
						$commissionArray[$data] = array($commissionPersistable);
					}
					return $commissionArray;
				}
				else
				{
					return $commissionArray;
				}
			}
		}
	}
}