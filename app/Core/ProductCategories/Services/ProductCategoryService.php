<?php
namespace ERP\Core\ProductCategories\Services;

use ERP\Core\ProductCategories\Persistables\ProductCategoryPersistable;
use ERP\Core\ProductCategories\Entities\ProductCategory;
use ERP\Model\ProductCategories\ProductCategoryModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\ProductCategories\Entities\EncodeData;
use ERP\Core\ProductCategories\Entities\EncodeAllData;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategoryService extends AbstractService
{
    /**
     * @var productCategoryService
	 * $var productCategoryModel
     */
    private $productCategoryService;
    private $productCategoryModel;
	
    /**
     * @param ProductCategoryService $productCategoryService
     */
    public function initialize(ProductCategoryService $productCategoryService)
    {		
		echo "init";
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param array
     * @return status
     */
	public function insert()
	{
		$productCatArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$productCatArray = func_get_arg(0);
		for($data=0;$data<count($productCatArray);$data++)
		{
			$funcName[$data] = $productCatArray[$data][0]->getName();
			$getData[$data] = $productCatArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $productCatArray[$data][0]->getkey();
		}
		//data pass to the model object for insert
		$productCatModel = new ProductCategoryModel();
		$status = $productCatModel->insertData($getData,$keyName);
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
		$productCatArray = array();
		$productCatArrayResult = func_get_arg(0);
		$productCatArray = $productCatArrayResult['dataArray'];
		
		for($arrayData=0;$arrayData<count($productCatArray);$arrayData++)
		{
			$funcName = array();
			$getData = array();
			$keyName = array();
			for($data=0;$data<count($productCatArray[$arrayData]);$data++)
			{
				$funcName[$data] = $productCatArray[$arrayData][$data][0]->getName();
				$getData[$data] = $productCatArray[$arrayData][$data][0]->$funcName[$data]();
				$keyName[$data] = $productCatArray[$arrayData][$data][0]->getkey();
			}
			array_push($getArrayData,$getData);
			array_push($keyArrayData,$keyName);
		}
		//data pass to the model object for insert
		$productCatModel = new ProductCategoryModel();
		$status = $productCatModel->insertBatchData($getArrayData,$keyArrayData,$productCatArrayResult['errorArray']);
		return $status;
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getAllProductCatData()
	{
		$productCategoryModel = new ProductCategoryModel();
		$status = $productCategoryModel->getAllData();
		
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
	public function getBulkProductCatData($productCategoryIds)
	{
		$productCategoryModel = new ProductCategoryModel();
		$status = $productCategoryModel->getAllBulkData($productCategoryIds);
		
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
	public function getProductCatData($productCategoryId)
	{
		$productCategoryModel = new ProductCategoryModel();
		$status = $productCategoryModel->getData($productCategoryId);
		
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
     * @param ProductCategoryPersistable $persistable
     * @param updateOptions $options [optional]
     * @return status
     */
    public function update()
    {
		$productCatArray = array();
		$getData = array();
		$funcName = array();
		$productCatArray = func_get_arg(0);
		for($data=0;$data<count($productCatArray);$data++)
		{
			$funcName[$data] = $productCatArray[$data][0]->getName();
			$getData[$data] = $productCatArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $productCatArray[$data][0]->getkey();
		}
		$productCatId = $productCatArray[0][0]->getProductCategoryId();
		//data pass to the model object for update
		$productCategoryModel = new ProductCategoryModel();
		$status = $productCategoryModel->updateData($getData,$keyName,$productCatId);
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
    public function delete(ProductCategoryPersistable $persistable)
    {      
		$productCatId = $persistable->getId();
        $productCategoryModel = new ProductCategoryModel();
		$status = $productCategoryModel->deleteData($productCatId);
		return $status;
    }   
}