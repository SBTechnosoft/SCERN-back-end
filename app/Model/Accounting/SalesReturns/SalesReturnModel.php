<?php
namespace ERP\Model\Accounting\SalesReturns;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use stdClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SalesReturnModel extends Model
{
	protected $table = 'sales_return';

	/**
	 * insert data with document
	 * @param  array
	 * returns the status
	*/
	public function insertData($salesReturnData, $requestData)
	{
		$mytime = Carbon\Carbon::now();
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$requestInput = $requestData->input();
		$insertKeyStr = "";
		$insertValueStr = "";
		$insertables = ['product_array','payment_mode','bank_name','bank_ledger_id','invoice_number','check_number','total','total_discounttype','total_discount','total_cgst_percentage','total_sgst_percentage','total_igst_percentage','extra_charge','tax','grand_total','advance','balance','remark','entry_date','client_id','company_id','branch_id','sale_id','jf_id'];
		$separator = '';
		foreach ($insertables as $value) {
			if (isset($salesReturnData[$value])) {
				$insertKeyStr .= $separator."`$value`";
				$insertValueStr .= $separator."'".$salesReturnData[$value]."'";
				$separator = ",";
			}
		}
		$insertKeyStr .= ",`created_at`,`updated_at`";
		$insertValueStr .= ",'$mytime','$mytime'";
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("INSERT INTO `sales_return` ($insertKeyStr) VALUES ($insertValueStr);");
		DB::commit();
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
}