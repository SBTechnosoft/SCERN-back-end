<?php
namespace ERP\Core\Accounting\SalesReturns\Services;

use ERP\Core\Accounting\Bills\Persistables\BillPersistable;
use ERP\Exceptions\ExceptionMessage;
use ERP\Http\Requests;
use Illuminate\Http\Request;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Accounting\SalesReturns\SalesReturnModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SalesReturnService
{
    /**
     * @var billService
	 * $var billModel
     */
    private $billService;
    private $billModel;
	
    /**
     * @param LedgerService $ledgerService
     */
    public function initialize(LedgerService $ledgerService)
    {		
		echo "init";
    }
	
    /**
     * @param LedgerPersistable $persistable
     */
    public function create(LedgerPersistable $persistable)
    {
		return "create method of LedgerService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param BillPersistable $persistable
     * @return status/error message
     */
	public function insert()
	{
		$billArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$billArray = func_get_arg(0);
		$requestInput = func_get_arg(1);
		if(is_array($billArray))
		{
			$getNameArray = $billArray[0];
			$persistable = $billArray[1];

			foreach ($getNameArray as $key => $getFunName) {
				$salesReturnArray[$key] = $persistable->$getFunName();
			}

			$salesReturnArray['jf_id'] = $persistable->getJfId();
			$salesReturnArray['product_array'] = $persistable->getProductArray();
			$salesReturnModel = new SalesReturnModel();
			$status = $salesReturnModel->insertData($salesReturnArray,$requestInput);
			//get exception message
			return $status;
		}
	}
}