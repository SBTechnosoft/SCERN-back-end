<?php
namespace ERP\Api\V1_0\Authenticate\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Authenticate\Services\AuthenticateService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Authenticate\Processors\AuthenticateProcessor;
use ERP\Core\Authenticate\Persistables\AuthenticatePersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class AuthenticateController extends BaseController implements ContainerInterface
{
	/**
     * @var authenticateService
     * @var processor
     * @var request
     * @var authenticatePersistable
     */
	private $authenticateService;
	private $processor;
	private $request;
	private $authenticatePersistable;	
	
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
		$this->request = $request;
		
		// check the requested Http method
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// insert
		if($requestMethod == 'POST')
		{
			$processor = new AuthenticateProcessor();
			$authenticatePersistable = new AuthenticatePersistable();		
			$authenticateService= new AuthenticateService();			
			$authenticatePersistable = $processor->createPersistable($this->request);
			if(is_object($authenticatePersistable) || is_array($authenticatePersistable))
			{
				$status = $authenticateService->insert($authenticatePersistable);
				return $status;
			}
			else
			{
				return $authenticatePersistable;
			}
		}
	}
	
	/**
     * get the specified resource.
     */
    public function getAllData()
    {
		$authenticationService= new AuthenticateService();
		$status = $authenticationService->getAllData();
		return $status;
	}
	
	/**
     * get the specified resource.
     */
    public function getData($userId)
    {
		$authenticationService= new AuthenticateService();
		$status = $authenticationService->getData($userId);
		return $status;
	}
}
