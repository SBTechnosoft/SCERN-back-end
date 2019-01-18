<?php
namespace ERP\Api\V1_0\Settings\Expenses\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Settings\Expenses\Services\ExpenseService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Settings\Expenses\Processors\ExpenseProcessor;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Settings\Expenses\ExpenseModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExpenseController extends BaseController implements ContainerInterface
{
	/**
     * @var expenseService
     * @var processor
     * @var request
     * @var expensePersistable
     */
	private $expenseService;
	private $processor;
	private $request;
	private $expensePersistable;	
	
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
				$processor = new ExpenseProcessor();
				$expenseService= new ExpenseService();			
				$expensePersistable = $processor->createPersistable($this->request);
				if($expensePersistable[0][0]=='[')
				{
					return $expensePersistable;
				}
				else if(is_array($expensePersistable))
				{
					$status = $expenseService->insert($expensePersistable);
					return $status;
				}
				else
				{
					return $expensePersistable;
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
     * @param  int  $expenseId
     */
    public function getData(Request $request,$expenseId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$expenseService= new ExpenseService();
			if($expenseId==null)
			{	
				$status = $expenseService->getAllExpenseData();
				return $status;
			}
			else
			{	
				$status = $expenseService->getExpenseData($expenseId);
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
	public function update(Request $request,$expenseId)
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
			$processor = new ExpenseProcessor();
			$expenseModel = new ExpenseModel();		
			$result = $expenseModel->getData($expenseId);
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($result,$exceptionArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$expensePersistable = $processor->createPersistableChange($this->request,$expenseId);
				//here two array and string is return at a time
				if(is_array($expensePersistable))
				{
					// print_r($expensePersistable);
					$expenseService= new ExpenseService();	
					$status = $expenseService->update($expensePersistable);
					return $status;
				}
				else
				{
					return $expensePersistable;
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
     * @param  expense_id     
     */
    public function destroy(Request $request,$expenseId)
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
			$expenseService= new ExpenseService();	
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			$expenseModel = new ExpenseModel();
			$result = $expenseModel->getData($expenseId);
			if(strcmp($result,$exceptionArray['404'])==0)
			{	
				return $result;
			}
			else
			{
				$status = $expenseService->delete($expenseId);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
