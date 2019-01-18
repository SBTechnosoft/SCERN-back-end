<?php
namespace ERP\Api\V1_0\Clients\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Clients\Persistables\ClientPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Clients\Validations\ClientValidate;
use ERP\Api\V1_0\Clients\Transformers\ClientTransformer;
use ERP\Exceptions\ExceptionMessage;
use stdclass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ClientProcessor extends BaseProcessor
{
	/**
     * @var clientPersistable
	 * @var request
     */
	private $clientPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Client Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		$clientArray = array();
		$clientValue = array();
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
			$clientTransformer = new ClientTransformer();
			$tRequest = $clientTransformer->trimInsertData($this->request);
			$clientValidate = new ClientValidate();
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			//validation
			$status = $clientValidate->validate($tRequest);
			if($status=="Success")
			{
				foreach ($tRequest as $key => $value)
				{
					if(!is_numeric($value))
					{
						if (strpos($value, '\'') !== FALSE)
						{
							$clientValue[$data]= str_replace("'","\'",$value);
							$keyName[$data] = $key;
						}
						else
						{
							$clientValue[$data] = $value;
							$keyName[$data] = $key;
						}
					}
					else
					{
						$clientValue[$data]= $value;
						$keyName[$data] = $key;
					}
					$data++;
				}
				// set data to the persistable object
				for($data=0;$data<count($clientValue);$data++)
				{
					//set the data in persistable object
					$clientPersistable = new ClientPersistable();	
					$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
					//make function name dynamically
					$setFuncName = 'set'.$str;
					$getFuncName[$data] = 'get'.$str;
					$clientPersistable->$setFuncName($clientValue[$data]);
					$clientPersistable->setName($getFuncName[$data]);
					$clientPersistable->setKey($keyName[$data]);
					$clientArray[$data] = array($clientPersistable);
				}
				return $clientArray;
			}
			else
			{
				return $status;
			}
		}
	}
	
	 /**
     * update client data
     * $param Request object [Request $request] and client-id
     * @return Client Persistable object/error message
     */	
	public function createPersistableChange(Request $request,$clientId)
	{
		$clientValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$clientPersistable;
		$clientArray = array();
		
		//get exception message 
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$clientValidate = new ClientValidate();
		if(count($_POST)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			for($data=0;$data<count($request->input());$data++)
			{
				//data get from body
				$clientPersistable = new ClientPersistable();
				$value[$data] = $request->input()[array_keys($request->input())[$data]];
				$key[$data] = array_keys($request->input())[$data];
				
				//trim an input 
				$clientTransformer = new ClientTransformer();
				$tRequest = $clientTransformer->trimUpdateData($key[$data],$value[$data]);
				if($tRequest==1)
				{
					return $exceptionArray['content'];
				}
				// get key value from trim array
				$tKeyValue[$data] = array_keys($tRequest[0])[0];
				$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
				
				//validation
				$status = $clientValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
				if($status=="Success")
				{
					// check data is string or not
					if(!is_numeric($tValue[$data]))
					{
						if (strpos($tValue[$data], '\'') !== FALSE)
						{
							$clientValue[$data] = str_replace("'","\'",$tValue[$data]);
						}
						else
						{
							$clientValue[$data] = $tValue[$data];
						}
					}
					else
					{
						$clientValue[$data] = $tValue[$data];
					}
					//flag=0...then data is valid(consider one data at a time)
					if($flag==0)
					{
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$clientPersistable->$setFuncName($clientValue[$data]);
						$clientPersistable->setName($getFuncName[$data]);
						$clientPersistable->setKey($tKeyValue[$data]);
						$clientPersistable->setClientId($clientId);
						$clientArray[$data] = array($clientPersistable);
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
				if($data==(count($request->input())-1))
				{
					if($flag==1)
					{
						return json_encode($errorStatus);
					}
					else
					{
						return $clientArray;
					}
				}
			
			}
		}
	}
	
	 /**
     * get the form-data and convert and validate it
     * $param Request header data
     * @return object
     */	
	public function dateConversion($headerData)
	{
		$objectClass = new stdclass();
		if(array_key_exists('invoicefromdate',$headerData))
		{
			//date format conversion
			$splitedFromDate = explode("-",trim($headerData['invoicefromdate'][0]));
			$invoiceTransformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			
			$splitedToDate = explode("-",trim($headerData['invoicetodate'][0]));
			$invoiceTransformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			
			//valiate Date
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$invoiceTransformFromDate))
			{
				return "invoice from-date is not valid";
			}
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$invoiceTransformToDate))
			{
				return "invoice to-date is not valid";
			}
			$objectClass->invoicefromdate = $invoiceTransformFromDate;
			$objectClass->invoicetodate = $invoiceTransformToDate;
		}
		if(array_key_exists('jobcardfromdate',$headerData))
		{
			//date format conversion
			$splitedFromDate = explode("-",trim($headerData['jobcardfromdate'][0]));
			$jobcardTransformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			
			$splitedToDate = explode("-",trim($headerData['jobcardtodate'][0]));
			$jobcardTransformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			
			//valiate Date
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$jobcardTransformFromDate))
			{
				return "invoice from-date is not valid";
			}
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$jobcardTransformToDate))
			{
				return "invoice to-date is not valid";
			}
			$objectClass->jobcardfromdate = $jobcardTransformFromDate;
			$objectClass->jobcardtodate = $jobcardTransformToDate;
		}
		return $objectClass;
	}
}