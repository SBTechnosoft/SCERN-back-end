<?php
namespace ERP\Api\V1_0\Companies\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Companies\Persistables\CompanyPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ERP\Core\Companies\Validations\CompanyValidate;
use ERP\Api\V1_0\Companies\Transformers\CompanyTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use Illuminate\Container\Container;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyProcessor extends BaseProcessor
{   
	/**
     * @var companyPersistable
	 * @var request
     */
	private $companyPersistable;
	private $request;    
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Branch Persistable object
     */	
	public function createPersistable(Request $request)
	{	
		date_default_timezone_set("Asia/Calcutta");
		$this->request = $request;	
		$documentArray = array();
		$companyValue = array();
		$tKeyValue = array();
		$keyName = array();
		$value = array();
		$data=0;
		$docFlag=0;
		$documentName="";
		$documentUrl="";
		$documentFormat="";
		$documentSize="";
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$file = $request->file();
		if(count($_POST)==0 && count($file)==0)
		{
			return $msgArray['204'];
		}
		if(in_array(true,$file))
		{
			$constantClass = new ConstantClass();
			$constantArray = $constantClass->constantVariable();
			$documentController =new DocumentController(new Container());
			$processedData = $documentController->insertUpdate($request,$constantArray['documentUrl']);
			if(is_array($processedData))
			{
				$docFlag=1;
			}
			else
			{
				return $processedData;
			}
		}
		//trim an input 
		$companyTransformer = new CompanyTransformer();
		$tRequest = $companyTransformer->trimInsertData($this->request);
		//validation
		$companyValidate = new CompanyValidate();
		if($tRequest==1)
		{
			return $msgArray['content'];
		}
		else
		{
			//validation
			$validateResult = $companyValidate->companyNameValidate($tRequest);
		}
		if(is_array($validateResult))
		{
			//validation
			$status = $companyValidate->validate($tRequest);
			if($status=="Success")
			{
				foreach ($tRequest as $key => $value)
				{
					if(!is_numeric($value))
					{
						if (strpos($value, '\'') !== FALSE)
						{
							$companyValue[$data]= str_replace("'","\'",$value);
							$keyName[$data] = $key;
						}
						else
						{
							$companyValue[$data] = $value;
							$keyName[$data] = $key;
						}
					}
					else
					{
						$companyValue[$data]= $value;
						$keyName[$data] = $key;
					}
					$data++;
				}
				// set data to the persistable object
				for($data=0;$data<count($companyValue);$data++)
				{
					//set the data in persistable object
					$companyPersistable = new CompanyPersistable();	
					$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));
					
					//make function name dynamically
					$setFuncName = 'set'.$str;
					$getFuncName[$data] = 'get'.$str;
					$companyPersistable->$setFuncName($companyValue[$data]);
					$companyPersistable->setName($getFuncName[$data]);
					$companyPersistable->setKey($keyName[$data]);
					$companyArray[$data] = array($companyPersistable);
					if($data==(count($companyValue)-1))
					{
						if($docFlag==1)
						{
							$companyArray[$data+1]=$processedData;
						}
					}
				}
				return $companyArray;
			}
			else
			{
				return $status;
			}
		}
		else
		{
			//entered company-name is already exists..
			return $msgArray['ExistCompanyName'];
		}
	}
	
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * $param company_id
     * @return Company Persistable object
     */	
	public function createPersistableChange(Request $request,$companyId)
	{
		date_default_timezone_set("Asia/Calcutta");
		$errorCount=0;
		$flag=0;
		$errorStatus=array();
		$docFlag=0;
		$documentName="";
		$documentArray = array();
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		// update
		if($requestMethod == 'POST')
		{
			$companyPersistable;
			$companyArray = array();
			$companyValue = array();
			$companyValidate = new CompanyValidate();
			$status;
			
			//get exception message 
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			$file = $request->file();
			//if data is not available in update request
			if(count($_POST)==0 && count($file)==0)
			{
				
				$status = $exceptionArray['204'];
				return $status;
			}
			//get constant document-url from document
			$documentUrl =  new ConstantClass();
			$documentArray = $documentUrl->constantVariable();
			
			//file uploading
			if(in_array(true,$file))
			{
				$documentController =new DocumentController(new Container());
				$processedData = $documentController->insertUpdate($request,$documentArray['documentUrl']);
				if(is_array($processedData))
				{
					$docFlag=1;
				}
				else
				{
					return $processedData;
				}
			}
			if(count($_POST)!=0)
			{
				for($data=0;$data<count($_POST);$data++)
				{
					// set the data in persistable object
					$companyPersistable = new CompanyPersistable();	
					$value[$data] = $_POST[array_keys($_POST)[$data]];
					$key[$data] = array_keys($_POST)[$data];
					
					// trim an input 
					$companyTransformer = new CompanyTransformer();
					$tRequest = $companyTransformer->trimUpdateData($key[$data],$value[$data]);
					
					if($tRequest==1)
					{
						return $exceptionArray['content'];
					}
					else
					{
						// get data from trim array
						$tKeyValue[$data] = array_keys($tRequest[0])[0];
						$tValue[$data] = $tRequest[0][array_keys($tRequest[0])[0]];
						
						if(strcmp($tKeyValue[$data],"company_name")==0)
						{
							$validateResult = $companyValidate->companyNameValidateUpdate($tRequest[0],$companyId);
							if(!is_array($validateResult))
							{
								return $exceptionArray['content'];
							}
						}
						// validation
						$status = $companyValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[0]);
						
						// enter data is valid(one data validate status return)
						if($status=="Success")
						{
							// check data is string or not
							if(!is_numeric($tValue[$data]))
							{
								if (strpos($tValue[$data], '\'') !== FALSE)
								{
									$companyValue[$data] = str_replace("'","\'",$tValue[$data]);
								}
								else
								{
									$companyValue[$data] = $tValue[$data];
								}
							}
							else
							{
								$companyValue[$data] = $tValue[$data];
							}
							// flag=0...then data is valid(consider one data at a time)
							if($flag==0)
							{
								$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
								// make function name dynamically
								$setFuncName = 'set'.$str;
								$getFuncName[$data] = 'get'.$str;
								$companyPersistable->$setFuncName($companyValue[$data]);
								$companyPersistable->setName($getFuncName[$data]);
								$companyPersistable->setKey($tKeyValue[$data]);
								$companyPersistable->setCompanyId($companyId);
								$companyArray[$data] = array($companyPersistable);
								
								if($data==(count($_POST)-1))
								{
									if($docFlag==1)
									{
										$companyArray[$data+1]=$processedData;
									}
								}
							}
						}
						// enter data is not valid
						else
						{
							// if flag==1 then enter data is not valid ..so error is stored in an array.
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
								return $companyArray;
							}
						}
					}
				}
			}
			else
			{
				if($docFlag==1)
				{
					$companyArray[0]=$processedData;
					return $companyArray;
				}
			}
		}
		//delete
		else if($requestMethod == 'DELETE')
		{
			$companyPersistable = new CompanyPersistable();		
			$companyPersistable->setId($companyId);			
			return $companyPersistable;
		}
	}	
}