<?php
namespace ERP\Api\V1_0\Settings\Professions\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Settings\Professions\Services\ProfessionService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Settings\Professions\Processors\ProfessionProcessor;
use ERP\Core\Settings\Professions\Persistables\ProfessionPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Settings\Professions\ProfessionModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfessionController extends BaseController implements ContainerInterface
{
	/**
     * @var professionService
     * @var processor
     * @var request
     * @var professionPersistable
     */
	private $professionService;
	private $processor;
	private $request;
	private $professionPersistable;	
	
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
				$processor = new ProfessionProcessor();
				$professionService= new ProfessionService();			
				$professionPersistable = $processor->createPersistable($this->request);
				if($professionPersistable[0][0]=='[')
				{
					return $professionPersistable;
				}
				else if(is_array($professionPersistable))
				{
					$status = $professionService->insert($professionPersistable);
					return $status;
				}
				else
				{
					return $professionPersistable;
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
     * @param  int  $professionId
     */
    public function getData(Request $request,$professionId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($professionId==null)
			{	
				$professionService= new ProfessionService();
				$status = $professionService->getAllProfessionData();
				return $status;
			}
			else
			{	
				$professionService= new ProfessionService();
				$status = $professionService->getProfessionData($professionId);
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
     * @param  branch_id
     */
	public function update(Request $request,$professionId)
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
			$processor = new ProfessionProcessor();
			$professionPersistable = new ProfessionPersistable();
			$professionModel = new ProfessionModel();		
			$result = $professionModel->getData($professionId);
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($result,$exceptionArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$professionPersistable = $processor->createPersistableChange($this->request,$professionId);
				//here two array and string is return at a time
				if(is_array($professionPersistable))
				{
					$professionService= new ProfessionService();	
					$status = $professionService->update($professionPersistable);
					return $status;
				}
				else
				{
					return $professionPersistable;
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
     * @param  profession_id     
     */
    public function destroy(Request $request,$professionId)
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
			$processor = new ProfessionProcessor();
			$professionPersistable = new ProfessionPersistable();		
			$professionService= new ProfessionService();	
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			$professionModel = new ProfessionModel();
			$result = $professionModel->getData($professionId);
			if(strcmp($result,$exceptionArray['404'])==0)
			{	
				return $result;
			}
			else
			{
				$status = $professionService->delete($professionId);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
