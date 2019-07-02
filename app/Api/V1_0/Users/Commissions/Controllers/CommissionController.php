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
    public function storeItemwise(Request $request,$commissionId=null)
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
				//get exception message
				$exception = new ExceptionMessage();
				$fileSizeArray = $exception->messageArrays();
				// create or update staff commission
				$processor = new CommissionProcessor();
				$commissionPersistable = new CommissionPersistable();
				$commissionService= new CommissionService();

				$commissionPersistable = $processor->createItemwisePersistable($this->request);
				if($commissionPersistable[0][0]=='[')
				{
					return $commissionPersistable;
				}
				else if(is_array($commissionPersistable))
				{
					if ($commissionId == null || $commissionId == '') {
						$status = $commissionService->insertItemwise($commissionPersistable);
						return $status;
					}else{
						$result = $commissionService->getItemwise($commissionId);
						if (strcmp($result,$fileSizeArray['404'])==0) {
							return $fileSizeArray['404'];
						}else{
							$status = $commissionService->updateItemwise($commissionPersistable,$commissionId);
							return $status;
						}
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
     * @param  int  $commissionId
     */
    public function getItemwiseData(Request $request,$commissionId=null)
    {
    	$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($commissionId==null || $commissionId == '')
			{
				$companyId = null;
				$productId = null;
				$commissionService= new CommissionService();
				if (array_key_exists('companyId', $request->header())) {
					$companyId = $request->header('companyId');
					if (array_key_exists('productId', $request->header())) {
						$productId = $request->header('productId');
					}
				}
				$status = $commissionService->getItemwiseByProduct($productId,$companyId);
				return $status;
			}
			else
			{
				$commissionService= new CommissionService();
				$status = $commissionService->getItemwise($commissionId);
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
    /**
     * get the specified resource.
     * @param  int  $userId
     */
    public function getReportData(Request $request,$userId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if (array_key_exists('companyid', $request->header())) 
			{
				$headerData = $request->header();
				$commissionService= new CommissionService();
				$status = $commissionService->getUserCommissionReport($userId,$headerData);
				return $status;
			}
			else
			{
				return $exceptionArray['204'];
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	public function destroyItemwise(Request $request,$commissionId)
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
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			$commissionService= new CommissionService();
			$result = $commissionService->getItemwise($commissionId);
			if (strcmp($result,$fileSizeArray['404'])==0) {
				return $fileSizeArray['404'];
			}else{
				$status = $commissionService->deleteItemwise($commissionId);
				return $status;
			}
		}
	}
}