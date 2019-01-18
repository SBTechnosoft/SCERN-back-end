<?php
namespace ERP\Core\Accounting\LedgerGroups\Services;

use ERP\Core\Accounting\LedgerGroups\Persistables\LedgerGroupPersistable;
use ERP\Core\Accounting\LedgerGroups\Entities\LedgerGroup;
use ERP\Model\Accounting\LedgerGroups\LedgerGroupModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\LedgerGroups\Entities\EncodeData;
use ERP\Core\Accounting\LedgerGroups\Entities\EncodeAllData;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerGroupService extends AbstractService
{
    /**
     * @var ledgerGroupService
	 * $var ledgerGroupModel
     */
    private $ledgerGroupService;
    private $ledgerGroupModel;
	
    /**
     * @param LedgerGrpService $ledgerGrpService
     */
    public function initialize(LedgerGroupService $ledgerGrpService)
    {		
		echo "init";
    }
	
    /**
     * @param LedgerGroupPersistable $persistable
     */
    public function create(LedgerGroupPersistable $persistable)
    {
		return "create method of LedgerGroupService";
		
    }
	
	/**
     * get all the data and call the model for database selection opertation
     * @return status
     */
	public function getAllLedgerGrpData()
	{
		$ledgerGrpModel = new LedgerGroupModel();
		$status = $ledgerGrpModel->getAllData();
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
		return $status;
	}
	
	/**
     * get all the data  as per given id and call the model for database selection opertation
     * @param $ledgerGrpId
     * @return status
     */
	public function getLedgerGrpData($ledgerGrpId)
	{
		$ledgerGrpModel = new LedgerGroupModel();
		$status = $ledgerGrpModel->getData($ledgerGrpId);
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