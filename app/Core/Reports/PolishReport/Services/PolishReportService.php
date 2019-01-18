<?php	
namespace ERP\Core\Reports\PolishReport\Services;
use ERP\Model\Reports\PolishReport\PolishReportModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Reports\PolishReport\Entities\EncodePolishReportData;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Bills\Entities\EncodeAllData;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PolishReportService extends AbstractService
{
    /**
     * @var polishReportService
	 * $var polishReportModel
     */
    private $polishReportService;
    private $polishReportModel;
	
    /**
     * @param PolishReportService $polishReportService
     */
    public function initialize(PolishReportService $polishReportService)
    {		
		echo "init";
    }
	
    /**
     * @param PolishReportPersistable $persistable
     */
    public function create(PolishReportPersistable $persistable)
    {
		return "create method of PolishReportService";
		
    }
	
	/**
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
	public function getData($companyId,$fromDate,$toDate)
	{
		//get data
		$polishReport = new PolishReportModel();
		$result = $polishReport->getPolishReportData($companyId,$fromDate,$toDate);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0)
		{
			return $result;
		}
		else
		{
			$encodeAllData = new EncodeAllData();
			$encodingResult = $encodeAllData->getEncodedAllData($result);
			return $encodingResult;
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