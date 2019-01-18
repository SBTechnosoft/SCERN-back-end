<?php	
namespace ERP\Core\Accounting\ProfitLoss\Services;
use ERP\Model\Accounting\ProfitLoss\ProfitLossModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\ProfitLoss\Entities\EncodeProfitLossData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfitLossService extends AbstractService
{
    /**
     * @var profitLossService
	 * $var profitLossModel
     */
    private $profitLossService;
    private $profitLossModel;
	
    /**
     * @param ProfitLossService $profitLossModel
     */
    public function initialize(ProfitLossService $profitLossModel)
    {		
		echo "init";
    }
	
    /**
     * @param ProfitLossPersistable $persistable
     */
    public function create(ProfitLossPersistable $persistable)
    {
		return "create method of ProfitLossService";
		
    }
	
	/**
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
	public function getData($companyId)
	{
		//get data
		$profitLossModel = new ProfitLossModel();
		$result = $profitLossModel->getProfitLossData($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0)
		{
			return $result;
		}
		else
		{
			$encoded = new EncodeProfitLossData();
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