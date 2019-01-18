<?php	
namespace ERP\Core\Accounting\CashFlow\Services;
use ERP\Model\Accounting\CashFlow\CashFlowModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\CashFlow\Entities\EncodeCashFlowData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CashFlowService extends AbstractService
{
    /**
     * @var cashFlowService
	 * $var cashFlowModel
     */
    private $cashFlowService;
    private $cashFlowModel;
	
    /**
     * @param CashFlowService $cashFlowModel
     */
    public function initialize(CashFlowService $cashFlowModel)
    {		
		echo "init";
    }
	
    /**
     * @param CashFlowPersistable $persistable
     */
    public function create(CashFlowPersistable $persistable)
    {
		return "create method of CashFlowService";
		
    }
	
	/**
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
	public function getData($companyId)
	{
		//get data
		$cashFlowModel = new CashFlowModel();
		$result = $cashFlowModel->getCashFlowData($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0)
		{
			return $result;
		}
		else
		{
			$encoded = new EncodeCashFlowData();
			$encodeAllData = $encoded->getEncodedAllData($result);
			return $encodeAllData;
		}
	}
	
	/**
     * get and invoke method is of Container Interface method
     * @param int $id,$name
     */
    public function get($id,$name)
    {
		echo "get";		
    }   
	public function invoke(callable $method)
	{
		echo "invoke";
	}
}