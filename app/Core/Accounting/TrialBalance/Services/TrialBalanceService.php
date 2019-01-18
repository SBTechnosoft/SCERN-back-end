<?php	
namespace ERP\Core\Accounting\TrialBalance\Services;
use ERP\Model\Accounting\TrialBalance\TrialBalanceModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\TrialBalance\Entities\EncodeTrialBalanceData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TrialBalanceService extends AbstractService
{
    /**
     * @var trialBalanceService
	 * $var trialBalanceModel
     */
    private $trialBalanceService;
    private $trialBalanceModel;
	
    /**
     * @param TrialBalanceService $trialBalanceModel
     */
    public function initialize(TrialBalanceService $trialBalanceModel)
    {		
		echo "init";
    }
	
    /**
     * @param TrialBalancePersistable $persistable
     */
    public function create(TrialBalancePersistable $persistable)
    {
		return "create method of TrialBalanceService";
		
    }
	
	/**
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
	public function getData($companyId)
	{
		//get data
		$trialBalance = new TrialBalanceModel();
		$result = $trialBalance->getTrialBalanceData($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0)
		{
			return $result;
		}
		else
		{
			$encoded = new EncodeTrialBalanceData();
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