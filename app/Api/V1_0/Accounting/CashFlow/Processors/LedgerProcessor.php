<?php
namespace ERP\Api\V1_0\Accounting\Ledgers\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Accounting\Ledgers\Persistables\LedgerPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Accounting\Ledgers\Validations\LedgerValidate;
use ERP\Api\V1_0\Accounting\Ledgers\Transformers\LedgerTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Journals\Validations\BuisnessLogic;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerProcessor extends BaseProcessor
{
	/**
     * @var ledgerPersistable
	 * @var request
     */
	private $ledgerPersistable;
	private $request;    
	
	function __construct(){
		// echo " [LedgerProcessor Cunst. Cash Flow]--";
	}

    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Ledger Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$ledgerArray = array();
		$ledgerValue = array();
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
			$ledgerTransformer = new LedgerTransformer();
			$tRequest = $ledgerTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}
			else
			{
				$buisnessLogic = new BuisnessLogic();
				$businessResult = $buisnessLogic->validateLedgerData($tRequest);
			}
			if(is_array($businessResult))
			{
				//validation
				$ledgerValidate = new LedgerValidate();
				$status = $ledgerValidate->validate($tRequest);
				
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$ledgerValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$ledgerValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$ledgerValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($ledgerValue);$data++)
					{
						//set the data in persistable object
						$ledgerPersistable = new LedgerPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						//make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$ledgerPersistable->$setFuncName($ledgerValue[$data]);
						$ledgerPersistable->setName($getFuncName[$data]);
						$ledgerPersistable->setKey($keyName[$data]);
						$ledgerArray[$data] = array($ledgerPersistable);
					}
					return $ledgerArray;
				}
				else
				{
					return $status;
				}
			}
			else
			{
				return $businessResult;
			}
		}
	}
	public function createPersistableChange(Request $request,$ledgerId)
	{
		$ledgerValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$ledgerPersistable;
		$ledgerArray = array();
		$ledgerValidate = new LedgerValidate();
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
					$buisnessFlag=0;
					//data get from body
					$ledgerPersistable = new LedgerPersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					//trim an input 
					$ledgerTransformer = new LedgerTransformer();
					$tRequest = $ledgerTransformer->trimUpdateData($key[$data],$value[$data]);
					//get data from trim array
					if($tRequest==1)
					{
						return $exceptionArray['content'];
					}
					else
					{
						if(array_key_exists("ledger_name",$tRequest[0]))
						{
							$buisnessFlag=1;
							$buisnessLogic = new BuisnessLogic();
							$businessResult = $buisnessLogic->validateUpdateLedgerData($tRequest,$ledgerId);
						}
					}
					if($buisnessFlag==1 && !is_array($businessResult))
					{
						return $businessResult;
					}
					else
					{
						$tKeyValue[$data] = array_keys($tRequest[0])[0];
						$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
						
						//validation
						$status = $ledgerValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$ledgerValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$ledgerValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$ledgerValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$ledgerPersistable->$setFuncName($ledgerValue[$data]);
								$ledgerPersistable->setName($getFuncName[$data]);
								$ledgerPersistable->setKey($tKeyValue[$data]);
								$ledgerPersistable->setLedgerId($ledgerId);
								$ledgerArray[$data] = array($ledgerPersistable);
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
								return $ledgerArray;
							}
						}
					}
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$ledgerPersistable = new LedgerPersistable();		
			$ledgerPersistable->setLedgerId($ledgerId);			
			return $ledgerPersistable;
		}
	}	
	
	//trim data & set header data (fromdate and todate data)
	public function createPersistableData(Request $request)
	{
		$this->request = $request;	
		
		//trim an input 
		$ledgerTransformer = new LedgerTransformer();
		$tRequest = $ledgerTransformer->trimDateData($this->request);
		
		$ledgerPersistable = new LedgerPersistable();
		$ledgerPersistable->setFromdate($tRequest['fromDate']);
		$ledgerPersistable->setTodate($tRequest['toDate']);
		
		return $ledgerPersistable;
	}
}