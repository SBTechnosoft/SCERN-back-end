<?php
namespace ERP\Model\Reports\PolishReport;

use Illuminate\Database\Eloquent\Model;
use DB;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use stdClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PolishReportModel extends Model
{
	/**
	 * get data as per given companyId 
	 * returns the array-data/exception message
	*/
	public function getPolishReportData($companyId,$fromDate,$toDate)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		payment_mode,
		bank_name,
		invoice_number,
		job_card_number,
		check_number,
		total,
		total_discounttype,
		total_discount,
		total_cgst_percentage,
		total_sgst_percentage,
		total_igst_percentage,
		extra_charge,
		tax,
		grand_total,
		advance,
		balance,
		po_number,
		user_id,
		remark,
		refund,
		entry_date,
		service_date,
		client_id,
		sales_type,
		company_id,
		branch_id,
		jf_id,
		created_at,
		updated_at 
		from sales_bill 
		where (entry_date BETWEEN '".$fromDate."' AND '".$toDate."') and 
		company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00' and is_draft='no' and is_salesorder='not'");
		DB::commit();
		
		if(count($raw)==0)
		{
			return $exceptionArray['404']; 
		}
		else
		{
			$documentResult = array();
			for($saleData=0;$saleData<count($raw);$saleData++)
			{
				$billExpenseResult = array();
				//get expense sale data from database
				DB::beginTransaction();
				$billExpenseResult = DB::connection($databaseName)->select("select 
				sale_expense_id as saleExpenseId,
				expense_type as expenseType,
				expense_id as expenseId,
				expense_name as expenseName,
				expense_value as expenseValue,
				expense_operation as expenseOperation,
				sale_id as saleId
				from sale_expense_dtl
				where deleted_at='0000-00-00 00:00:00' and
				sale_id=".$raw[$saleData]->sale_id);
				DB::commit();
				$raw[$saleData]->expense = $billExpenseResult;

				DB::beginTransaction();
				$documentResult[$saleData] = DB::connection($databaseName)->select("select
				document_id,
				sale_id,
				document_name,
				document_size,
				document_format,
				document_type,
				created_at,
				updated_at
				from sales_bill_doc_dtl
				where sale_id='".$raw[$saleData]->sale_id."' and 
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				if(count($documentResult[$saleData])==0)
				{
					$documentResult[$saleData] = array();
					$documentResult[$saleData][0] = new stdClass();
					$documentResult[$saleData][0]->document_id = 0;
					$documentResult[$saleData][0]->sale_id = 0;
					$documentResult[$saleData][0]->document_name = '';
					$documentResult[$saleData][0]->document_size = 0;
					$documentResult[$saleData][0]->document_format = '';
					$documentResult[$saleData][0]->document_type ='bill';
					$documentResult[$saleData][0]->created_at = '0000-00-00 00:00:00';
					$documentResult[$saleData][0]->updated_at = '0000-00-00 00:00:00';
				}
			}
			$salesArrayData = array();
			$salesArrayData['salesData'] = json_encode($raw);
			$salesArrayData['documentData'] = json_encode($documentResult);
			return json_encode($salesArrayData);
		}
	}
}
