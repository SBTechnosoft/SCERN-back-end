<?php
namespace ERP\Core\Merge\Services;

use Carbon;
use DB;
use ERP\Core\Support\Service\AbstractService;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;
use Exception;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class SpecialUtilityService extends AbstractService
{
	/**
	 * @var mergeService
	 * $var mergeModel
	 */
	function __construct()
	{
		parent::__construct();

		$exceptions = new ExceptionMessage();
		$this->messages = $exceptions->messageArrays();
		$this->constant = new ConstantClass();
		$this->constantVars = $this->constant->constantVariable();
		$database = $this->constant->constantDatabase();
		$this->database = DB::connection($database);
	} 

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
	public function get($id, $name)
	{
		echo "get";
	}
	public function invoke(callable $method)
	{
		echo "invoke";
	}


	public function fixSaleInventory()
	{
		$pendingBills = $this->database->select("SELECT 
			sales_bill.sale_id, sales_bill.product_array 
			FROM sales_bill 
			LEFT JOIN sale_inventory_dtl ON sales_bill.sale_id = sale_inventory_dtl.sale_id
			WHERE inventory_dtl_id IS NULL 
			AND sales_bill.deleted_at = 0
			AND sales_bill.is_draft = 'no'
		");
		
		foreach ($pendingBills as $bill) {
			try {
				$transformer = new \ERP\Api\V1_0\Accounting\Bills\Transformers\SaleInventoryTransformer();
				$trimInv = $transformer->trimInventory($bill->product_array, $bill->sale_id);
				$invModel = new \ERP\Model\Accounting\Bills\SaleInventoryModel();
				$status = $invModel->insertData($trimInv);
				if(strcmp($status, $this->messages['200'])!=0) {
					throw new Exception($status);
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}
		return $this->messages['200'].' '.count($pendingBills);
	}

	public function fixPurchaseInventory()
	{
		$pendingBills = $this->database->select("SELECT 
			purchase_bill.purchase_id, purchase_bill.product_array 
			FROM purchase_bill 
			LEFT JOIN purchase_inventory_dtl ON purchase_bill.purchase_id = purchase_inventory_dtl.purchase_id
			WHERE inventory_dtl_id IS NULL 
			AND purchase_bill.deleted_at = 0
		");
		
		foreach ($pendingBills as $bill) {
			try {
				$transformer = new \ERP\Api\V1_0\Accounting\PurchaseBills\Transformers\PurchaseInventoryTransformer();
				$trimInv = $transformer->trimInventory($bill->product_array, $bill->purchase_id);
				$invModel = new \ERP\Model\Accounting\PurchaseBills\PurchaseInventoryModel();
				$status = $invModel->insertData($trimInv);
				if(strcmp($status, $this->messages['200'])!=0) {
					throw new Exception($status);
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		return $this->messages['200'].' '.count($pendingBills);
	}

	public function fixSaleReturnInventory()
	{
		$pendingBills = $this->database->select("SELECT 
			sales_return.sale_return_id, sales_return.product_array 
			FROM sales_return 
			LEFT JOIN sale_return_inventory_dtl ON sales_return.sale_return_id = sale_return_inventory_dtl.sale_return_id
			WHERE inventory_dtl_id IS NULL 
			AND sales_return.deleted_at = 0
		");
		
		foreach ($pendingBills as $bill) {
			try {
				$transformer = new \ERP\Api\V1_0\Accounting\SalesReturns\Transformers\SaleReturnInventoryTransformer();
				$trimInv = $transformer->trimInventory($bill->product_array, $bill->sale_return_id);
				$invModel = new \ERP\Model\Accounting\SalesReturns\SaleReturnInventoryModel();
				$status = $invModel->insertData($trimInv);
				if(strcmp($status, $this->messages['200'])!=0) {
					throw new Exception($status);
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		return $this->messages['200'].' '.count($pendingBills);
	}

	public function fixPurchaseReturnInventory()
	{
		$pendingBills = $this->database->select("SELECT 
			purchase_return.purchase_return_id, purchase_return.product_array 
			FROM purchase_return 
			LEFT JOIN purchase_return_inventory_dtl ON purchase_return.purchase_return_id = purchase_return_inventory_dtl.purchase_return_id
			WHERE inventory_dtl_id IS NULL 
			AND purchase_return.deleted_at = 0
		");
		
		foreach ($pendingBills as $bill) {
			try {
				$transformer = new \ERP\Api\V1_0\Accounting\PurchaseReturns\Transformers\PurchaseReturnInventoryTransformer();
				$trimInv = $transformer->trimInventory($bill->product_array, $bill->purchase_return_id);
				$invModel = new \ERP\Model\Accounting\PurchaseReturns\PurchaseReturnInventoryModel();
				$status = $invModel->insertData($trimInv);
				if(strcmp($status, $this->messages['200'])!=0) {
					throw new Exception($status);
				}
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		return $this->messages['200'].' '.count($pendingBills);
	}

}