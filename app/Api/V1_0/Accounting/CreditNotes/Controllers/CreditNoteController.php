<?php
namespace ERP\Api\V1_0\Accounting\CreditNotes\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Support\Service\ContainerInterface;
use Illuminate\Container\Container;
// Common deps
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
// CreditNote Deps
use ERP\Api\V1_0\Accounting\CreditNotes\Processors\CreditNoteProcessor;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Core\Accounting\CreditNotes\Services\CreditNoteService;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CreditNoteController extends BaseController implements ContainerInterface
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
	private $constant;
	private $constantVars;
	private $exception;
	private $messages;
	private $authenticate;
	
	function __construct(Request $request, Container $container) {
		parent::__construct($container);
		$this->request = $request;
		//get constant array
		$this->constant = new ConstantClass();
		$this->constantVars = $this->constant->constantVariable();
		// get exception message
		$this->exception = new ExceptionMessage();
		$this->messages = $this->exception->messageArrays();
		$this->authenticate = new TokenAuthentication();
		$response = $this->authenticate->authenticate($this->request->header());
		// $this->persistable = new CreditNotePersistable();
		if(strcmp($this->constantVars['success'],$response)!=0)
		{
			return $response;
		}

		$this->service = new CreditNoteService();
	}
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
    public function store($saleId)
    {
		if (!$this->request->isMethod('post') || !count($this->request->all())) {
			return $this->messages['204'];
		}

		$billModel = new BillModel();
		$status = $billModel->getSaleIdData($saleId);
		if(strcmp($status, $this->messages['404']) == 0) {
			return $status;
		}
		$salesData = json_decode($status, true);
		if(!is_array($salesData)) {
			return $this->messages['content'];
		}
		$salesData = $salesData[0];

		$this->processor = new CreditNoteProcessor($this->request);

		$status = $this->processor->createPersistable($salesData);
		if (!is_array($status)) {
			return $status;
		}
		return $this->service->insert($status);
	}
}
