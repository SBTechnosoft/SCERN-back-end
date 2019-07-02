<?php
namespace ERP\Api\V1_0\Settings\Expenses\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Settings\Expenses\Persistables\ExpensePersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Settings\Expenses\Validations\ExpenseValidate;
use ERP\Api\V1_0\Settings\Expenses\Transformers\ExpenseTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Ledgers\Entities\LedgerArray;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Companies\Services\CompanyService;
use ERP\Api\V1_0\Accounting\Ledgers\Controllers\LedgerController;
use Illuminate\Container\Container;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExpenseProcessor extends BaseProcessor
{
	/**
     * @var expensePersistable
	 * @var request
     */
	private $expensePersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Expense Array / Error Message Array / Exception Message
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$expenseArray = array();
		$expenseValue = array();
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
			$expenseTransformer = new ExpenseTransformer();
			$tRequest = $expenseTransformer->trimInsertData($this->request);
			
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$expenseValidate = new ExpenseValidate();
				$status = $expenseValidate->validate($tRequest);
				if($status=="Success")
				{
					$ledgerGroupId ='';
					$ledgerArray = new LedgerArray();
					$expenseGroupArray = $ledgerArray->expenseLedgerArray();
					$ledgerGroupId = isset($expenseGroupArray[$tRequest['expense_group_type']]) ? $expenseGroupArray[$tRequest['expense_group_type']] : 0;

					$ledgerStatus = $this->insertDefaultLedger($tRequest['company_id'], $tRequest['expense_name'] ,$ledgerGroupId, $request->header());

					if(strcmp($msgArray['500'],$ledgerStatus)==0 || strcmp($msgArray['content'],$ledgerStatus)==0)
					{
						return $ledgerStatus;
					}

					$tRequest['ledger_id'] = json_decode($ledgerStatus)[0]->ledger_id;

					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$expenseValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$expenseValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$expenseValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}

					// set data to the persistable object
					for($data=0;$data<count($expenseValue);$data++)
					{
						//set the data in persistable object
						$expensePersistable = new ExpensePersistable();
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
						// make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$expensePersistable->$setFuncName($expenseValue[$data]);
						$expensePersistable->setName($getFuncName[$data]);
						$expensePersistable->setKey($keyName[$data]);
						$expenseArray[$data] = array($expensePersistable);
					}
					return $expenseArray;
				}
				else
				{
					return $expenseArray;
				}
			}
		}
	}
	
	 /**
     * update data
     * $param Request object [Request $request] and Expense Id
     * @return Expense Array / Error Message Array / Exception Message
     */
	public function createPersistableChange(Request $request,$expenseId, $expenseData)
	{
		$expenseValue = array();
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$expensePersistable;
		$expenseArray = array();
		$expenseValidate = new ExpenseValidate();
		$status;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$expenseDataArray = json_decode($expenseData,true)[0];
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$expenseName = '';
		$expenseGroupType = '';
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
					$expensePersistable = new ExpensePersistable();
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					//trim an input 
					$expenseTransformer = new ExpenseTransformer();
					$tRequest = $expenseTransformer->trimUpdateData($key[$data],$value[$data]);
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
						$status = $expenseValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						
						//enter data is valid(one data validate status return)
						if($status=="Success")
						{
							if ($key[$data]=='expenseName') 
							{
								$expenseName = $tValue[$data];
							}
							if ($key[$data]=='expenseGroupType') 
							{
								$expenseGroupType = $tValue[$data];
							}
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$expenseValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$expenseValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$expenseValue[$data] = $tValue[$data];
							}
							//flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								//make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$expensePersistable->$setFuncName($expenseValue[$data]);
								$expensePersistable->setName($getFuncName[$data]);
								$expensePersistable->setKey($tKeyValue[$data]);
								$expensePersistable->setExpenseId($expenseId);
								$expenseArray[$data] = array($expensePersistable);
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

								// update ledger
								// $expenseGroupType
								$ledgerArray = new LedgerArray();
								$expenseGroupArray = $ledgerArray->expenseLedgerArray();
								$ledgerGroupId = isset($expenseGroupArray[$expenseGroupType]) 
													? $expenseGroupArray[$expenseGroupType] : 0;

								if (isset($expenseDataArray['ledger_id']) && $expenseDataArray['ledger_id']) 
								{
									$ledgerId = $expenseDataArray['ledger_id'];
									$ledgerStatus = $this->updateDefaultLedger($expenseDataArray['ledger_id'], $expenseName, $ledgerGroupId, $expenseDataArray, $request->header());
									if (strcmp($ledgerStatus, $exceptionArray['200'])!=0) 
									{
										return $ledgerStatus;
									}
								}
								else
								{
									$ledgerStatus = $this->insertDefaultLedger($expenseDataArray['company_id'], $expenseName, $ledgerGroupId, $request->header());

									if(strcmp($exceptionArray['500'],$ledgerStatus)==0 || strcmp($exceptionArray['content'],$ledgerStatus)==0)
									{
										return $ledgerStatus;
									}

									$ledgerId = json_decode($ledgerStatus)[0]->ledger_id;
								}
								$expenseArrayCount = count($expenseArray);
								$expensePersistable = new ExpensePersistable();

								$expensePersistable->setLedgerId($ledgerId);
								$expensePersistable->setName('getLedgerId');
								$expensePersistable->setKey('ledger_id');
								$expensePersistable->setExpenseId($expenseId);

								$expenseArray[$expenseArrayCount] = array($expensePersistable);
								return $expenseArray;
							}
						}
					}
				}
			}
		}
	}

	/**
     * insert expense ledger
     * $param Request object [Request $request]
     * @return LedgerId / Exception Message
     */
	public function insertDefaultLedger($companyId,$ledgerName,$ledgerGroupId, $headers)
	{
		$amountTypeEnum = new AmountTypeEnum();
		$enumAmountTypeArray = $amountTypeEnum->enumArrays();
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$ledgerArray=array();
		$companyService = new CompanyService();
		$exceptionMessage = new ExceptionMessage();
		$exceptionArray = $exceptionMessage->messageArrays();
		$companyJson = $companyService->getCompanyData($companyId);
		if (strcmp($companyJson,$exceptionArray['404'])==0) 
		{
			return $companyJson;
		}
		$companyDataArray = json_decode($companyJson);
		$ledgerArray['ledgerName']=$ledgerName;
		$ledgerArray['address1']='';
		$ledgerArray['address2']='';
		$ledgerArray['contactNo']='';
		$ledgerArray['emailId']='';
		$ledgerArray['invoiceNumber']='';
		$ledgerArray['stateAbb']= $companyDataArray->city->stateAbb;
		$ledgerArray['cityId']=$companyDataArray->city->cityId;
		$ledgerArray['companyId']=$companyId;
		$ledgerArray['balanceFlag']=$constantArray['openingBalance'];
		$ledgerArray['amount']=0;
		$ledgerArray['amountType']=$constantArray['credit'];
		$ledgerArray['ledgerGroupId']=$ledgerGroupId;
		$ledgerArray['clientName']='';
		$ledgerArray['outstandingLimit']='0.0000';
		$ledgerArray['outstandingLimit']=$enumAmountTypeArray['creditType'];
		$ledgerController = new LedgerController(new Container());
		$method=$constantArray['postMethod'];
		$path=$constantArray['ledgerUrl'];

		$ledgerRequest = Request::create($path,$method,$ledgerArray);
		$ledgerRequest->headers->set('authenticationtoken',$headers['authenticationtoken'][0]);
		$processedData = $ledgerController->store($ledgerRequest);
		return $processedData;
	}
	/**
     * update expense ledger
     * $param Request object [Request $request]
     * @return LedgerId / Exception Message
     */
	public function updateDefaultLedger($ledgerId,$ledgerName,$ledgerGroupId, $ledgerDecoded, $headers)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$updateFlag = 1;
		//update ledger data
		$ledgerArray=array();
		// $ledgerArray['ledgerName']=$tRequest['client_name'];
		
		$ledgerArray['ledgerName']=$ledgerName;
		$ledgerArray['ledgerGroupId']=$ledgerGroupId;
		if ($updateFlag==1) 
		{
			$ledgerController = new LedgerController(new Container());
			$method=$constantArray['postMethod'];
			$path=$constantArray['ledgerUrl'].'/'.$ledgerId;
			$ledgerRequest = Request::create($path,$method,$ledgerArray);
			$ledgerRequest->headers->set('authenticationtoken',$headers['authenticationtoken'][0]);
			$processedData = $ledgerController->update($ledgerRequest,$ledgerId);
			return $processedData;
		}
		else
		{
			return $msgArray['200'];
		}
	}
}