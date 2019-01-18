<?php
namespace ERP\Api\V1_0\Crm\JobForm\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Crm\JobForm\Persistables\JobFormPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Crm\JobForm\Validations\JobFormValidate;
use ERP\Api\V1_0\Crm\JobForm\Transformers\JobFormTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use Illuminate\Container\Container;
use ERP\Api\V1_0\Clients\Controllers\ClientController;
use ERP\Model\Clients\ClientModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormProcessor extends BaseProcessor
{
	/**
     * @var jobFormPersistable
	 * @var request
     */
	private $jobFormPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Job-Form Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$jobFormArray = array();
		$jobFormValue = array();
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
			$jobFormTransformer = new JobFormTransformer();
			$tRequest = $jobFormTransformer->trimInsertData($this->request);
			
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//validation
				$jobFormValidate = new JobFormValidate();
				$status = $jobFormValidate->validate($tRequest);
				if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$tRequest['entryDate']))
				{
					return $msgArray['invalidEntryDate'];
				}
				if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$tRequest['deliveryDate']))
				{
					return $msgArray['invalidDeliveryDate'];
				}
				$trimRequest = $tRequest;
				$tRequest= array_splice($tRequest,0,-1);
				
				$clientResult = $this->clientProcess($tRequest);
				if(!is_array($clientResult))
				{
					return $clientResult;
				}
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$jobFormValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$jobFormValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$jobFormValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					$jobFormPersistable=array();
					for($data=0;$data<count($trimRequest[0]);$data++)
					{
						$jobFormPersistable[$data] = new JobFormPersistable();
						$jobFormPersistable[$data]->setClientName($trimRequest['clientName']);
						$jobFormPersistable[$data]->setAddress($trimRequest['address']);
						$jobFormPersistable[$data]->setContactNo($trimRequest['contactNo']);
						$jobFormPersistable[$data]->setEmailId($trimRequest['emailId']);
						$jobFormPersistable[$data]->setJobCardNo($trimRequest['jobCardNo']);
						$jobFormPersistable[$data]->setLabourCharge($trimRequest['labourCharge']);
						$jobFormPersistable[$data]->setServiceType($trimRequest['serviceType']);
						$jobFormPersistable[$data]->setEntryDate($trimRequest['entryDate']);
						$jobFormPersistable[$data]->setDeliveryDate($trimRequest['deliveryDate']);
						$jobFormPersistable[$data]->setAdvance($trimRequest['advance']);
						$jobFormPersistable[$data]->setTotal($trimRequest['total']);
						$jobFormPersistable[$data]->setTax($trimRequest['tax']);
						$jobFormPersistable[$data]->setPaymentMode($trimRequest['paymentMode']);
						$jobFormPersistable[$data]->setStateAbb($trimRequest['stateAbb']);
						$jobFormPersistable[$data]->setCityId($trimRequest['cityId']);
						$jobFormPersistable[$data]->setCompanyId($trimRequest['companyId']);
						$jobFormPersistable[$data]->setBankName($trimRequest['bankName']);
						$jobFormPersistable[$data]->setChequeNo($trimRequest['chequeNo']);
						$jobFormPersistable[$data]->setClientId($clientResult['clientId']);
						
						$jobFormPersistable[$data]->setProductId($trimRequest[0][$data]['productId']);
						$jobFormPersistable[$data]->setProductName($trimRequest[0][$data]['productName']);
						$jobFormPersistable[$data]->setProductInformation($trimRequest[0][$data]['productInformation']);
						$jobFormPersistable[$data]->setQty($trimRequest[0][$data]['qty']);
						$jobFormPersistable[$data]->setPrice($trimRequest[0][$data]['price']);
						$jobFormPersistable[$data]->setDiscountType($trimRequest[0][$data]['discountType']);
						$jobFormPersistable[$data]->setDiscount($trimRequest[0][$data]['discount']);
					}
					return $jobFormPersistable;
				}
				else
				{
					return $status;
				}
			}
		}
	}
	
	/**
     * get the trim-data and check the client as per given contact-no
     * $param Request object [Request $request]
     * @return status/error-message
     */	
	public function clientProcess($tRequest)
	{
		//get constant variables array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		
		$isDisplayEnum = new IsDisplayEnum();
		$isDisplayArray = $isDisplayEnum->enumArrays();
		//get contact-number from input data
		if(!array_key_exists('contactNo',$tRequest))
		{
			$contactNo="";
		}
		else
		{
			$contactNo = $tRequest['contactNo'];
		}
		if($contactNo=="" || $contactNo==0)
		{
			$clientArray = array();
			$clientArray['clientName']=$tRequest['clientName'];
			$clientArray['companyName']='';
			$clientArray['emailId']=$tRequest['emailId'];
			$clientArray['contactNo']=$tRequest['contactNo'];
			$clientArray['address1']=$tRequest['address'];
			$clientArray['isDisplay']=$isDisplayArray['display'];
			$clientArray['stateAbb']=$tRequest['stateAbb'];
			$clientArray['cityId']=$tRequest['cityId'];
			$clientController = new ClientController(new Container());
			$method=$constantArray['postMethod'];
			$path=$constantArray['clientUrl'];
			$clientRequest = Request::create($path,$method,$clientArray);
			$processedData = $clientController->store($clientRequest);
			if(strcmp($processedData,$msgArray['content'])==0)
			{
				return $processedData;
			}
			$clientId = json_decode($processedData)[0]->client_id;
		}
		else
		{
			//check client is exists by contact-number
			$clientModel = new ClientModel();
			$clientData = $clientModel->getClientData($contactNo);
			
			if(is_array(json_decode($clientData)))
			{
				$encodedClientData = json_decode($clientData);
				$clientId = $encodedClientData[0]->client_id;
				//update client data
				$clientArray = array();
				$clientArray['clientName']=$tRequest['clientName'];
				$clientArray['companyName']='';
				$clientArray['emailId']=$tRequest['emailId'];
				$clientArray['contactNo']=$tRequest['contactNo'];
				$clientArray['address1']=$tRequest['address'];
				$clientArray['isDisplay']=$isDisplayArray['display'];
				$clientArray['stateAbb']=$tRequest['stateAbb'];
				$clientArray['cityId']=$tRequest['cityId'];
				$clientController = new ClientController(new Container());
				$method=$constantArray['postMethod'];
				$path=$constantArray['clientUrl'].'/'.$clientId;
				$clientRequest = Request::create($path,$method,$clientArray);
				$processedData = $clientController->updateData($clientRequest,$clientId);
				if(strcmp($processedData,$msgArray['200'])!=0)
				{
					return $processedData;
				}
			}
			else
			{
				$clientArray = array();
				$clientArray['clientName']=$tRequest['clientName'];
				$clientArray['companyName']='';
				$clientArray['contactNo']=$tRequest['contactNo'];
				$clientArray['emailId']=$tRequest['emailId'];
				$clientArray['address1']=$tRequest['address'];
				$clientArray['isDisplay']=$isDisplayArray['display'];
				$clientArray['stateAbb']=$tRequest['stateAbb'];
				$clientArray['cityId']=$tRequest['cityId'];
				$clientController = new ClientController(new Container());
				$method=$constantArray['postMethod'];
				$path=$constantArray['clientUrl'];
				$clientRequest = Request::create($path,$method,$clientArray);
				$processedData = $clientController->store($clientRequest);
				if(strcmp($processedData,$msgArray['content'])==0)
				{
					return $processedData;
				}
				$clientId = json_decode($processedData)[0]->client_id;
			}
		}
		$clientIdArray = array();
		$clientIdArray['clientId'] = $clientId;
		return $clientIdArray;
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