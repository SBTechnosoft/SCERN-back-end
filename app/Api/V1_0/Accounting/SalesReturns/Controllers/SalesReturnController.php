<?php
namespace ERP\Api\V1_0\Accounting\SalesReturns\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use Illuminate\Container\Container;

use ERP\Api\V1_0\Accounting\SalesReturns\Processors\SalesReturnProcessor;
use ERP\Core\Accounting\Bills\Persistables\BillPersistable;
use ERP\Core\Accounting\SalesReturns\Services\SalesReturnService;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class SalesReturnController extends BaseController implements ContainerInterface
{
	/**
     * @var salesReturnService
     * @var processor
     * @var request
     * @var billPersistable
     */
	private $salesReturnService;
	private $processor;
	private $request;
	private $billPersistable;
	
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
	public function store(Request $request,$saleId)
	{
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		$this->request = $request;
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0){
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// get exception message
			$exception = new ExceptionMessage();
			$msgArray = $exception->messageArrays();
			// insert
			if($requestMethod == 'POST')
			{
				if (count($_POST)==0) {
					return $msgArray['204'];
				}
				$processor = new SalesReturnProcessor();
				$billPersistable = new BillPersistable();
				$billPersistable = $processor->createPersistable($this->request);
				if(is_array($billPersistable) || is_object($billPersistable))
				{
					$SalesReturnService= new SalesReturnService();
					return $SalesReturnService->insert($billPersistable,$this->request);

				}else{
					return $billPersistable;
				}
			}
		}else{
			return $authenticationResult;
		}
	}
}