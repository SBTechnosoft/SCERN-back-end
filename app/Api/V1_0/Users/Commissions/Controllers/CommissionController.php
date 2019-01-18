<?php
namespace ERP\Api\V1_0\Users\Commissions\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Users\Commissions\Services\CommissionService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Users\Commissions\Processors\CommissionProcessor;
use ERP\Core\Users\Commissions\Persistables\CommissionPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Users\Commissions\CommissionModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use Illuminate\Support\Facades\Log;
/**
 * @author Hiren Faldu <hiren.f@siliconbrain.in>
 */
class CommissionController extends BaseController implements ContainerInterface
{
	/**
     * @var CommissionService
     * @var processor
     * @var request
     * @var CommissionPersistable
     */
	private $commissionService;
	private $processor;
	private $request;
	private $commissionPersistable;	
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
	public function storeOrUpdate(Request $request,$userId)
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
			$this->request = $request;
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert or update
			if($requestMethod == 'POST')
			{
				$commissionModel = new CommissionModel();
				$result = $commissionModel->getData($userId);
				//get exception message
				$exception = new ExceptionMessage();
				$fileSizeArray = $exception->messageArrays();
				// create or update staff commission
				$processor = new CommissionProcessor();
				$commissionPersistable = new CommissionPersistable();
				$commissionService= new CommissionService();
				$commissionPersistable = $processor->createPersistable($this->request);
				if($commissionPersistable[0][0]=='[')
				{
					return $commissionPersistable;
				}
				else if(is_array($commissionPersistable))
				{
					if (strcmp($result,$fileSizeArray['404'])==0) {
						$status = $commissionService->insert($commissionPersistable);
						return $status;
					}else{
						$status = $commissionService->update($commissionPersistable);
						return $status;
					}
					
				}
				else
				{
					return $commissionPersistable;
				}
			}
		}
    }
    /**
     * get the specified resource.
     * @param  int  $userId
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
				$commissionService= new CommissionService();
				$status = $commissionService->getAllCommissionData();
				return $status;
			}
			else
			{
				$commissionService= new CommissionService();
				$status = $commissionService->getCommissionData($userId);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
}