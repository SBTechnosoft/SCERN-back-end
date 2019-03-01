<?php
namespace ERP\Core\Merge\Services;

use ERP\Core\Support\Service\AbstractService;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Merge\Persistables\MergePersistable;
use ERP\Model\Merge\MergeModel;
use ERP\Model\Products\ProductModel;
use Exception;
use DB;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class MergeService extends AbstractService
{
    /**
     * @var mergeService
	 * $var mergeModel
     */
    private $mergeService;
    private $mergeModel;
	
    /**
     * @param MergeService $mergeService
     */
    public function initialize(MergeService $mergeService)
    {		
		echo "init";
    }
	
    /**
     * @param MergePersistable $persistable
     */
    public function create(MergePersistable $persistable)
    {
		return "create method of MergeService";
		
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
	public function mergeProduct($mergingProducts)
	{
		$fromProductId = $mergingProducts['from_product'];
		$toProductId = $mergingProducts['to_product'];
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$productModel = new ProductModel();
		$newProductStatus = $productModel->getData($toProductId);
		if (strcmp($exceptionArray['404'], $newProductStatus) == 0) {
			return $newProductStatus;
		}
		$newProductData = json_decode($newProductStatus,true);
		$mergeModel = new MergeModel();
		DB::beginTransaction();
		try{
			$status = $mergeModel->getProductTrnData($fromProductId);
			if (strcmp($exceptionArray['404'], $status) == 0) {
				throw new Exception($status);
			}
			$productTrnArray = json_decode($status,true);
			foreach ($productTrnArray as $productTrn) {
				$jf_id = $productTrn['jf_id'];
				$mergeArray = [
					'to_product' => $toProductId,
					'from_product' => $fromProductId,
					'new_product' => $newProductData
				];
				if ($productTrn['transaction_type']=='Balance') {
					$status = $mergeModel->deleteProductTrnById($productTrn['product_trn_id']);
					if (strcmp($status, $exceptionArray['500'])==0) {
						throw new Exception($status);
					}
					continue;
				}elseif ($productTrn['transaction_type']=='Inward') {
					if ($productTrn['bill_number'] != '') {
						$purchaseBillStatus = $this->changePurchaseBillData($mergeArray,[
							'jf_id'=> $jf_id
						],'productArray');
						if (strcmp($purchaseBillStatus, $exceptionArray['500'])==0) {
							throw new Exception($purchaseBillStatus);
						}
						if (strcmp($purchaseBillStatus, $exceptionArray['404'])==0) {
							goto salesReturnUpdate;
						}
					}else{
						salesReturnUpdate:
						$salesReturnStatus = $this->changeSalesReturnData($mergeArray,[
							'jf_id'=> $jf_id
						],'productArray');
						if (strcmp($salesReturnStatus, $exceptionArray['500'])==0) {
							throw new Exception($salesReturnStatus);
						}
					}
				}elseif ($productTrn['transaction_type']=='Outward') {
					if ($productTrn['invoice_number'] != '') {
						$billStatus = $this->changeSalesBillData($mergeArray,[
							'jf_id'=> $jf_id
						],'productArray');
						if (strcmp($billStatus, $exceptionArray['500'])==0) {
							throw new Exception($billStatus);
						}
					}
				}
			}
			$productIdUpdate['product_id'] = $toProductId;
			$status = $mergeModel->updateProductTrnByProductId($productIdUpdate,$fromProductId);
			if (strcmp($exceptionArray['500'], $status) == 0) {
				throw new Exception($status);
			}
			$status = $mergeModel->updateItemizeTrnByProductId($productIdUpdate,$fromProductId);
			if (strcmp($exceptionArray['500'], $status) == 0) {
				throw new Exception($status);
			}
			$status = $mergeModel->updateItemwiseCommissionByProductId($productIdUpdate,$fromProductId);
			if (strcmp($exceptionArray['500'], $status) == 0) {
				throw new Exception($status);
			}
			DB::commit();
			return $status;
		}catch(\Exception $e){
			DB::rollback();
			return $e->getMessage();
		}
	}

	/**
	 * @param mergeArray, mergeCondition, mergeParam
	 * @return query status
	 * Update Sales Bill Data using JfId
	 */
	public function changeSalesBillData($mergeArray,$mergeCondition, $param = 'productArray')
	{
		if ($param = 'productArray') {
			$fromProductId = $mergeArray['from_product'];
			$toProductId = $mergeArray['to_product'];
			$newProduct = $mergeArray['new_product'];
			$jfId = $mergeCondition['jf_id'];
			$mergeModel = new MergeModel();
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			$status = $mergeModel->getSalesBillDataByJfId($jfId);
			if (strcmp($status, $exceptionArray['404'])==0) {
				return $status;
			}
			$salesBillArray = json_decode($status,true);
			foreach ($salesBillArray as $salesBill) {
				$productArray = json_decode($salesBill['product_array'],true);
				$productArray['inventory'] = array_map(function($pr) use($mergeArray){
					if ($pr['productId'] == $mergeArray['from_product']) {
						$pr['productId'] = $mergeArray['to_product'];
						$pr['productName'] = $mergeArray['new_product'][0]['product_name'];
						$pr['color'] = $mergeArray['new_product'][0]['color'];
						$pr['size'] = $mergeArray['new_product'][0]['size'];
						$pr['variant'] = $mergeArray['new_product'][0]['variant'];
					}
					return $pr;
				}, $productArray['inventory']);

				$updateArray['product_array'] = json_encode($productArray);
				$updateStatus = $mergeModel->updateSalesBillBySaleId($updateArray,$salesBill['sale_id']);
				if (strcmp($updateStatus,$exceptionArray['500'])==0) {
					break;
				}
			}
			return $updateStatus;
		}
	}
	/**
	 * @param mergeArray, mergeCondition, mergeParam
	 * @return query status
	 * 
	 * Update Purchase Bill Data using JfId
	 */
	public function changePurchaseBillData($mergeArray,$mergeCondition, $param = 'productArray')
	{
		if ($param = 'productArray') {
			$fromProductId = $mergeArray['from_product'];
			$toProductId = $mergeArray['to_product'];
			$newProduct = $mergeArray['new_product'];
			$jfId = $mergeCondition['jf_id'];
			$mergeModel = new MergeModel();
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			$status = $mergeModel->getPurchaseBillDataByJfId($jfId);
			if (strcmp($status, $exceptionArray['404'])==0) {
				return $status;
			}
			$purchaseBillArray = json_decode($status,true);
			foreach ($purchaseBillArray as $purchaseBill) {
				$productArray = json_decode($purchaseBill['product_array'],true);
				$productArray['inventory'] = array_map(function($pr) use($mergeArray){
					if ($pr['productId'] == $mergeArray['from_product']) {
						$pr['productId'] = $mergeArray['to_product'];
						$pr['productName'] = $mergeArray['new_product'][0]['product_name'];
						$pr['color'] = $mergeArray['new_product'][0]['color'];
						$pr['size'] = $mergeArray['new_product'][0]['size'];
						$pr['variant'] = $mergeArray['new_product'][0]['variant'];
						return $pr;
					}
				}, $productArray['inventory']);

				$updateArray['product_array'] = json_encode($productArray);
				$updateStatus = $mergeModel->updatePurchaseBillByPurchaseId($updateArray,$purchaseBill['purchase_id']);
				if (strcmp($updateStatus,$exceptionArray['500'])==0) {
					break;
				}
			}
			return $updateStatus;
		}
	}
	/**
	 * @param mergeArray, mergeCondition, mergeParam
	 * @return query status
	 * Update Sales Return Data using JfId
	 */
	public function changeSalesReturnData($mergeArray,$mergeCondition, $param = 'productArray')
	{
		if ($param = 'productArray') {
			$fromProductId = $mergeArray['from_product'];
			$toProductId = $mergeArray['to_product'];
			$newProduct = $mergeArray['new_product'];
			$jfId = $mergeCondition['jf_id'];
			$mergeModel = new MergeModel();
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			$status = $mergeModel->getSalesReturnDataByJfId($jfId);
			if (strcmp($status, $exceptionArray['404'])==0) {
				return $status;
			}
			$salesReturnArray = json_decode($status,true);
			foreach ($salesReturnArray as $salesReturn) {
				$productArray = json_decode($salesReturn['product_array'],true);
				$productArray['inventory'] = array_map(function($pr) use($mergeArray){
					if ($pr['productId'] == $mergeArray['from_product']) {
						$pr['productId'] = $mergeArray['to_product'];
						$pr['productName'] = $mergeArray['new_product'][0]['product_name'];
						$pr['color'] = $mergeArray['new_product'][0]['color'];
						$pr['size'] = $mergeArray['new_product'][0]['size'];
						$pr['variant'] = $mergeArray['new_product'][0]['variant'];
						return $pr;
					}
				}, $productArray['inventory']);

				$updateArray['product_array'] = json_encode($productArray);
				$updateStatus = $mergeModel->updateSalesReturnById($updateArray,$salesReturn['sale_return_id']);
				if (strcmp($updateStatus,$exceptionArray['500'])==0) {
					break;
				}
			}
			return $updateStatus;
		}
	}
}