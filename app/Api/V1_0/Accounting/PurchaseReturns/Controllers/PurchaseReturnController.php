<?php
namespace ERP\Api\V1_0\Accounting\PurchaseReturns\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Accounting\PurchaseBills\Persistables\PurchaseBillPersistable;
use ERP\Api\V1_0\Accounting\PurchaseReturns\Processors\PurchaseReturnProcessor;
use ERP\Core\Accounting\PurchaseReturns\Services\PurchaseReturnService;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class PurchaseReturnController extends BaseController implements ContainerInterface
{
	/**
	 * @var service
	 * @var processor
	 * @var request
	 * @var persistable
	 */
	private $service;
	private $processor;
	private $request;
	private $persistable;
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
	public function store(Request $request,$purchaseId)
	{
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		$this->request = $request;
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		if(strcmp($constantArray['success'],$authenticationResult)!=0){
			return $authenticationResult;
		}

		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();

		if (!$this->request->isMethod('post') || !count($this->request->all())) {
			return $exceptionArray['204'];
		}

		$this->processor = new PurchaseReturnProcessor();
		$this->persistable = new PurchaseBillPersistable();
		$this->persistable = $this->processor->createPersistable($this->request, $purchaseId);
		if(!is_array($this->persistable) && !is_object($this->persistable)) {
			return $this->persistable;
		}
		
		$this->service = new PurchaseReturnService();
		return $this->service->insert($this->persistable, $this->request);
	}
}