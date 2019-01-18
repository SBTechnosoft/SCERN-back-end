<?php	
namespace ERP\Core\Accounting\BalanceSheet\Services;
use ERP\Model\Accounting\BalanceSheet\BalanceSheetModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\BalanceSheet\Entities\EncodeBalanceSheetData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BalanceSheetService extends AbstractService
{
    /**
     * @var balanceSheetService
	 * $var balanceSheetModel
     */
    private $balanceSheetService;
    private $balanceSheetModel;
	
    /**
     * @param BalanceSheetService $balanceSheetModel
     */
    public function initialize(BalanceSheetService $balanceSheetModel)
    {		
		echo "init";
    }
	
    /**
     * @param BalanceSheetPersistable $persistable
     */
    public function create(BalanceSheetPersistable $persistable)
    {
		return "create method of BalanceSheetService";
		
    }
	
	/**
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
	public function getData($companyId)
	{
		//get data
		$balanceSheet = new BalanceSheetModel();
		$result = $balanceSheet->getBalanceSheetData($companyId);

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0)
		{
			return $result;
		}
		else
		{
			$encoded = new EncodeBalanceSheetData();
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