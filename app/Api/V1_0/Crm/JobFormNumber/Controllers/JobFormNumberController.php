<?php
namespace ERP\Api\V1_0\Crm\JobFormNumber\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Crm\JobFormNumber\Services\JobFormNumberService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Crm\JobFormNumber\Processors\JobFormNumberProcessor;
use ERP\Core\Crm\JobFormNumber\Persistables\JobFormNumberPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumberController extends BaseController implements ContainerInterface
{
	/**
     * @var jobFormService
     * @var processor
     * @var request
     * @var jobFormPersistable
     */
	private $jobFormNumberService;
	private $processor;
	private $request;
	private $jobFormNumberPersistable;	
	
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
				$processor = new JobFormNumberProcessor();
				
				$jobFormNumberPersistable = new JobFormNumberPersistable();		
				$jobFormNumberService= new JobFormNumberService();		
				$jobFormNumberPersistable = $processor->createPersistable($this->request);
				
				if($jobFormNumberPersistable[0][0]=='[')
				{
					return $jobFormNumberPersistable;
				}
				else if(is_array($jobFormNumberPersistable))
				{
					$status = $jobFormNumberService->insert($jobFormNumberPersistable);
					return $status;
				}
				else
				{
					return $jobFormNumberPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get all the data
     * @param  int  $invoiceId
     */
    public function getAllData(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$jobFormNumberService= new JobFormNumberService();
			$status = $jobFormNumberService->getAllJobFormNumberData();
			return $status;
		}
		else
		{
			return $authenticationResult;
		}	
    }
	
	/**
     * get the latest job form number data.
     * @param  int  $companyId
     */
    public function getLatestData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$jobFormNumberService= new JobFormNumberService();
			$status = $jobFormNumberService->getLatestJobFormNumberData($companyId);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
}
