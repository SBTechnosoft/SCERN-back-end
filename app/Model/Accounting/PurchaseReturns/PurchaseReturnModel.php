<?php
namespace ERP\Model\Accounting\PurchaseReturns;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
// Inventory Deps
use ERP\Api\V1_0\Accounting\PurchaseReturns\Transformers\PurchaseReturnInventoryTransformer;
use ERP\Model\Accounting\PurchaseReturns\PurchaseReturnInventoryModel;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class PurchaseReturnModel extends Model
{
	protected $table = 'purchase_return';

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


	/**
	 * insert data with document
	 * @param  array
	 * @return the status
	*/
	public function insertData($insertData, $requestData)
	{
		$mytime = Carbon\Carbon::now();
		$exceptionArray = $this->messages;
		$requestInput = $requestData->input();
		$insertKeyStr = "";
		$insertValueStr = "";
		$separator = "";
		$valueArray = [];
		$insertables = ['product_array', 'payment_mode', 'bank_name','bank_ledger_id', 'bill_number', 'check_number', 'total', 'total_discounttype', 'total_discount', 'total_cgst_percentage', 'total_sgst_percentage', 'total_igst_percentage', 'extra_charge', 'tax', 'grand_total', 'advance', 'balance', 'remark', 'entry_date', 'vendor_id', 'company_id', 'purchase_id', 'jf_id'];
		foreach ($insertables as $value) {
			if(array_key_exists($value, $insertData)) {
				$insertKeyStr .= $separator."{$value}";
				$insertValueStr .= $separator."?";
				array_push($valueArray, $insertData[$value]);
				$separator = ",";
			}
		}
		$insertKeyStr .= ",created_at, updated_at";
		$insertValueStr .= ",?,?";
		array_push($valueArray, $mytime, $mytime);
		DB::beginTransaction();
		$raw = $this->database->statement("INSERT INTO {$this->table} ($insertKeyStr) VALUES ($insertValueStr);", $valueArray);
		$returnId = $this->database->select("SELECT max(purchase_return_id) as return_id FROM {$this->table};");
		if(array_key_exists('product_array', $insertData)) {
			$returnId = $returnId[0]->return_id;
			$transformer = new PurchaseReturnInventoryTransformer();
			$trimInv = $transformer->trimInventory($insertData['product_array'], $returnId);
			$invModel = new PurchaseReturnInventoryModel();
			$status = $invModel->insertData($trimInv);
		}
		DB::commit();
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
}