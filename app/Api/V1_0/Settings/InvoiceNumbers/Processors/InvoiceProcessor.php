<?php
namespace ERP\Api\V1_0\Settings\InvoiceNumbers\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Settings\InvoiceNumbers\Persistables\InvoicePersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Settings\InvoiceNumbers\Validations\InvoiceValidate;
use ERP\Api\V1_0\Settings\InvoiceNumbers\Transformers\InvoiceTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InvoiceProcessor extends BaseProcessor
{
	/**
     * @var invoicePersistable
	 * @var request
     */
	private $invoicePersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Invoice Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$invoiceArray = array();
		$invoiceValue = array();
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
			$invoiceTransformer = new InvoiceTransformer();
			$tRequest = $invoiceTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$invoiceValidate = new InvoiceValidate();
				$status = $invoiceValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$invoiceValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$invoiceValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$invoiceValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($invoiceValue);$data++)
					{
						//set the data in persistable object
						$invoicePersistable = new InvoicePersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$invoicePersistable->$setFuncName($invoiceValue[$data]);
						$invoicePersistable->setName($getFuncName[$data]);
						$invoicePersistable->setKey($keyName[$data]);
						$invoiceArray[$data] = array($invoicePersistable);
					}
					return $invoiceArray;
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
     * @return Invoice Persistable object
     */	
	public function createPersistableChange(Request $request,$invoiceId)
	{
		$invoiceValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$invoicePersistable;
		$invoiceArray = array();
		$invoiceValidate = new InvoiceValidate();
		$status;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// update
		if($requestMethod == 'POST')
		{
			//if data is not available in update request
			if(count($request->input())==0)
			{
				$status = $exceptionArray['204'];
				return $status;
			}
			//data is avalilable for update
			else
			{
				for($data=0;$data<count($request->input());$data++)
				{
					//data get from body
					$invoicePersistable = new InvoicePersistable();
					$value[$data] = $request->input()[array_keys($request->input())[$data]];
					$key[$data] = array_keys($request->input())[$data];
					
					//trim an input 
					$invoiceTransformer = new InvoiceTransformer();
					$tRequest = $invoiceTransformer->trimUpdateData($key[$data],$value[$data]);
					
					//get data from trim array
					if($tRequest==1)
					{
						return $exceptionArray['content'];
					}
					else
					{
						$tKeyValue[$data] = array_keys($tRequest[0])[0];
						$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
						
						//validation
						$status = $invoiceValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$invoiceValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$invoiceValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$invoiceValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$invoicePersistable->$setFuncName($invoiceValue[$data]);
								$invoicePersistable->setName($getFuncName[$data]);
								$invoicePersistable->setKey($tKeyValue[$data]);
								$invoicePersistable->setInvoiceId($invoiceId);
								$invoiceArray[$data] = array($invoicePersistable);
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
								return $invoiceArray;
							}
						}
					}
				}
			}
		}
	}	
}