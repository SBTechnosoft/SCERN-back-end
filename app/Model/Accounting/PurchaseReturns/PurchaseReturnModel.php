<?php
namespace ERP\Model\Accounting\PurchaseReturns;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PurchaseReturnModel extends Model
{
	protected $table = 'purchase_return';
	/**
	 * insert data with document
	 * @param  array
	 * @return the status
	*/
	public function insertData($insertData, $requestData)
	{
		$mytime = Carbon\Carbon::now();
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
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

		$raw = DB::connection($databaseName)->statement("INSERT INTO `purchase_return` ($insertKeyStr) VALUES ($insertValueStr);", $valueArray);
		DB::commit();
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
}