<?php
namespace ERP\Api\V1_0\Settings\QuotationNumbers\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Settings\QuotationNumbers\Persistables\QuotationPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Settings\QuotationNumbers\Validations\QuotationValidate;
use ERP\Api\V1_0\Settings\QuotationNumbers\Transformers\QuotationTransformer;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationProcessor extends BaseProcessor
{
	/**
     * @var quotationPersistable
	 * @var request
     */
	private $quotationPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Invoice Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$quotationArray = array();
		$quotationValue = array();
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
			$quotationTransformer = new QuotationTransformer();
			$tRequest = $quotationTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$quotationValidate = new QuotationValidate();
				$status = $quotationValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$quotationValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$quotationValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$quotationValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($quotationValue);$data++)
					{
						//set the data in persistable object
						$quotationPersistable = new QuotationPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$quotationPersistable->$setFuncName($quotationValue[$data]);
						$quotationPersistable->setName($getFuncName[$data]);
						$quotationPersistable->setKey($keyName[$data]);
						$quotationArray[$data] = array($quotationPersistable);
					}
					return $quotationArray;
				}
				else
				{
					return $status;
				}
			}
		}
	}
	
	public function createPersistableChange(Request $request,$quotationId)
	{
		$quotationValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$quotationPersistable;
		$quotationArray = array();
		$quotationValidate = new QuotationValidate();
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
					$quotationPersistable = new QuotationPersistable();
					$value[$data] = $request->input()[array_keys($request->input())[$data]];
					$key[$data] = array_keys($request->input())[$data];
					//trim an input 
					$quotationTransformer = new QuotationTransformer();
					$tRequest = $quotationTransformer->trimUpdateData($key[$data],$value[$data]);
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
						$status = $quotationValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$quotationValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$quotationValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$quotationValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$quotationPersistable->$setFuncName($quotationValue[$data]);
								$quotationPersistable->setName($getFuncName[$data]);
								$quotationPersistable->setKey($tKeyValue[$data]);
								$quotationPersistable->setQuotationId($quotationId);
								$quotationArray[$data] = array($quotationPersistable);
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
								return $quotationArray;
							}
						}
					}
				}
			}
		}
	}	
}