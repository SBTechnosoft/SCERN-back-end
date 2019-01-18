<?php
namespace ERP\Api\V1_0\Companies\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Companies\Services\CompanyService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Companies\Processors\CompanyProcessor;
use ERP\Core\Companies\Persistables\CompanyPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Core\Companies\Validations\CompanyValidate;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Companies\CompanyModel;
use ERP\Entities\Constants\ConstantClass;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyController extends BaseController implements ContainerInterface
{
	/**
     * @var companyService
     * @var processor
     * @var request
     * @var companyPersistable
     */
	private $companyService;
	private $processor;
	private $request;
	private $companyPersistable;	
	
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
				$processor = new CompanyProcessor();
				$companyPersistable = new CompanyPersistable();		
				$companyService= new CompanyService();		
				$companyPersistable = $processor->createPersistable($this->request);
				if($companyPersistable[0][0]=='[')
				{
					return $companyPersistable;
				}
				else if(is_array($companyPersistable))
				{
					$status = $companyService->insert($companyPersistable);
					return $status;
				}
				else
				{
					return $companyPersistable;
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
     * @param  int  $companyId
     */
    public function getData(Request $request,$companyId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($companyId==null)
			{	
				$companyService= new CompanyService();
				$status = $companyService->getAllCompanyData();
				return $status;
			}
			else
			{	
				$companyService= new CompanyService();
				$status = $companyService->getCompanyData($companyId);
				return $status;
			}    
		}
		else
		{
			return $authenticationResult;
		}	
    }
	
    /**
     * Update the specified resource in storage.
     * @param  Request object[Request $request] 
     * @param  company_id 
     */
	public function update(Request $request,$companyId)
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
			$processor = new CompanyProcessor();
			$companyPersistable = new CompanyPersistable();	
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			//check for company is available in database...
			$companyModel = new CompanyModel();
			$result = $companyModel->getData($companyId);
			if(strcmp($result,$exceptionArray['404'])==0)
			{	
				return $result;
			}
			else
			{
				$companyPersistable = $processor->createPersistableChange($this->request,$companyId);
			
				$companyService= new CompanyService();
				if(is_array($companyPersistable))
				{
					$status = $companyService->update($companyPersistable,$companyId);
					return $status;
				}
				else
				{
					return $companyPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}	
	}
	
    /**
     * Remove the specified resource from storage.
     * @param  Request object[Request $request]     
     * @param  company_id     
     */
    public function destroy(Request $request,$companyId)
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
			$processor = new CompanyProcessor();
			$companyPersistable = new CompanyPersistable();		
			$companyService= new CompanyService();	
			
			//get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			
			$companyModel = new CompanyModel();
			$result = $companyModel->getData($companyId);
			if(strcmp($result,$fileSizeArray['404'])==0)
			{	
				return $result;
			}
			else
			{
				$companyPersistable = $processor->createPersistableChange($this->request,$companyId);
				$status = $companyService->delete($companyPersistable);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
