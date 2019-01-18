<?php
namespace ERP\Api\V1_0\Accounting\PurchaseBills\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Accounting\PurchaseBills\Services\PurchaseBillService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Accounting\PurchaseBills\Processors\PurchaseBillProcessor;
use ERP\Core\Accounting\PurchaseBills\Persistables\PurchaseBillPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Settings\Templates\Services\TemplateService;
// use ERP\Core\Accounting\PurchaseBills\Entities\PurchaseBillMpdf;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Settings\Templates\Entities\TemplateTypeEnum;
// use ERP\Core\Settings\InvoiceNumbers\Services\InvoiceService;
// use ERP\Api\V1_0\Settings\InvoiceNumbers\Controllers\InvoiceController;
use Illuminate\Container\Container;
// use ERP\Api\V1_0\Documents\Controllers\DocumentController;
use ERP\Model\Accounting\PurchaseBills\PurchaseBillModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PurchaseBillController extends BaseController implements ContainerInterface
{
	/**
     * @var purchaseBillService
     * @var processor
     * @var request
     * @var purchaseBillPersistable
     */
	private $purchaseBillService;
	private $processor;
	private $request;
	private $purchaseBillPersistable;	
	
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
			// get exception message
			$exception = new ExceptionMessage();
			$msgArray = $exception->messageArrays();
			if($requestMethod == 'POST')
			{
				if(count($_POST)==0)
				{
					return $msgArray['204'];
				}
				else
				{
					$processor = new PurchaseBillProcessor();
					$purchaseBillPersistable = $processor->createPersistable($this->request);
					if(is_array($purchaseBillPersistable))
					{
						$purchaseBillService= new PurchaseBillService();
						$status = $purchaseBillService->insert($purchaseBillPersistable,$this->request);
						return $status;
					}
					else
					{
						return $purchaseBillPersistable;
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
	 * update the purchase-bill data as per given purchase-id
	 * @param  Request object[Request $request]
	 * @return array-data/error message
	*/
	public function update(Request $request,$purchaseId)
	{
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			// get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			$file = $request->file();
			if(count($_POST)==0 && count($file)==0)
			{
				return $exceptionArray['204'];
			}
			else
			{
				$processor = new PurchaseBillProcessor();
				$purchaseBillPersistable = $processor->createPersistableChange($request,$purchaseId);
				if(is_array($purchaseBillPersistable))
				{
					$purchaseBillService= new PurchaseBillService();
					$status = $purchaseBillService->update($purchaseBillPersistable,$purchaseId);
					return $status;
				}
				else
				{
					return $purchaseBillPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * get the specified resource 
	 * @param  Request object[Request $request] and companyId
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function getData(Request $request,$companyId)
	{
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		// get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if(array_key_exists('fromdate',$request->header()) && array_key_exists('todate',$request->header()))
			{
				$processor = new PurchaseBillProcessor();
				$purchaseBillPersistable = new PurchaseBillPersistable();
				$purchaseBillPersistable = $processor->getPersistableData($request->header());
				
				if(!is_object($purchaseBillPersistable))
				{
					return $purchaseBillPersistable;
				}
				$data = $purchaseBillPersistable;
			}
			else if(array_key_exists('billnumber',$request->header()))
			{
				$data = $request->header();
			}
			$purchaseBillService = new PurchaseBillService();
			$status = $purchaseBillService->getData($data,$companyId);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * get the purchase-bill data as per given parameter
	 * @param  Request object[Request $request]
	 * @return array-data/error message
	*/
	public function getPurchaseBillData(Request $request)
	{
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		// get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$purchaseBillService= new PurchaseBillService();
			$status = $purchaseBillService->getPurchaseBillData($request->header());
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * delete the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function destroy(Request $request,$purchaseId)
	{
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		// get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$purchaseBillModel = new PurchaseBillModel();
			$deletePurchaseBillResult = $purchaseBillModel->deletePurchaseBillData($request->header(),$purchaseId);
			return $deletePurchaseBillResult;
		}
		else
		{
			return $authenticationResult;
		}
	}
}
