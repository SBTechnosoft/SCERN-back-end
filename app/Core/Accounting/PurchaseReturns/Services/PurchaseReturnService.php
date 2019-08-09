<?php
namespace ERP\Core\Accounting\PurchaseReturns\Services;

use ERP\Exceptions\ExceptionMessage;
use ERP\Http\Requests;
use Illuminate\Http\Request;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Accounting\PurchaseReturns\PurchaseReturnModel;
use ERP\Core\Accounting\PurchaseBills\Persistables\PurchaseBillPersistable;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class PurchaseReturnService
{
	/**
     * @var persistable
	 * @var model
    */
    private $persistable;
    private $model;
    /**
     * @param LedgerService $ledgerService
     */
    public function initialize(PurchaseReturnService $purchaseReturnService)
    {		
		echo "init";
    }
	
    /**
     * @param LedgerPersistable $persistable
     */
    public function create(PurchaseBillPersistable $persistable)
    {
		return "create method of LedgerService";
    }
    public function insert()
	{
		$dataArray = array();
		$getData = array();
		$dataArray = func_get_arg(0);
		$requestInput = func_get_arg(1);
		if(is_array($dataArray))
		{
			$getNameArray = $dataArray[0];
			$this->persistable = $dataArray[1];

			foreach ($getNameArray as $key => $getFunName) {
				$getData[$key] = $this->persistable->$getFunName();
			}

			$getData['jf_id'] = $this->persistable->getJfId();
			$getData['product_array'] = $this->persistable->getProductArray();
			$this->model = new PurchaseReturnModel();
			return $this->model->insertData($getData,$requestInput);
		}

		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		return $exceptionArray['500'];
	}
}