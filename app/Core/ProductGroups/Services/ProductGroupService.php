<?php
namespace ERP\Core\ProductGroups\Services;

use ERP\Core\ProductGroups\Persistables\ProductGroupPersistable;
use ERP\Core\ProductGroups\Entities\ProductGroup;
use ERP\Model\ProductGroups\ProductGroupModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\ProductGroups\Entities\EncodeData;
use ERP\Core\ProductGroups\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupService extends AbstractService
{
    /**
     * @var productGroupService
	 * $var productGroupModel
     */
    private $productGroupService;
    private $productCategoryModel;
	
    /**
     * @param ProductGroupService $productGroupService
     */
    public function initialize(ProductGroupService $productGroupService)
    {		
		echo "init";
    }
	
    /**
     * @param ProductCategoryPersistable $persistable
     */
    public function create(ProductGroupPersistable $persistable)
    {
		return "create method of ProductGroupService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param array
     * @return status
     */
	public function insert()
	{
		$productGroupArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$productGroupArray = func_get_arg(0);
		for($data=0;$data<count($productGroupArray);$data++)
		{
			$funcName[$data] = $productGroupArray[$data][0]->getName();
			$getData[$data] = $productGroupArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $productGroupArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$productGrpModel = new ProductGroupModel();
		$status = $productGrpModel->insertData($getData,$keyName);
		return $status;
	}
	
	/**
     * get the data from persistable object and call the model for database insertion opertation
     * @param array
     * @return status
     */
	public function insertBatchData()
	{
		$getArrayData = array();
		$keyArrayData = array();
		$productGroupArrayResult = array();
		$productGroupArrayResult = func_get_arg(0);
		$productGroupArray = $productGroupArrayResult['dataArray'];
		for($arrayData=0;$arrayData<count($productGroupArray);$arrayData++)
		{
			$funcName = array();
			$getData = array();
			$keyName = array();
			for($data=0;$data<count($productGroupArray[$arrayData]);$data++)
			{
				$funcName[$data] = $productGroupArray[$arrayData][$data][0]->getName();
				$getData[$data] = $productGroupArray[$arrayData][$data][0]->$funcName[$data]();
				$keyName[$data] = $productGroupArray[$arrayData][$data][0]->getkey();
			}
			array_push($getArrayData,$getData);
			array_push($keyArrayData,$keyName);
		}
		//data pass to the model object for insert
		$productGrpModel = new ProductGroupModel();
		$status = $productGrpModel->insertBatchData($getArrayData,$keyArrayData,$productGroupArrayResult['errorArray']);
		return $status;
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getAllProductGrpData()
	{
		$productGroupModel = new ProductGroupModel();
		$status = $productGroupModel->getAllData();
		
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
	public function getBulkProductGrpData($productGroupIds)
	{
		$productGroupModel = new ProductGroupModel();
		$status = $productGroupModel->getAllBulkData($productGroupIds);
		
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
     * get all the data from the table and call the model for database selection opertation
     * @param $productCategoryId
     * @return status
     */
	public function getProductGrpData($productGroupId)
	{
		$productGroupModel = new ProductGroupModel();
		$status = $productGroupModel->getData($productGroupId);
		
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
     * get the data from persistable object and call the model for database update opertation
     * @param array
     * @param updateOptions $options [optional]
     * @return status
     */
    public function update()
    {
		$productGrpArray = array();
		$getData = array();
		$funcName = array();
		$productGrpArray = func_get_arg(0);
		for($data=0;$data<count($productGrpArray);$data++)
		{
			$funcName[$data] = $productGrpArray[$data][0]->getName();
			$getData[$data] = $productGrpArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $productGrpArray[$data][0]->getkey();
		}
		$productGrpId = $productGrpArray[0][0]->getProductGroupId();
		// data pass to the model object for update
		$productGrpModel = new ProductGroupModel();
		$status = $productGrpModel->updateData($getData,$keyName,$productGrpId);
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
     * @param int $id
     */
    public function delete(ProductGroupPersistable $persistable)
    {      
		$productGrpId = $persistable->getId();
        $productGrpModel = new ProductGroupModel();
		$status = $productGrpModel->deleteData($productGrpId);
		return $status;
    }   
}