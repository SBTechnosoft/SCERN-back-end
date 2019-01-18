<?php
namespace ERP\Api\V1_0\Settings\Templates\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Settings\Templates\Services\TemplateService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Settings\Templates\Processors\TemplateProcessor;
use ERP\Core\Settings\Templates\Persistables\TemplatePersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Settings\Templates\TemplateModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TemplateController extends BaseController implements ContainerInterface
{
	/**
     * @var templateService
     * @var processor
     * @var request
     * @var templatePersistable
     */
	private $templateService;
	private $processor;
	private $request;
	private $templatePersistable;	
	
	/**
	 * get and invoke method is of ContainerInterface method
	 */		
    public function get($id,$name)
	{
		// echo "get";
	}
	public function invoke(callable $method)
	{
		// echo "invoke";
	}
	
	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function store(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == 'POST')
			{
				$processor = new TemplateProcessor();
				$templatePersistable = new TemplatePersistable();		
				$templateService= new TemplateService();
				$templatePersistable = $processor->createPersistable($this->request);
				
				if($templatePersistable[0][0]=='[')
				{
					return $templatePersistable;
				}
				else if(is_array($templatePersistable))
				{
					$status = $templateService->insert($templatePersistable);
					return $status;
				}
				else
				{
					return $templatePersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param  int  $templateId
     */
    public function getData(Request $request,$templateId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($templateId==null)
			{	
				$templateService= new TemplateService();
				$status = $templateService->getAllTemplateData();
				return $status;
			}
			else
			{	
				$templateService= new TemplateService();
				$status = $templateService->getTemplateData($templateId);
				return $status;
			} 
		}
		else
		{
			return $authenticationResult;
		}		
    }
	
	/**
     * get the specified resource.
     * @param  int  $companyId
     */
    public function getTemplateData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$templateType="all";
			$templateService= new TemplateService();
			$status = $templateService->getSpecificData($companyId,$templateType);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * Update the specified resource in storage.
     * @param  Request object[Request $request]
     * @param  branch_id
     */
	public function update(Request $request,$templateId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			$processor = new TemplateProcessor();
			$templatePersistable = new TemplatePersistable();
			$templateModel = new TemplateModel();		
			$result = $templateModel->getData($templateId);
			
			//get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$templatePersistable = $processor->createPersistableChange($this->request,$templateId);
				//here two array and string is return at a time
				if(is_array($templatePersistable))
				{
					$templateService= new TemplateService();	
					$status = $templateService->update($templatePersistable);
					return $status;
				}
				else
				{
					return $templatePersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
}
