<?php	
namespace ERP\Core\Accounting\Ledgers\Services;
use ERP\Core\Accounting\Ledgers\Persistables\LedgerPersistable;
use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Accounting\Ledgers\Entities\EncodeData;
use ERP\Core\Accounting\Ledgers\Entities\EncodeAllData;
use ERP\Core\Accounting\Ledgers\Entities\EncodeTrnAllData;
use ERP\Core\Accounting\Ledgers\Entities\EncodeTransactionAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerService extends AbstractService
{
    /**
     * @var ledgerService
	 * $var ledgerModel
     */
    private $ledgerService;
    private $ledgerModel;
	
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
     * @param LedgerPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$flag=0;
		$ledgerArray = array();
		$getData = array();
		$dataArray = array();
		$key = array();
		$funcName = array();
		$ledgerArray = func_get_arg(0);
		$ledgerData=0;
		$balanceData=0;
		$ledgerKeyNameExt = array();
		
		for($data=0;$data<count($ledgerArray);$data++)
		{
			$funcName[$data] = $ledgerArray[$data][0]->getName();
			$getData[$data] = $ledgerArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $ledgerArray[$data][0]->getkey();
			if(strcmp($keyName[$data],"balance_flag")==0 || strcmp($keyName[$data],"amount")==0 || strcmp($keyName[$data],"amount_type")==0)
			{
				$ledgerFuncNameExt[$balanceData] = $funcName[$data];
				$ledgerGetDataExt[$balanceData] = $getData[$data];
				$ledgerKeyNameExt[$balanceData] = $keyName[$data];
				$balanceData++;
			}
			else
			{
				$ledgerFuncName[$ledgerData] = $funcName[$data];
				$ledgerGetData[$ledgerData] = $getData[$data];
				$ledgerKeyName[$ledgerData] = $keyName[$data];
				$ledgerData++;
			}
		}
		if(count($ledgerKeyNameExt)!=0)
		{
			//data pass to the model object for insert
			$ledgerModel = new LedgerModel();
			$status = $ledgerModel->insertAllData($ledgerGetData,$ledgerKeyName,$ledgerGetDataExt,$ledgerKeyNameExt);
			return $status;
		}
		else
		{
			//data pass to the model object for insert
			$ledgerModel = new LedgerModel();
			$status = $ledgerModel->insertData($ledgerGetData,$ledgerKeyName);
			return $status;
		}
	}
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllLedgerData()
	{
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getAllData();
		
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data  as per given id and call the model for database selection opertation
     * @param $ledgerId
     * @return status
     */
	public function getLedgerData($ledgerId)
	{
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getData($ledgerId);
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['404'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($status);
			return $encodeData;
		}
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getAllData($ledgerGrpId)
	{
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getAllLedgerData($ledgerGrpId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getLedgerDetail($companyId)
	{
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getLedgerDetail($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$fileSizeArray = $exception->messageArrays();
		if(strcmp($status,$fileSizeArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get transaction data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getLedgerTransactionDetail($ledgerId)
	{
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getLedgerTransactionDetail($ledgerId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeTransactionAllData();
			$encodeAllData = $encoded->getEncodedAllData($status,$ledgerId);
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data between given date and call the model for database selection opertation
     * @return status
     */
	public function getData()
	{
		$processArray = array();
		$processArray = func_get_arg(0);
		$companyId = func_get_arg(1);
		$ledgerType = func_get_arg(2);
		
		//get data
		$fromDate = $processArray->getFromDate();
		$toDate = $processArray->getToDate();
		$ledgerModel = new LedgerModel();
		
		//get ledger_id
		$ledgerIdDetail = $ledgerModel->getLedgerId($companyId,$ledgerType);
		
		//get ledger data between given date
		$status = $ledgerModel->getLedgerData($fromDate,$toDate,$companyId,$ledgerType);
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeTrnAllData();
			$encodeAllData = $encoded->getEncodedAllData($status,json_decode($ledgerIdDetail));
			return $encodeAllData;
		}
	}
	
	/**
     * get current-year data and call the model for database selection opertation
     * @return status
     */
	public function getCurrentYearData()
	{
		$processArray = array();
		$ledgerType = func_get_arg(1);
		$companyId = func_get_arg(0);
		
		$ledgerModel = new LedgerModel();
		
		//get ledger_id
		$ledgerIdDetail = $ledgerModel->getLedgerId($companyId,$ledgerType);
		
		//get ledger data between given date
		$status = $ledgerModel->getCurrentYearData($companyId,$ledgerType);
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $status;
		}
		else
		{
			
			$encoded = new EncodeTrnAllData();
			$encodeAllData = $encoded->getEncodedAllData($status,json_decode($ledgerIdDetail));
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data as per given ledger-grp id & company_id and call the model for database 		selection operation
     * @return status
     */
	public function getDataAsLedgerGrp()
	{
		$processArray = array();
		$ledgerGrpArray = func_get_arg(0);
		$companyId = func_get_arg(1);
		
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getDataAsPerLedgerGrp($ledgerGrpArray,$companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(is_array($status))
		{
			
			for($arrayData=0;$arrayData<count($status);$arrayData++)
			{
				$encoded = new EncodeAllData();
				$encodeAllData = $encoded->getEncodedAllData(json_encode($status[$arrayData]));
				$ledgerArray[$arrayData]=json_decode($encodeAllData);
			}
			return json_encode($ledgerArray);
		}
		else
		{
			return $status;
		}
	}
	
	/**
     * get all the data as per given ledger-name id & company_id and call the model for database 		selection operation
     * @return status
     */
	public function getLedgerDataAsName($requestHeader,$companyId)
	{
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->getDataAsPerLedgerName($requestHeader['ledgername'][0],$companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(is_object(json_decode($status)))
		{
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($status);
			return $encodeData;
		}
		else
		{
			return $status;
		}
	}
	
    /**
     * get the data from persistable object and call the model for database update opertation
     * @param LedgerPersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form.
     * @return status
     */
    public function update()
    {
		$ledgerArray = array();
		$getData = array();
		$funcName = array();
		$ledgerArray = func_get_arg(0);
		for($data=0;$data<count($ledgerArray);$data++)
		{
			$funcName[$data] = $ledgerArray[$data][0]->getName();
			$getData[$data] = $ledgerArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $ledgerArray[$data][0]->getkey();
		}
		$ledgerId = $ledgerArray[0][0]->getLedgerId();
		// data pass to the model object for update
		$ledgerModel = new LedgerModel();
		$status = $ledgerModel->updateData($getData,$keyName,$ledgerId);
		return $status;	
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
	
    /**
     * @param delete
     * @param LedgerPersistable $persistable
     */
    public function delete(LedgerPersistable $persistable)
    {      
		$ledgerId = $persistable->getLedgerId();
        $ledgerModel = new LedgerModel();
		$status = $ledgerModel->deleteData($ledgerId);
		return $status;
    }   
}