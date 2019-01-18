<?php
namespace ERP\Api\V1_0\Users\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Users\Services\UserService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Users\Processors\UserProcessor;
use ERP\Core\Users\Persistables\UserPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Model\Users\UserModel;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Authenticate\AuthenticateModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserController extends BaseController implements ContainerInterface
{
	/**
     * @var userService
     * @var processor
     * @var request
     * @var userPersistable
     */
	private $userService;
	private $processor;
	private $request;
	private $userPersistable;	
	
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
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			//get user-type from active_session & user_mst for checking user is admin or not?
			$authenticationModel = new AuthenticateModel();
			$userType = $authenticationModel->getUserType($request->header());

			if(strcmp($userType,$exceptionArray['content'])==0)
			{
				return $userType;
			}
			else
			{
				$this->request = $request;
				// check the requested Http method
				$requestMethod = $_SERVER['REQUEST_METHOD'];
				// insert
				if($requestMethod == 'POST')
				{
					$processor = new Userprocessor();
					$userPersistable = new UserPersistable();	
					$userService= new UserService();
					$userPersistable = $processor->createPersistable($this->request);
					if($userPersistable[0][0]=='[')
					{
						return $userPersistable;
					}
					else if(is_array($userPersistable))
					{
						$status = $userService->insert($userPersistable);
						return $status;
					}
					else
					{
						return $userPersistable;
					}
				}
				else
				{
					return $status;
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
     * @param  state_id
     */
    public function getData(Request $request,$userId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($userId==null)
			{
				$userService= new UserService();
				$status = $userService->getAllUserData($request);
				return $status;
			}
			else
			{	
				$userService= new UserService();
				$status = $userService->getUserData($userId);
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
     * @param  state_abb
     */
	public function update(Request $request,$userId)
    {    
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			//get user-type from active_session & user_mst for checking user is admin or not?
			$authenticationModel = new AuthenticateModel();
			$userType = $authenticationModel->getUserTypeForPermission($request->header());
			if(strcmp($userType,$exceptionArray['content'])==0)
			{
				return $userType;
			}
			else
			{
				$this->request = $request;	
				$processor = new UserProcessor();
				$userPersistable = new UserPersistable();		
				$userService= new UserService();
				$userModel = new UserModel();	
				$result = $userModel->getData($userId);
				// get exception message
				$exception = new ExceptionMessage();
				$exceptionArray = $exception->messageArrays();
				
				if(strcmp($result,$exceptionArray['404'])==0)
				{
					return $exceptionArray['404'];
				}
				else
				{
					$userPersistable = $processor->createPersistableChange($this->request,$userId);
					if(is_array($userPersistable))
					{
						$status = $userService->update($userPersistable);
						return $status;
					}
					else
					{
						return $userPersistable;
					}
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
     * @param  state_abb     
     */
    public function Destroy(Request $request,$userId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			//get user-type from active_session & user_mst for checking user is admin or not?
			$authenticationModel = new AuthenticateModel();
			$userType = $authenticationModel->getUserType($request->header());
			if(strcmp($userType,$exceptionArray['content'])==0)
			{
				return $userType;
			}
			else
			{
				$this->request = $request;
				$processor = new UserProcessor();
				$userPersistable = new UserPersistable();		
				$userService= new UserService();	
				
				$userModel = new UserModel();	
				$result = $userModel->getData($userId);
				
				// get exception message
				$exception = new ExceptionMessage();
				$exceptionArray = $exception->messageArrays();
				
				if(strcmp($result,$exceptionArray['404'])==0)
				{
					return $exceptionArray['404'];
				}
				else
				{		
					$userPersistable = $processor->createPersistableChange($this->request,$userId);
					$status = $userService->delete($userPersistable);
					return $status;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
