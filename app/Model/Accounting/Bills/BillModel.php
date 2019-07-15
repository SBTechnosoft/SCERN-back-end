<?php
namespace ERP\Model\Accounting\Bills;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Accounting\Journals\JournalModel;
use ERP\Model\Settings\InvoiceNumbers\InvoiceModel;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Model\Clients\ClientModel;
use ERP\Core\Clients\Entities\ClientArray;
use stdClass;
use ERP\Model\Settings\SettingModel;
/** 
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BillModel extends Model
{
	protected $table = 'sales_bill';
	
	/**
	 * insert data with document
	 * @param  array
	 * returns the status
	*/
	public function insertAllData($productArray,$paymentMode,$bankLedgerId,$invoiceNumber,$jobCardNumber,$bankName,$checkNumber,$total,$extraCharge,$tax,$grandTotal,$advance,$balance,$remark,$entryDate,$companyId,$branchId,$ClientId,$salesType,$documentArray,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$poNumber,$requestData,$expense,$serviceDate,$userId,$createdBy = 0)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$requestInput = $requestData->input();
		$isSalesOrder = array_key_exists("issalesorder",$requestData->header())?"is_salesorder='ok'":"is_salesorder='not'";
		$isSalesOrderInsert = array_key_exists("issalesorder",$requestData->header())?"ok":"not";
		if($jobCardNumber!="")
		{
			//get job-card-number for checking job-card-number is exist or not
			DB::beginTransaction();
			$getJobCardNumber = DB::connection($databaseName)->select("select
			job_card_number 
			from sales_bill 
			where job_card_number='".$jobCardNumber."' and 
			deleted_at='0000-00-00 00:00:00' and is_draft='no' and ".$isSalesOrder);
			DB::commit();
		}
		else
		{
			$getJobCardNumber = array();
		}
		if(array_key_exists("isDraft",$requestInput))
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update sales_bill set
			product_array='".$productArray."',
			payment_mode='".$paymentMode."',
			bank_ledger_id='".$bankLedgerId."',
			invoice_number='".$invoiceNumber."',
			job_card_number='".$jobCardNumber."',
			bank_name='".$bankName."',
			check_number='".$checkNumber."',
			total='".$total."',
			total_discounttype='".$totalDiscounttype."',
			total_discount='".$totalDiscount."',
			total_cgst_percentage='".$totalCgstPercentage."',
			total_sgst_percentage='".$totalSgstPercentage."',
			total_igst_percentage='".$totalIgstPercentage."',
			extra_charge='".$extraCharge."',
			tax='".$tax."',
			grand_total='".$grandTotal."',
			advance='".$advance."',
			balance='".$balance."',
			po_number='".$poNumber."',
			user_id='".$userId."',
			remark='".$remark."',
			entry_date='".$entryDate."',
			service_date='".$serviceDate."',
			company_id='".$companyId."',
			branch_id='".$branchId."',
			sales_type='".$salesType."',
			client_id='".$ClientId."',
			jf_id='".$jfId."',
			created_by='".$createdBy."',
			updated_by='".$createdBy."',
			updated_at='".$mytime."',
			is_draft='no',
			".$isSalesOrder."
			where sale_id='".$requestInput['isDraft']."'");
			DB::commit();
			
			//update invoice-number
			$invoiceResult = $this->updateInvoiceNumber($companyId);
			if(strcmp($invoiceResult,$exceptionArray['200'])!=0)
			{
				return $invoiceResult;
			}
		}
		else
		{
			//if job-card-number is exists then update bill data otherwise insert bill data
			if(count($getJobCardNumber)==0)
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into sales_bill(
				product_array,
				payment_mode,
				bank_ledger_id,
				invoice_number,
				job_card_number,
				bank_name,
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
				is_salesorder,
				remark,
				entry_date,
				service_date,
				company_id,
				branch_id,
				sales_type,
				client_id,
				jf_id,
				created_by,
				created_at) 
				values('".$productArray."','".$paymentMode."','".$bankLedgerId."','".$invoiceNumber."','".$jobCardNumber."','".$bankName."','".$checkNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$advance."','".$balance."','".$poNumber."','".$userId."','".$isSalesOrderInsert."','".$remark."','".$entryDate."','".$serviceDate."','".$companyId."','".$branchId."','".$salesType."','".$ClientId."','".$createdBy."','".$mytime."')");
				DB::commit();
				
				//update invoice-number
				$invoiceResult = $this->updateInvoiceNumber($companyId);
				if(strcmp($invoiceResult,$exceptionArray['200'])!=0)
				{
					return $invoiceResult;
				}
			}
			else
			{
				//update bill data
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("update 
				sales_bill set 
				product_array='".$productArray."',
				payment_mode='".$paymentMode."',
				bank_ledger_id='".$bankLedgerId."',
				job_card_number='".$jobCardNumber."',
				bank_name='".$bankName."',
				check_number='".$checkNumber."',
				total='".$total."',
				total_discounttype='".$totalDiscounttype."',
				total_discount='".$totalDiscount."',
				total_cgst_percentage='".$totalCgstPercentage."',
				total_sgst_percentage='".$totalSgstPercentage."',
				total_igst_percentage='".$totalIgstPercentage."',
				extra_total='".$extraCharge."',
				tax='".$tax."',
				grand_total='".$grandTotal."',
				advance='".$advance."',
				balance='".$balance."',
				remark='".$remark."',
				entry_date='".$entryDate."',
				service_date='".$serviceDate."',
				company_id='".$companyId."',
				branch_id='".$branchId."',
				client_id='".$ClientId."',
				".$isSalesOrderInsert.",
				sales_type='".$salesType."',
				po_number='".$poNumber."',
				user_id='".$userId."',
				jf_id='".$jfId."',
				created_by='".$createdBy."',
				updated_by='".$createdBy."',
				updated_at='".$mytime."' 
				where job_card_number='".$jobCardNumber."' and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
		}
		$decodedJsonExpense = json_decode($expense);
		if($raw==1)
		{
			//get latest sale-id from database
			DB::beginTransaction();
			$saleId = DB::connection($databaseName)->select("SELECT 
			max(sale_id) sale_id
			FROM sales_bill where deleted_at='0000-00-00 00:00:00' and ".$isSalesOrder);
			DB::commit();

			if($decodedJsonExpense!="" && is_array($decodedJsonExpense))
			{
				if(count($decodedJsonExpense)!=0)
				{
					$expenseCountData = count($decodedJsonExpense);
					for($expenseArray=0;$expenseArray<$expenseCountData;$expenseArray++)
					{
						//insertion in sale_expense_dtl
						DB::beginTransaction();
						$raw = DB::connection($databaseName)->statement("insert into sale_expense_dtl(
						expense_name,
						expense_type,
						expense_value,
						expense_tax,
						expense_operation,
						sale_id,
						expense_id,
						created_at)
						values(
						'".$decodedJsonExpense[$expenseArray]->expenseName."',
						'".$decodedJsonExpense[$expenseArray]->expenseType."',
						'".$decodedJsonExpense[$expenseArray]->expenseValue."',
						'".$decodedJsonExpense[$expenseArray]->expenseTax."',
						'".$decodedJsonExpense[$expenseArray]->expense_operation."',
						'".$saleId[0]->sale_id."',
						'".$decodedJsonExpense[$expenseArray]->expenseId."',
						'".$mytime."')");
						DB::commit();
					}
				}
			}
			DB::beginTransaction();
			$salesTrnData = DB::connection($databaseName)->statement("insert into sales_bill_trn(
			product_array,
			payment_mode,
			bank_ledger_id,
			invoice_number,
			job_card_number,
			bank_name,
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
			is_salesorder,
			remark,
			entry_date,
			service_date,
			company_id,
			branch_id,
			sales_type,
			client_id,
			sale_id,
			jf_id,
			created_at) 
			values('".$productArray."','".$paymentMode."','".$bankLedgerId."','".$invoiceNumber."','".$jobCardNumber."','".$bankName."','".$checkNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$advance."','".$balance."','".$poNumber."','".$userId."','".$isSalesOrderInsert."','".$remark."','".$entryDate."','".$serviceDate."','".$companyId."','".$branchId."','".$salesType."','".$ClientId."','".$saleId[0]->sale_id."','".$jfId."','".$mytime."')");
			DB::commit();
			if(is_array($saleId))
			{
				for($docArray=0;$docArray<count($documentArray);$docArray++)
				{
					// add documents in sale-document table
					DB::beginTransaction();
					$saleDocumentResult = DB::connection($databaseName)->statement("insert into sales_bill_doc_dtl(
					sale_id,
					document_name,
					document_size,
					document_format,
					created_at) 
					values('".$saleId[0]->sale_id."','".$documentArray[$docArray][0]."','".$documentArray[$docArray][1]."','".$documentArray[$docArray][2]."','".$mytime."')");
					DB::commit();
					
					// add documents in client database
					DB::beginTransaction();
					$clientDocumentResult = DB::connection($databaseName)->statement("insert into client_doc_dtl(
					sale_id,
					document_name,
					document_size,
					document_format,
					client_id,
					created_at) 
					values('".$saleId[0]->sale_id."','".$documentArray[$docArray][0]."','".$documentArray[$docArray][1]."','".$documentArray[$docArray][2]."','".$ClientId."','".$mytime."')");
					DB::commit();
					if($saleDocumentResult==0 || $clientDocumentResult==0)
					{
						return $exceptionArray['500'];
					}
				}	
				if($saleDocumentResult==1)
				{
					//get latest sale data from database
					DB::beginTransaction();
					$billResult = DB::connection($databaseName)->select("select
					sale_id,
					product_array,
					payment_mode,
					bank_ledger_id,
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
					entry_date,
					service_date,
					sales_type,
					client_id,
					company_id,
					branch_id,
					jf_id,
					created_at,
					updated_at
					from sales_bill where sale_id=(select MAX(sale_id) as sale_id from sales_bill) and deleted_at='0000-00-00 00:00:00' and is_draft='no' and ".$isSalesOrder); 
					DB::commit();
					//get latest expense sale data from database
					DB::beginTransaction();
					$billExpenseResult = DB::connection($databaseName)->select("select 
					sale_expense_id as saleExpenseId,
					expense_type as expenseType,
					expense_id as expenseId,
					expense_name as expenseName,
					expense_value as expenseValue,
					expense_tax as expenseTax,
					expense_operation as expenseOperation,
					sale_id as saleId
					from sale_expense_dtl
					where deleted_at='0000-00-00 00:00:00' and
					sale_id=(select MAX(sale_id) as sale_id from sales_bill)");
					DB::commit();
					$billResult[0]->expense = $billExpenseResult;
					if(count($billResult)==1)
					{
						return json_encode($billResult);
					}
					else
					{
						return $exceptionArray['500'];
					}
				}
			}
			else
			{
				return $exceptionArray['500'];
			}
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * insert only data 
	 * @param  array
	 * returns the status
	*/
	public function insertData($productArray,$paymentMode,$bankLedgerId,$invoiceNumber,$jobCardNumber,$bankName,$checkNumber,$total,$extraCharge,$tax,$grandTotal,$advance,$balance,$remark,$entryDate,$companyId,$branchId,$ClientId,$salesType,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$poNumber,$requestData,$expense,$serviceDate,$userId,$createdBy = 0)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$requestInput = $requestData->input();
		$printCount = '0';
		if(array_key_exists('operation',$requestData->header()))
		{
			if ($requestData->header()['operation'][0]== 'generate') {
				$printCount = '1';
			}
		}
		$salesOrder = array_key_exists("issalesorder",$requestData->header()) ? "is_salesorder='ok'" : "is_salesorder='not'";
		$salesOrderInsert = array_key_exists("issalesorder",$requestData->header()) ? "ok" : "not";
		if($jobCardNumber!="")
		{
			//get job-card-number for checking job-card-number is exist or not
			DB::beginTransaction();
			$getJobCardNumber = DB::connection($databaseName)->select("select
			job_card_number 
			from sales_bill 
			where job_card_number='".$jobCardNumber."' and 
			deleted_at='0000-00-00 00:00:00' and is_draft='no' and ".$salesOrder);
			DB::commit();
		}
		else
		{
			$getJobCardNumber = array();
		}		
		if(array_key_exists("isDraft",$requestInput))
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update sales_bill set
			product_array='".$productArray."',
			payment_mode='".$paymentMode."',
			bank_ledger_id='".$bankLedgerId."',
			invoice_number='".$invoiceNumber."',
			job_card_number='".$jobCardNumber."',
			bank_name='".$bankName."',
			check_number='".$checkNumber."',
			total='".$total."',
			total_discounttype='".$totalDiscounttype."',
			total_discount='".$totalDiscount."',
			total_cgst_percentage='".$totalCgstPercentage."',
			total_sgst_percentage='".$totalSgstPercentage."',
			total_igst_percentage='".$totalIgstPercentage."',
			extra_charge='".$extraCharge."',
			tax='".$tax."',
			grand_total='".$grandTotal."',
			advance='".$advance."',
			balance='".$balance."',
			po_number='".$poNumber."',
			user_id='".$userId."',
			remark='".$remark."',
			entry_date='".$entryDate."',
			service_date='".$serviceDate."',
			company_id='".$companyId."',
			branch_id='".$branchId."',
			sales_type='".$salesType."',
			client_id='".$ClientId."',
			jf_id='".$jfId."',
			print_count='$printCount',
			created_by='".$createdBy."',
			updated_by='".$createdBy."',
			updated_at='".$mytime."',
			is_draft='no',
			".$salesOrder."
			where sale_id='".$requestInput['isDraft']."'");
			DB::commit();			
			//update invoice-number
			$invoiceResult = $this->updateInvoiceNumber($companyId);
			if(strcmp($invoiceResult,$exceptionArray['200'])!=0)
			{				
				return $invoiceResult;
			}
		}
		else
		{			
			//if job-card-number is exists then update bill data otherwise insert bill data
			if(count($getJobCardNumber)==0)
			{
				//insert bill data
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into sales_bill(
				product_array,
				payment_mode,
				bank_ledger_id,
				invoice_number,
				job_card_number,
				bank_name,
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
				is_salesorder,
				remark,
				entry_date,
				service_date,
				company_id,
				branch_id,
				client_id,
				sales_type,
				jf_id,
				print_count,
				created_by,
				created_at) 
				values('".$productArray."','".$paymentMode."','".$bankLedgerId."','".$invoiceNumber."','".$jobCardNumber."','".$bankName."','".$checkNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$advance."','".$balance."','".$poNumber."','".$userId."','".$salesOrderInsert."','".$remark."','".$entryDate."','".$serviceDate."','".$companyId."','".$branchId."','".$ClientId."','".$salesType."','".$jfId."','$printCount','".$createdBy."','".$mytime."')");
				DB::commit();
				
				//update invoice-number				
				$invoiceResult = $this->updateInvoiceNumber($companyId);
				if(strcmp($invoiceResult,$exceptionArray['200'])!=0)
				{					
					return $invoiceResult;
				}
			}
			else
			{
				//update bill data
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("update 
				sales_bill set 
				product_array='".$productArray."',
				payment_mode='".$paymentMode."',
				bank_ledger_id='".$bankLedgerId."',
				job_card_number='".$jobCardNumber."',
				bank_name='".$bankName."',
				check_number='".$checkNumber."',
				total='".$total."',
				total_discounttype='".$totalDiscounttype."',
				total_discount='".$totalDiscount."',
				total_cgst_percentage='".$totalCgstPercentage."',
				total_sgst_percentage='".$totalSgstPercentage."',
				total_igst_percentage='".$totalIgstPercentage."',
				extra_charge='".$extraCharge."',
				tax='".$tax."',
				grand_total='".$grandTotal."',
				advance='".$advance."',
				balance='".$balance."',
				po_number='".$poNumber."',
				user_id='".$userId."',
				remark='".$remark."',
				entry_date='".$entryDate."',
				service_date='".$serviceDate."',
				company_id='".$companyId."',
				branch_id='".$branchId."',
				client_id='".$ClientId."',
				sales_type='".$salesType."',
				".$salesOrder.",
				jf_id='".$jfId."',
				print_count='$printCount',
				created_by='".$createdBy."',
				updated_by='".$createdBy."',
				updated_at='".$mytime."' 
				where job_card_number='".$jobCardNumber."' and 
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
		}
		$decodedJsonExpense = json_decode($expense);
		if($raw==1)
		{
			DB::beginTransaction();
			$saleId = DB::connection($databaseName)->select("SELECT 
			max(sale_id) sale_id
			FROM sales_bill where deleted_at='0000-00-00 00:00:00' and is_draft='no' and ".$salesOrder);
			DB::commit();
			if($decodedJsonExpense!="" && is_array($decodedJsonExpense))
			{
				if(count($decodedJsonExpense)!=0)
				{
					$expenseCountData = count($decodedJsonExpense);
					for($expenseArray=0;$expenseArray<$expenseCountData;$expenseArray++)
					{
						DB::beginTransaction();
						$raw = DB::connection($databaseName)->statement("insert into sale_expense_dtl(
						expense_name,
						expense_type,
						expense_value,
						expense_tax,
						expense_operation,
						sale_id,
						expense_id,
						created_at)
						values(
						'".$decodedJsonExpense[$expenseArray]->expenseName."',
						'".$decodedJsonExpense[$expenseArray]->expenseType."',
						'".$decodedJsonExpense[$expenseArray]->expenseValue."',
						'".$decodedJsonExpense[$expenseArray]->expenseTax."',
						'".$decodedJsonExpense[$expenseArray]->expenseOperation."',
						'".$saleId[0]->sale_id."',
						'".$decodedJsonExpense[$expenseArray]->expenseId."',
						'".$mytime."')");
						DB::commit();
					}
				}
			}
			//insertion in sale bill transaction
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into sales_bill_trn(
			product_array,
			payment_mode,
			bank_ledger_id,
			invoice_number,
			job_card_number,
			bank_name,
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
			is_salesorder,
			remark,
			entry_date,
			service_date,
			company_id,
			branch_id,
			client_id,
			sales_type,
			sale_id,
			jf_id,
			created_at) 
			values('".$productArray."','".$paymentMode."','".$bankLedgerId."','".$invoiceNumber."','".$jobCardNumber."','".$bankName."','".$checkNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$advance."','".$balance."','".$poNumber."','".$userId."','".$salesOrderInsert."','".$remark."','".$entryDate."','".$serviceDate."','".$companyId."','".$branchId."','".$ClientId."','".$salesType."','".$saleId[0]->sale_id."','".$jfId."','".$mytime."')");
			DB::commit();
			//get latest inserted sale bill data
			DB::beginTransaction();
			$billResult = DB::connection($databaseName)->select("select
			sale_id,
			product_array,
			payment_mode,
			bank_ledger_id,
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
			entry_date,
			service_date,
			client_id,
			sales_type,
			company_id,
			branch_id,
			jf_id,
			print_count,
			created_at,
			updated_at 
			from sales_bill where sale_id=(select MAX(sale_id) as sale_id from sales_bill) and deleted_at='0000-00-00 00:00:00' and is_draft='no' and ".$salesOrder); 
			DB::commit();
			//get latest expense sale data from database
			DB::beginTransaction();
			$billExpenseResult = DB::connection($databaseName)->select("select 
			sale_expense_id as saleExpenseId,
			expense_type as expenseType,
			expense_id as expenseId,
			expense_name as expenseName,
			expense_value as expenseValue,
			expense_tax as expenseTax,
			expense_operation as expenseOperation,
			sale_id as saleId
			from sale_expense_dtl
			where deleted_at='0000-00-00 00:00:00' and
			sale_id=(select MAX(sale_id) as sale_id from sales_bill)");
			DB::commit();
			$billResult[0]->expense = $billExpenseResult;
			if(count($billResult)==1)
			{
				return json_encode($billResult);
			}
			else
			{
				return $exceptionArray['500'];
			}
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * insertion of draft-bill data
	 * @param  request-input array
	 * returns the exception-message/status
	*/
	public function insertBillDraftData(Request $request)
	{
		$mytime = Carbon\Carbon::now();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$inputData = $request->input();
		$clientArray = new ClientArray();
		$clientBillArrayData = $clientArray->getBillClientArrayData();
		//splice data from trim array
		for($index=0;$index<count($clientBillArrayData);$index++)
		{
			if(array_key_exists($clientBillArrayData[array_keys($clientBillArrayData)[$index]],$inputData))
			{
				unset($inputData[$clientBillArrayData[array_keys($clientBillArrayData)[$index]]]);
			}
		}
		$inventoryArray = array();
		$inventoryArray['inventory'] = $inputData['inventory'];
		$inventoryDecodedData = json_encode($inventoryArray);
		unset($inputData['inventory']);	
		$inputDataCount = count($inputData);
		$newInputArray = array();
		$keyString='';
		$valueString='';
		$updateString='';
		for($billData=0;$billData<$inputDataCount;$billData++)
		{
			if(strcmp(array_keys($inputData)[$billData],"entryDate")==0 || strcmp(array_keys($inputData)[$billData],"serviceDate")==0)
			{
				//entry-date conversion
				$splitedEntryDate = explode("-",$inputData[array_keys($inputData)[$billData]]);
				$inputData[array_keys($inputData)[$billData]] = $splitedEntryDate[2]."-".$splitedEntryDate[1]."-".$splitedEntryDate[0];
			}

			$conversion= preg_replace('/(?<!\ )[A-Z]/', '_$0', array_keys($inputData)[$billData]);
			$lowerCase = strtolower($conversion);
			// $newInputArray[$lowerCase] = $inputData[array_keys($inputData)[$billData]];
			$keyString = $keyString.$lowerCase.",";
			$valueString = $valueString."'".$inputData[array_keys($inputData)[$billData]]."',";
			
			$updateString = $updateString.$lowerCase."='".$inputData[array_keys($inputData)[$billData]]."',";
		}	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		if(array_key_exists("saleid",$request->header()))
		{
			$saleId = $request->header()['saleid'][0];
			//update sale-bill draft data
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update sales_bill
			set ".$updateString."updated_at='".$mytime."',is_draft='yes',product_array='".$inventoryDecodedData."' 
			where sale_id=".$saleId);
			DB::commit();
		}
		else
		{
			//insert sale-bill draft data
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into sales_bill
			(".$keyString."product_array,is_draft,created_at)
			values(".$valueString."'".$inventoryDecodedData."','yes','".$mytime."')");
			DB::commit();
		}
		if($raw==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}			
	}
	
	/**
	 * after insertion bill data update invoice-number
	 * @param  compant-id
	 * returns the exception-message
	*/
	public function updateInvoiceNumber($companyId)
	{
		//get constants from constant class
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$invoiceModel = new InvoiceModel();
		$invoiceData = $invoiceModel->getLatestInvoiceData($companyId);
		if(strcmp($exceptionArray['204'],$invoiceData)==0)
		{
			return $invoiceData;
		}
		$invoiceId = json_decode($invoiceData)[0]->invoice_id;
		$updateResult = $invoiceModel->incrementInvoiceNumber($invoiceId);
		return $updateResult;
	}
	
	/**
	 * insert document data
	 * @param  sale-id,document-name,document-format,document-type
	 * returns the exception-message
	*/
	public function billDocumentData($saleId,$documentName,$documentFormat,$documentType)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//insert document data into sale-bill-document table
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into sales_bill_doc_dtl(
		sale_id,
		document_name,
		document_format,
		document_type,
		created_at)
		values('".$saleId."','".$documentName."','".$documentFormat."','".$documentType."','".$mytime."')");
		DB::commit();
		
		//get client-id from sale-bill
		DB::beginTransaction();
		$saleBillData = DB::connection($databaseName)->select("SELECT 
		sale_id,
		client_id
		FROM sales_bill where sale_id='".$saleId."' and deleted_at='0000-00-00 00:00:00' and is_draft='no' and is_salesorder='not'");
		DB::commit();
		
		//insert document data into client-document table
		DB::beginTransaction();
		$clientDocumentInsertion = DB::connection($databaseName)->statement("insert into client_doc_dtl(
		sale_id,
		document_name,
		document_format,
		document_type,
		client_id,
		created_at)
		values('".$saleId."','".$documentName."','".$documentFormat."','".$documentType."','".$saleBillData[0]->client_id."','".$mytime."')");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get bill-document data
	 * @param  company-id,sales-type,from-date,to-date
	 * returns the exception-message
	*/
	public function getSpecifiedData($companyId,$data)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$isSalesOrder = array_key_exists("isSalesOrder",$data) ? "s.is_salesorder='ok'" : "s.is_salesorder='not'";
		if(is_object($data))
		{
			$salesType = $data->getSalesType();
			$fromDate = $data->getFromDate();
			$toDate = $data->getToDate();
			$branch_check = "";
			if ($data->getBranchId()) {
				$branchId = $data->getBranchId();
				$branch_check = " and s.branch_id = ".$branchId;
			}

			if ($data->getIsSalesOrder()) {
				$isSalesOrder = "s.is_salesorder='ok'";
			}
			DB::beginTransaction();
			DB::statement('SET group_concat_max_len = 1000000');
			$raw = DB::connection($databaseName)->select("select 
			s.sale_id,
			s.product_array,
			s.payment_mode,
			s.bank_ledger_id,
			s.bank_name,
			s.invoice_number,
			s.job_card_number,
			s.check_number,
			s.total,
			s.total_discounttype,
			s.total_discount,
			s.total_cgst_percentage,
			s.total_sgst_percentage,
			s.total_igst_percentage,
			s.extra_charge,
			s.tax,
			s.grand_total,
			s.advance,
			s.balance,
			s.po_number,
			s.user_id,
			s.remark,
			s.entry_date,
			s.service_date,
			s.dispatch_status,
			s.client_id,
			s.sales_type,
			s.refund,
			s.jf_id,
			s.print_count,
			s.company_id,
			s.branch_id,
			s.created_at,
			s.updated_at,
			e.expense,
			d.file
			from sales_bill as s 
			LEFT JOIN (
				SELECT 
					sale_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"saleExpenseId\":', sale_expense_id,
								 	', \"expenseType\":\"', IFNULL(expense_type,''),
								 	'\", \"expenseId\":', IFNULL(expense_id,0),
								 	', \"expenseName\":\"', IFNULL(expense_name,''),
								 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
								 	'\", \"expenseTax\":\"', IFNULL(expense_tax,''),
								 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
								 	'\", \"saleId\":', IFNULL(sale_id,0),
							 	' }'
							 ) SEPARATOR ', '),
						']'
					) expense
				FROM sale_expense_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY sale_id 
			) e ON e.sale_id = s.sale_id

			JOIN (
				SELECT 
					sale_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"documentId\":', document_id,
								 	', \"saleId\":', IFNULL(sale_id,0),
								 	', \"documentName\":\"', IFNULL(document_name,''),
								 	'\", \"documentSize\":\"', IFNULL(document_size,''),
								 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
								 	'\", \"documentType\":\"', IFNULL(document_type,''),
								 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
								 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
							 	'\" }'
							 ) SEPARATOR ', '),
						']'
					) file
				FROM sales_bill_doc_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY sale_id 
			) d ON d.sale_id = s.sale_id

			where s.sales_type='".$salesType."' and
			(s.entry_date BETWEEN '".$fromDate."' AND '".$toDate."') and 
			s.company_id='".$companyId."' and 
			s.deleted_at='0000-00-00 00:00:00' and s.is_draft='no' and ".$isSalesOrder.$branch_check);;
			DB::commit();
			if(count($raw)==0)
			{
				return $exceptionArray['404']; 
			}
			else
			{
				return json_encode($raw);
			}
		}
		else if(is_array($data))
		{
			DB::beginTransaction();
			DB::statement('SET group_concat_max_len = 1000000');
			$raw = DB::connection($databaseName)->select("select 
			s.sale_id,
			s.product_array,
			s.payment_mode,
			s.bank_ledger_id,
			s.bank_name,
			s.invoice_number,
			s.job_card_number,
			s.check_number,
			s.total,
			s.total_discounttype,
			s.total_discount,
			s.total_cgst_percentage,
			s.total_sgst_percentage,
			s.total_igst_percentage,
			s.extra_charge,
			s.tax,
			s.grand_total,
			s.advance,
			s.balance,
			s.po_number,
			s.user_id,
			s.remark,
			s.entry_date,
			s.service_date,
			s.client_id,
			s.dispatch_status,
			s.sales_type,
			s.refund,
			s.jf_id,
			s.print_count,
			s.company_id,
			s.branch_id,
			s.created_at,
			s.updated_at,
			e.expense,
			d.file
			from sales_bill as s 
			LEFT JOIN (
				SELECT 
					sale_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"saleExpenseId\":', sale_expense_id,
								 	', \"expenseType\":\"', IFNULL(expense_type,''),
								 	'\", \"expenseId\":', IFNULL(expense_id,0),
								 	', \"expenseName\":\"', IFNULL(expense_name,''),
								 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
								 	'\", \"expenseTax\":\"', IFNULL(expense_tax,''),
								 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
								 	'\", \"saleId\":', IFNULL(sale_id,0),
							 	' }'
							 ) SEPARATOR ', '),
						']'
					) expense
				FROM sale_expense_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY sale_id 
			) e ON e.sale_id = s.sale_id

			LEFT JOIN (
				SELECT 
					sale_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"documentId\":', document_id,
								 	', \"saleId\":', IFNULL(sale_id,0),
								 	', \"documentName\":\"', IFNULL(document_name,''),
								 	'\", \"documentSize\":\"', IFNULL(document_size,''),
								 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
								 	'\", \"documentType\":\"', IFNULL(document_type,''),
								 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
								 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
							 	'\" }'
							 ) SEPARATOR ', '),
						']'
					) file
				FROM sales_bill_doc_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY sale_id 
			) d ON d.sale_id = s.sale_id

			where s.sales_type='".$data['salestype'][0]."' 
			and s.is_draft='no' and 
			".$isSalesOrder." and 
			s.company_id='".$companyId."' and 
			s.deleted_at='0000-00-00 00:00:00' and 
			(s.invoice_number='".$data['invoicenumber'][0]."' or s.client_id in ( select client_id from client_mst where contact_no = '".$data['invoicenumber'][0]."') or s.client_id in ( select client_id from client_mst where email_id = '".$data['invoicenumber'][0]."') or s.client_id in ( select client_id from client_mst where client_name like '%".$data['invoicenumber'][0]."%')) ");
			DB::commit();
			if(count($raw)==0)
			{
				return $exceptionArray['404']; 
			}
			else
			{
				return json_encode($raw);
			}
		}
	}
	
	/**
	 * get bill-draft data
	 * @param 
	 * returns the exception-message/sales data
	*/
	public function getBillDraftData($companyId)
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
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where deleted_at='0000-00-00 00:00:00' and is_draft='yes' and company_id='".$companyId."'");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['204']; 
		}
		else
		{
			$expenseCount = count($raw);
			for($expenseData=0;$expenseData<$expenseCount;$expenseData++)
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
				expense_tax as expenseTax,
				expense_operation as expenseOperation,
				sale_id as saleId
				from sale_expense_dtl
				where deleted_at='0000-00-00 00:00:00' and
				sale_id=".$raw[$expenseData]->sale_id);
				DB::commit();
				$raw[$expenseData]->expense = $billExpenseResult;
			}
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}
	
	/**
	 * get bill data
	 * @param  sale_id
	 * returns the exception-message/sales data
	*/
	public function getSaleIdData($saleId)
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
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where sale_id='".$saleId."' and 
		deleted_at='0000-00-00 00:00:00' and is_draft='no'");
		DB::commit();
		
		if(count($raw)==0)
		{
			return $exceptionArray['404']; 
		}
		else
		{
			//get latest expense sale data from database
			DB::beginTransaction();
			$billExpenseResult = DB::connection($databaseName)->select("select 
			sale_expense_id as saleExpenseId,
			expense_type as expenseType,
			expense_id as expenseId,
			expense_name as expenseName,
			expense_value as expenseValue,
			expense_tax as expenseTax,
			expense_operation as expenseOperation,
			sale_id as saleId
			from sale_expense_dtl
			where deleted_at='0000-00-00 00:00:00' and
			sale_id=".$saleId);
			DB::commit();
			$raw[0]->expense = $billExpenseResult;
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}
	
	/**
	 * get bill data
	 * @param  sale_id
	 * returns the exception-message/sales data
	*/
	public function getSaleOrderData($saleId)
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
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where sale_id='".$saleId."' and 
		deleted_at='0000-00-00 00:00:00' and is_salesorder='ok'");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['404']; 
		}
		else
		{
			//get latest expense sale data from database
			DB::beginTransaction();
			$billExpenseResult = DB::connection($databaseName)->select("select 
			sale_expense_id as saleExpenseId,
			expense_type as expenseType,
			expense_id as expenseId,
			expense_name as expenseName,
			expense_value as expenseValue,
			expense_tax as expenseTax,
			expense_operation as expenseOperation,
			sale_id as saleId
			from sale_expense_dtl
			where deleted_at='0000-00-00 00:00:00' and
			sale_id=".$saleId);
			DB::commit();
			$raw[0]->expense = $billExpenseResult;
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}
	
	/**
	 * get bill data
	 * @param  sale_id
	 * returns the exception-message/sales data
	*/
	public function getSaleOrderIdData($saleId)
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
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where sale_id='".$saleId."' and 
		deleted_at='0000-00-00 00:00:00' and is_draft='no'");
		DB::commit();

		//get latest expense sale data from database
		DB::beginTransaction();
		$billExpenseResult = DB::connection($databaseName)->select("select 
		sale_expense_id as saleExpenseId,
		expense_type as expenseType,
		expense_id as expenseId,
		expense_name as expenseName,
		expense_value as expenseValue,
		expense_tax as expenseTax,
		expense_operation as expenseOperation,
		sale_id as saleId
		from sale_expense_dtl
		where deleted_at='0000-00-00 00:00:00' and
		sale_id='".$saleId."'");
		DB::commit();
		
		$raw[0]->expense = $billExpenseResult;

		if(count($raw)==0)
		{
			return $exceptionArray['404']; 
		}
		else
		{
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}
	
	/**
	 * get previous-next bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getPreviousNextData($headerData) #done
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$isSalesOrder =  array_key_exists("issalesorder",$headerData) ? "and s.is_salesorder='ok'" : "and s.is_salesorder='not'";
		$salesType =  array_key_exists("issalesorder",$headerData) ? "":"s.sales_type='".$headerData['salestype'][0]."' and";
		$nextPreviousWhere = '';
		$orderBy = '';
		$limit = 'limit 1';
		if (array_key_exists('previoussaleid',$headerData)) {
			$nextPreviousWhere = $headerData['previoussaleid'][0]==0 ? '' : ' and s.sale_id < '.$headerData['previoussaleid'][0];
			$orderBy = 'order by s.sale_id desc';
		}elseif (array_key_exists('nextsaleid',$headerData)) {
			$nextPreviousWhere = $headerData['nextsaleid'][0]==0 ? '' : ' and s.sale_id > '.$headerData['nextsaleid'][0];
			$orderBy = 'order by s.sale_id asc';
		}elseif(array_key_exists('operation',$headerData)) {
			$nextPreviousWhere = '';
			if (strcmp($headerData['operation'][0],'first')==0) {
				$orderBy = 'order by s.sale_id asc';
			}elseif (strcmp($headerData['operation'][0],'last')==0) {
				$orderBy = 'order by s.sale_id desc';
			}else{
				return $exceptionArray['204'];
			}
		}else{
			$limit = '';
		}
		
		DB::beginTransaction();
		DB::statement('SET group_concat_max_len = 1000000');
		$raw = DB::connection($databaseName)->select("select 
		s.sale_id,
		s.product_array,
		s.payment_mode,
		s.bank_ledger_id,
		s.bank_name,
		s.invoice_number,
		s.job_card_number,
		s.check_number,
		s.total,
		s.total_discounttype,
		s.total_discount,
		s.total_cgst_percentage,
		s.total_sgst_percentage,
		s.total_igst_percentage,
		s.extra_charge,
		s.tax,
		s.grand_total,
		s.advance,
		s.balance,
		s.po_number,
		s.user_id,
		s.remark,
		s.entry_date,
		s.service_date,
		s.client_id,
		s.sales_type,
		s.refund,
		s.jf_id,
		s.print_count,
		s.company_id,
		s.branch_id,
		s.created_at,
		s.updated_at,
		e.expense,
		d.file
		from sales_bill as s 
		LEFT JOIN (
			SELECT 
				sale_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"saleExpenseId\":', sale_expense_id,
							 	', \"expenseType\":\"', IFNULL(expense_type,''),
							 	'\", \"expenseId\":', IFNULL(expense_id,0),
							 	', \"expenseName\":\"', IFNULL(expense_name,''),
							 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
							 	'\", \"expenseTax\":\"', IFNULL(expense_tax,''),
							 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
							 	'\", \"saleId\":', IFNULL(sale_id,0),
						 	' }'
						 ) SEPARATOR ', '),
					']'
				) expense
			FROM sale_expense_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY sale_id 
		) e ON e.sale_id = s.sale_id

		LEFT JOIN (
			SELECT 
				sale_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"documentId\":', document_id,
							 	', \"saleId\":', IFNULL(sale_id,0),
							 	', \"documentName\":\"', IFNULL(document_name,''),
							 	'\", \"documentSize\":\"', IFNULL(document_size,''),
							 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
							 	'\", \"documentType\":\"', IFNULL(document_type,''),
							 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
							 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
						 	'\" }'
						 ) SEPARATOR ', '),
					']'
				) file
			FROM sales_bill_doc_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY sale_id 
		) d ON d.sale_id = s.sale_id

		where ".$salesType."
		s.company_id = '".$headerData['companyid'][0]."' and
		s.deleted_at='0000-00-00 00:00:00' and s.is_draft='no' ".$isSalesOrder.$nextPreviousWhere."
		".$orderBy." ".$limit);
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			return json_encode($raw);
		}
	}
	
	/**
	 * get previous bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getSalePreviousNextData($headerData,$saleId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$isSalesOrder =  array_key_exists("issalesorder",$headerData) ? "and is_salesorder='ok'" : "and is_salesorder='not'";
		$salesType =  array_key_exists("issalesorder",$headerData) ? "" :"sales_type='".$headerData['salestype'][0]."' and";
		DB::beginTransaction();
		$saleData = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where ".$salesType."
		company_id = '".$headerData['companyid'][0]."' and
		deleted_at='0000-00-00 00:00:00' and 
		sale_id='".$saleId."' and is_draft='no' ".$isSalesOrder);
		DB::commit();
		return $saleData;
	}
	/**
	 * get previous bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getBillByJfId($companyId,$jfId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		DB::beginTransaction();
		DB::statement('SET group_concat_max_len = 1000000');
		$raw = DB::connection($databaseName)->select("select 
		s.sale_id,
		s.product_array,
		s.payment_mode,
		s.bank_ledger_id,
		s.bank_name,
		s.invoice_number,
		s.job_card_number,
		s.check_number,
		s.total,
		s.total_discounttype,
		s.total_discount,
		s.total_cgst_percentage,
		s.total_sgst_percentage,
		s.total_igst_percentage,
		s.extra_charge,
		s.tax,
		s.grand_total,
		s.advance,
		s.balance,
		s.po_number,
		s.user_id,
		s.remark,
		s.entry_date,
		s.service_date,
		s.client_id,
		s.sales_type,
		s.refund,
		s.jf_id,
		s.print_count,
		s.company_id,
		s.branch_id,
		s.created_at,
		s.updated_at,
		e.expense,
		d.file
		from sales_bill as s 
		LEFT JOIN (
			SELECT 
				sale_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"saleExpenseId\":', sale_expense_id,
							 	', \"expenseType\":\"', IFNULL(expense_type,''),
							 	'\", \"expenseId\":', IFNULL(expense_id,0),
							 	', \"expenseName\":\"', IFNULL(expense_name,''),
							 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
							 	'\", \"expenseTax\":\"', IFNULL(expense_tax,''),
							 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
							 	'\", \"saleId\":', IFNULL(sale_id,0),
						 	' }'
						 ) SEPARATOR ', '),
					']'
				) expense
			FROM sale_expense_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY sale_id 
		) e ON e.sale_id = s.sale_id

		LEFT JOIN (
			SELECT 
				sale_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"documentId\":', document_id,
							 	', \"saleId\":', IFNULL(sale_id,0),
							 	', \"documentName\":\"', IFNULL(document_name,''),
							 	'\", \"documentSize\":\"', IFNULL(document_size,''),
							 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
							 	'\", \"documentType\":\"', IFNULL(document_type,''),
							 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
							 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
						 	'\" }'
						 ) SEPARATOR ', '),
					']'
				) file
			FROM sales_bill_doc_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY sale_id 
		) d ON d.sale_id = s.sale_id
		where s.company_id = '$companyId' and s.jf_id = '$jfId' and
				deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		return json_encode($raw);
	}
	
	/**
	 * get document bill data(internal call)
	 * @param  bill-data
	 * returns the sales-data
	*/
	public function getDocumentData($saleArrayData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$documentResult = array();
		for($saleData=0;$saleData<count($saleArrayData);$saleData++)
		{
			$billExpenseResult = array();
			//get latest expense sale data from database
			DB::beginTransaction();
			$billExpenseResult = DB::connection($databaseName)->select("select 
			sale_expense_id as saleExpenseId,
			expense_type as expenseType,
			expense_id as expenseId,
			expense_name as expenseName,
			expense_value as expenseValue,
			expense_tax as expenseTax,
			expense_operation as expenseOperation,
			sale_id as saleId
			from sale_expense_dtl
			where deleted_at='0000-00-00 00:00:00' and
			sale_id=".$saleArrayData[$saleData]->sale_id);
			DB::commit();
			$saleArrayData[$saleData]->expense = $billExpenseResult;
			
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
			where sale_id='".$saleArrayData[$saleData]->sale_id."' and 
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
		$salesArrayData['salesData'] = json_encode($saleArrayData);
		$salesArrayData['documentData'] = json_encode($documentResult);
		return json_encode($salesArrayData);
	}
	
	/**
	 * update payment bill data
	 * @param  bill array data
	 * returns the exception-message/status
	*/
	public function updatePaymentData($arrayData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$paymentTransaction = $arrayData->payment_transaction;
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(strcmp($arrayData->payment_mode,"bank")==0)
		{
			if(strcmp($paymentTransaction,"payment")==0 || strcmp($paymentTransaction,"receipt")==0)
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("update
				sales_bill set
				payment_mode = '".$arrayData->payment_mode."',
				advance = '".$arrayData->advance."',
				balance = '".$arrayData->balance."'	,
				bank_name = '".$arrayData->bank_name."',
				check_number = '".$arrayData->check_number."',
				bank_ledger_id = '".$arrayData->bank_ledger_id."',
				entry_date = '".$arrayData->entry_date."',
				updated_at = '".$mytime."'
				where sale_id = ".$arrayData->sale_id." and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
			else
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("update
				sales_bill set
				payment_mode = '".$arrayData->payment_mode."',
				advance = '".$arrayData->advance."',
				balance = '".$arrayData->balance."'	,
				bank_name = '".$arrayData->bank_name."',
				refund = '".$arrayData->refund."',
				check_number = '".$arrayData->check_number."',
				bank_ledger_id = '".$arrayData->bank_ledger_id."',
				entry_date = '".$arrayData->entry_date."',
				updated_at = '".$mytime."'
				where sale_id = ".$arrayData->sale_id." and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
			
		}
		else
		{
			if(strcmp($paymentTransaction,"payment")==0 || strcmp($paymentTransaction,"receipt")==0)
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("update
				sales_bill set
				payment_mode = '".$arrayData->payment_mode."',
				advance = '".$arrayData->advance."',
				balance = '".$arrayData->balance."',
				entry_date = '".$arrayData->entry_date."',
				updated_at = '".$mytime."'
				where sale_id = ".$arrayData->sale_id." and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
			else
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("update
				sales_bill set
				payment_mode = '".$arrayData->payment_mode."',
				advance = '".$arrayData->advance."',
				refund = '".$arrayData->refund."',
				balance = '".$arrayData->balance."',
				entry_date = '".$arrayData->entry_date."',
				updated_at = '".$mytime."'
				where sale_id = ".$arrayData->sale_id." and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
		}
		if($raw!=1)
		{
			return $exceptionArray['500']; 
		}
		$saleIdData = $this->getSaleIdData($arrayData->sale_id);
		$jsonDecodedSaleData = json_decode($saleIdData);
		DB::beginTransaction();
		$saleTrnInsertionResult = DB::connection($databaseName)->statement("insert
		into sales_bill_trn(
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
		bank_name,
		invoice_number,
		job_card_number,
		check_number,
		total,
		extra_charge,
		tax,
		grand_total,
		advance,
		balance,
		po_number,
		user_id,
		remark,
		payment_trn,
		refund,
		entry_date,
		service_date,
		client_id,
		sales_type,
		company_id,
		branch_id,
		jf_id,
		created_at,
		updated_at)
		values(
		'".$jsonDecodedSaleData[0]->sale_id."',
		'".$jsonDecodedSaleData[0]->product_array."',
		'".$jsonDecodedSaleData[0]->payment_mode."',
		'".$jsonDecodedSaleData[0]->bank_ledger_id."',
		'".$jsonDecodedSaleData[0]->bank_name."',
		'".$jsonDecodedSaleData[0]->invoice_number."',
		'".$jsonDecodedSaleData[0]->job_card_number."',
		'".$jsonDecodedSaleData[0]->check_number."',
		'".$jsonDecodedSaleData[0]->total."',
		'".$jsonDecodedSaleData[0]->extra_charge."',
		'".$jsonDecodedSaleData[0]->tax."',
		'".$jsonDecodedSaleData[0]->grand_total."',
		'".$jsonDecodedSaleData[0]->advance."',
		'".$jsonDecodedSaleData[0]->balance."',
		'".$jsonDecodedSaleData[0]->po_number."',
		'".$jsonDecodedSaleData[0]->user_id."',
		'".$jsonDecodedSaleData[0]->remark."',
		'".$paymentTransaction."',
		'".$jsonDecodedSaleData[0]->refund."',
		'".$jsonDecodedSaleData[0]->entry_date."',
		'".$jsonDecodedSaleData[0]->service_date."',
		'".$jsonDecodedSaleData[0]->client_id."',
		'".$jsonDecodedSaleData[0]->sales_type."',
		'".$jsonDecodedSaleData[0]->company_id."',
		'".$jsonDecodedSaleData[0]->branch_id."',
		'".$jsonDecodedSaleData[0]->jf_id."',
		'".$jsonDecodedSaleData[0]->created_at."',
		'".$jsonDecodedSaleData[0]->updated_at."')");
		DB::commit();

		if($saleTrnInsertionResult!=1)
		{
			return $exceptionArray['500']; 
		}
		else
		{
			return $exceptionArray['200'];
		}
	}
	
	/**
	 * update bill status data
	 * @param  sale-id and bill-data array and image Array
	 * returns the exception-message/status
	*/
	public function updateStatusData($statusData)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$statusId = $statusData['dispatch_status'];
		$saleId = $statusData['sale_id'];
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update
			sales_bill set
			dispatch_status = '$statusId'
			where sale_id = '$saleId'
			");
		DB::commit();
		if ($raw==1) {
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	/**
	 * update bill data
	 * @param  sale-id and bill-data array and image Array
	 * returns the exception-message/status
	*/
	public function updateBillData($billArray,$saleId,$documentArray,$headerData)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$keyValueString = "";
		$expenseKey = "";
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(array_key_exists('operation',$headerData))
		{
			if ($headerData['operation'][0]== 'generate') {
				$this->updatePrintCount($saleId);
			}
		}
		$salesOrder = array_key_exists("issalesorderupdate",$headerData) ? "is_salesorder='ok'":"is_salesorder='not'";
		$salesOrderInsert = array_key_exists("issalesorderupdate",$headerData) ? "ok":"not";
		if(isset($documentArray) && !empty($documentArray))
		{
			for($docArray=0;$docArray<count($documentArray);$docArray++)
			{
				DB::beginTransaction();
				$documentResult = DB::connection($databaseName)->statement("insert into sales_bill_doc_dtl(
				sale_id,
				document_name,
				document_size,
				document_format,
				created_at) 
				values('".$saleId."','".$documentArray[$docArray][0]."','".$documentArray[$docArray][1]."','".$documentArray[$docArray][2]."','".$mytime."')");
				DB::commit();
				
				//get client-id from sale-bill
				DB::beginTransaction();
				$saleBillData = DB::connection($databaseName)->select("SELECT 
				sale_id,
				client_id
				FROM sales_bill where sale_id='".$saleId."' and deleted_at='0000-00-00 00:00:00' and is_draft='no' and ".$salesOrder);
				DB::commit();
				
				//insert document data into client-document table
				DB::beginTransaction();
				$clientDocumentInsertion = DB::connection($databaseName)->statement("insert into client_doc_dtl(
				sale_id,
				document_name,
				document_format,
				document_size,
				client_id,
				created_at)
				values('".$saleId."','".$documentArray[$docArray][0]."','".$documentArray[$docArray][2]."','".$documentArray[$docArray][1]."','".$saleBillData[0]->client_id."','".$mytime."')");
				DB::commit();
				if($documentResult==0)
				{
					return $exceptionArray['500'];
				}
			}	
		}
		if(array_key_exists('expense',$billArray))
		{
			$decodedExpenseData = json_decode($billArray['expense']);
			$expenseCount = count($decodedExpenseData);
			
			//delete expense data
			DB::beginTransaction();
			$deleteExpenseData = DB::connection($databaseName)->statement("update
			sale_expense_dtl set
			deleted_at = '".$mytime."'
			where sale_id = ".$saleId);
			DB::commit();
			
			for($expenseArray=0;$expenseArray<$expenseCount;$expenseArray++)
			{
				//insert expense data for update expense data
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into
				sale_expense_dtl(
				expense_type,
				expense_name,
				expense_value,
				expense_tax,
				expense_operation,
				sale_id,
				expense_id,
				created_at)
				values('".$decodedExpenseData[$expenseArray]->expenseType."',
				'".$decodedExpenseData[$expenseArray]->expenseName."',
				'".$decodedExpenseData[$expenseArray]->expenseValue."',
				'".$decodedExpenseData[$expenseArray]->expenseTax."',
				'".$decodedExpenseData[$expenseArray]->expenseOperation."',
				'".$saleId."',
				'".$decodedExpenseData[$expenseArray]->expenseId."',
				'".$mytime."')");
				DB::commit();
			}
			//remode expense from an array
			unset($billArray['expense']);
		}
		for($billArrayData=0;$billArrayData<count($billArray);$billArrayData++)
		{
			$keyValueString = $keyValueString.array_keys($billArray)[$billArrayData]." = '".$billArray[array_keys($billArray)[$billArrayData]]."',";
		}
		// update bill-data
		// try{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			sales_bill set
			".$keyValueString."
			updated_at = '".$mytime."'
			where sale_id = ".$saleId." and
			".$salesOrder." and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		// }
		// catch(\Illuminate\Database\QueryException $ex){ 
		//   dd($ex->getMessage()); 
		// }
		
		if($raw==1)
		{
			$saleData = $this->getSaleIdData($saleId);
			$jsonDecodedSaleData = json_decode($saleData);

			//insert bill data in bill_trn 
			DB::beginTransaction();
			$saleTrnInsertionResult = DB::connection($databaseName)->statement("insert
			into sales_bill_trn(
			sale_id,
			product_array,
			payment_mode,
			bank_ledger_id,
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
			is_salesorder,
			remark,
			entry_date,
			service_date,
			client_id,
			sales_type,
			company_id,
			branch_id,
			jf_id,
			created_at,
			updated_at)
			values(
			'".$jsonDecodedSaleData[0]->sale_id."',
			'".$jsonDecodedSaleData[0]->product_array."',
			'".$jsonDecodedSaleData[0]->payment_mode."',
			'".$jsonDecodedSaleData[0]->bank_ledger_id."',
			'".$jsonDecodedSaleData[0]->bank_name."',
			'".$jsonDecodedSaleData[0]->invoice_number."',
			'".$jsonDecodedSaleData[0]->job_card_number."',
			'".$jsonDecodedSaleData[0]->check_number."',
			'".$jsonDecodedSaleData[0]->total."',
			'".$jsonDecodedSaleData[0]->total_discounttype."',
			'".$jsonDecodedSaleData[0]->total_discount."',
			'".$jsonDecodedSaleData[0]->total_cgst_percentage."',
			'".$jsonDecodedSaleData[0]->total_sgst_percentage."',
			'".$jsonDecodedSaleData[0]->total_igst_percentage."',
			'".$jsonDecodedSaleData[0]->extra_charge."',
			'".$jsonDecodedSaleData[0]->tax."',
			'".$jsonDecodedSaleData[0]->grand_total."',
			'".$jsonDecodedSaleData[0]->advance."',
			'".$jsonDecodedSaleData[0]->balance."',
			'".$jsonDecodedSaleData[0]->po_number."',
			'".$jsonDecodedSaleData[0]->user_id."',
			'".$salesOrderInsert."',
			'".$jsonDecodedSaleData[0]->remark."',
			'".$jsonDecodedSaleData[0]->entry_date."',
			'".$jsonDecodedSaleData[0]->service_date."',
			'".$jsonDecodedSaleData[0]->client_id."',
			'".$jsonDecodedSaleData[0]->sales_type."',
			'".$jsonDecodedSaleData[0]->company_id."',
			'".$jsonDecodedSaleData[0]->branch_id."',
			'".$jsonDecodedSaleData[0]->jf_id."',
			'".$jsonDecodedSaleData[0]->created_at."',
			'".$jsonDecodedSaleData[0]->updated_at."')");
			DB::commit();
			
			if($saleTrnInsertionResult!=1)
			{
				return $exceptionArray['500']; 
			}
			else
			{
				return $exceptionArray['200'];
			}
		}
	}
	
	/**
	 * update bill-entry date
	 * @param  sale-id and bill-entryDate
	 * returns the exception-message/status
	*/
	public function updateBillEntryData($entryDate,$saleId,$headerData)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$salesOrder = array_key_exists("issalesorderupdate",$headerData) ? "is_salesorder='ok'" : "is_salesorder='not'";
		$salesOrderInsert = array_key_exists("issalesorderupdate",$headerData) ? "ok" : "not";
		// update bill-date
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update
		sales_bill set
		entry_date = '".$entryDate."',
		updated_at = '".$mytime."'
		where sale_id = ".$saleId." and
		".$salesOrder." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		if($raw==1)
		{
			$saleData = $this->getSaleIdData($saleId);
			$jsonDecodedSaleData = json_decode($saleData);
			
			//insert bill data in bill_trn 
			DB::beginTransaction();
			$saleTrnInsertionResult = DB::connection($databaseName)->statement("insert
			into sales_bill_trn(
			sale_id,
			product_array,
			payment_mode,
			bank_ledger_id,
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
			is_salesorder,
			remark,
			entry_date,
			service_date,
			client_id,
			sales_type,
			company_id,
			branch_id,
			jf_id,
			created_at,
			updated_at)
			values(
			'".$jsonDecodedSaleData[0]->sale_id."',
			'".$jsonDecodedSaleData[0]->product_array."',
			'".$jsonDecodedSaleData[0]->payment_mode."',
			'".$jsonDecodedSaleData[0]->bank_ledger_id."',
			'".$jsonDecodedSaleData[0]->bank_name."',
			'".$jsonDecodedSaleData[0]->invoice_number."',
			'".$jsonDecodedSaleData[0]->job_card_number."',
			'".$jsonDecodedSaleData[0]->check_number."',
			'".$jsonDecodedSaleData[0]->total."',
			'".$jsonDecodedSaleData[0]->total_discounttype."',
			'".$jsonDecodedSaleData[0]->total_discount."',
			'".$jsonDecodedSaleData[0]->total_cgst_percentage."',
			'".$jsonDecodedSaleData[0]->total_sgst_percentage."',
			'".$jsonDecodedSaleData[0]->total_igst_percentage."',
			'".$jsonDecodedSaleData[0]->extra_charge."',
			'".$jsonDecodedSaleData[0]->tax."',
			'".$jsonDecodedSaleData[0]->grand_total."',
			'".$jsonDecodedSaleData[0]->advance."',
			'".$jsonDecodedSaleData[0]->balance."',
			'".$jsonDecodedSaleData[0]->po_number."',
			'".$jsonDecodedSaleData[0]->user_id."',
			'".$salesOrderInsert."',
			'".$jsonDecodedSaleData[0]->remark."',
			'".$jsonDecodedSaleData[0]->entry_date."',
			'".$jsonDecodedSaleData[0]->service_date."',
			'".$jsonDecodedSaleData[0]->client_id."',
			'".$jsonDecodedSaleData[0]->sales_type."',
			'".$jsonDecodedSaleData[0]->company_id."',
			'".$jsonDecodedSaleData[0]->branch_id."',
			'".$jsonDecodedSaleData[0]->jf_id."',
			'".$jsonDecodedSaleData[0]->created_at."',
			'".$jsonDecodedSaleData[0]->updated_at."')");
			DB::commit();
			if($saleTrnInsertionResult!=1)
			{
				return $exceptionArray['500']; 
			}
			else
			{
				return $exceptionArray['200'];
			}
		}
	}
	
	/**
	 * update image data
	 * @param  image-array and saleId
	 * returns the exception-message/status
	*/
	public function updateImageData($saleId,$documentArray)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(isset($documentArray) && !empty($documentArray))
		{
			for($docArray=0;$docArray<count($documentArray);$docArray++)
			{
				DB::beginTransaction();
				$documentResult = DB::connection($databaseName)->statement("insert into sales_bill_doc_dtl(
				sale_id,
				document_name,
				document_size,
				document_format,
				created_at) 
				values('".$saleId."','".$documentArray[$docArray][0]."','".$documentArray[$docArray][1]."','".$documentArray[$docArray][2]."','".$mytime."')");
				DB::commit();
				
				//get client-id from sale-bill
				DB::beginTransaction();
				$saleBillData = DB::connection($databaseName)->select("SELECT 
				sale_id,
				client_id
				FROM sales_bill 
				where sale_id='".$saleId."' and 
				deleted_at='0000-00-00 00:00:00' 
				and is_draft='no' and is_salesorder='not'");
				DB::commit();
				
				//insert document data into client-document table
				DB::beginTransaction();
				$clientDocumentInsertion = DB::connection($databaseName)->statement("insert into client_doc_dtl(
				sale_id,
				document_name,
				document_format,
				document_size,
				client_id,
				created_at)
				values('".$saleId."','".$documentArray[$docArray][0]."','".$documentArray[$docArray][2]."','".$documentArray[$docArray][1]."','".$saleBillData[0]->client_id."','".$mytime."')");
				DB::commit();
				if($documentResult==0)
				{
					return $exceptionArray['500'];
				}
			}
			return $exceptionArray['200'];		
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get last 2 records of transaction data
	 * @param  saleId
	 * returns the exception-message/status
	*/
	public function getTransactionData($saleId)
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
		sale_trn_id,
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		company_id,
		branch_id,
		jf_id,
		payment_trn,
		refund,
		created_at,
		updated_at
		from sales_bill_trn 
		where sale_id='".$saleId."' and 
		deleted_at='0000-00-00 00:00:00'
		ORDER BY sale_trn_id DESC
		limit 2");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['500'];
		}
		else
		{
			return $raw;
		}
	}
	
	/**
	 * get bill data
	 * @param  invoice-number
	 * returns the exception-message/data
	*/
	public function getInvoiceNumberData($invoiceNumber)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$billData = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where invoice_number='".$invoiceNumber."' and
		deleted_at='0000-00-00 00:00:00' and 
		is_draft='no' and is_salesorder='not'");
		DB::commit();
		if(count($billData)!=0)
		{
			return json_encode($billData);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get bill data
	 * @param  fromdate,todate
	 * returns the exception-message/data
	*/
	public function getFromToDateData($fromDate,$toDate)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$billData = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where (entry_Date BETWEEN '".$fromDate."' AND '".$toDate."') and
		deleted_at='0000-00-00 00:00:00' and is_draft='no' and is_salesorder='not'");
		DB::commit();
		if(count($billData)!=0)
		{
			return json_encode($billData);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}

	/**
	 * get bill data
	 * @param  fromdate,todate,companyId
	 * returns the exception-message/data
	*/
	public function getFromToDateCompanyData($fromDate,$toDate,$companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
	
		DB::beginTransaction();
		$billData = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where (entry_Date BETWEEN '".$fromDate."' AND '".$toDate."') and
		company_id='".$companyId."' and
		deleted_at='0000-00-00 00:00:00' and is_draft='no' and is_salesorder='not'");
		DB::commit();
		if(count($billData)!=0)
		{
			return json_encode($billData);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get bill-imps data
	 * @param  fromdate,todate,companyId
	 * returns the exception-message/data
	*/
	public function getImpsData($fromDate,$toDate,$companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$billData = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		payment_mode,
		bank_ledger_id,
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
		entry_date,
		service_date,
		client_id,
		sales_type,
		refund,
		company_id,
		branch_id,
		jf_id,
		print_count,
		created_at,
		updated_at 
		from sales_bill 
		where (entry_Date BETWEEN '".$fromDate."' AND '".$toDate."') and
		company_id='".$companyId."' and
		payment_mode='imps' and 
		deleted_at='0000-00-00 00:00:00' and is_draft='no' and is_salesorder='not'");
		DB::commit();

		if(count($billData)!=0)
		{
			return json_encode($billData);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}

	/**
	 * delete bill data
	 * @param  sale-id
	 * returns the exception-message/status
	*/
	public function validateChequeNo($chequeNo,$billId=null)
	{
		$flag=0;
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//get setting-data and check the settings is enable/disable
		$settingModel = new SettingModel();
		$settingData = $settingModel->getParticularTypeData("chequeno");
		$decodedSettingData = json_decode($settingData);
		if(strcmp($settingData,$exceptionArray['204'])==0)
		{
			$flag=1;
		}
		else
		{
			//check cheque-no is enable/disable
			foreach ($decodedSettingData as $key => $value)
			{
				if(strcmp($decodedSettingData[$key]->setting_type,"chequeno")==0)
				{
					$settingData = json_decode($decodedSettingData[$key]->setting_data);
					if(strcmp($settingData->chequeno_status,'disable')==0)
					{
						$flag=1;
					}
				}
			}
		}
		if($flag==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			$billString="";
			if($billId!=null)
			{
				$billString = $billString."sale_id !=".$billId." and ";
			}
			//check cheque-no
			DB::beginTransaction();
			$chequeNoResult = DB::connection($databaseName)->select("select 
			sale_id
			from sales_bill
			where check_number='".$chequeNo."' and ".$billString."
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($chequeNoResult)==0)
			{
				return $exceptionArray['200'];
			}
			else
			{
				return $exceptionArray['500'];
			}
		}
	}

	/**
	 * delete bill data
	 * @param  sale-id
	 * returns the exception-message/status
	*/
	public function deleteBillData($saleId,$deletedBy = 0)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$saleIdData = $this->getSaleIdData($saleId);
		$jsonDecodedSaleData = json_decode($saleIdData);
		// $productArray = $jsonDecodedSaleData[0]->product_array;
		// $inventoryCount = count(json_decode($productArray)->inventory);
		// for($productArrayData=0;$productArrayData<$inventoryCount;$productArrayData++)
		// {
		// 	$inventoryData = json_decode($productArray)->inventory;
		// 	DB::beginTransaction();
		// 	$getTransactionSummaryData[$productArrayData] = DB::connection($databaseName)->select("select 
		// 	product_trn_summary_id,
		// 	qty
		// 	from product_trn_summary
		// 	where product_id='".$inventoryData[$productArrayData]->productId."' and
		// 	deleted_at='0000-00-00 00:00:00'");
		// 	DB::commit();
		// 	if(count($getTransactionSummaryData[$productArrayData])==0)
		// 	{
		// 		//insert data
		// 		// DB::beginTransaction();
		// 		// $insertionResult[$productArrayData] = DB::connection($databaseName)->statement("insert into 
		// 		// product_trn_summary(qty,company_id,branch_id,product_id)
		// 		// values('".$inventoryData[$productArrayData]->qty."',
		// 		// 	   '".$jsonDecodedSaleData[0]->company_id."',
		// 		// 	   0,
		// 		// 	   '".$inventoryData[$productArrayData]->productId."')");
		// 		// DB::commit();
		// 	}
		// 	else
		// 	{
		// 		$qty = $getTransactionSummaryData[$productArrayData][0]->qty+$inventoryData[$productArrayData]->qty;
		// 		//update data
		// 		DB::beginTransaction();
		// 		$updateResult = DB::connection($databaseName)->statement("update 
		// 		product_trn_summary set qty='".$qty."'
		// 		where product_trn_summary_id='".$getTransactionSummaryData[$productArrayData][0]->product_trn_summary_id."' and
		// 		deleted_at='0000-00-00 00:00:00'");
		// 		DB::commit();
		// 	}
		// }
		if(strcmp($saleIdData,$exceptionArray['404'])==0)
		{
			return $exceptionArray['404'];
		}
		//get ledger id from journal
		$journalModel = new JournalModel();
		$journalData = $journalModel->getJfIdArrayData($jsonDecodedSaleData[0]->jf_id);
		$jsonDecodedJournalData = json_decode($journalData);
		
		if(strcmp($journalData,$exceptionArray['404'])!=0)
		{
			foreach ($jsonDecodedJournalData as $value)
			{
				//delete ledgerId_ledger_dtl data as per given ledgerId and jf_id
				DB::beginTransaction();
				$deleteLedgerData = DB::connection($databaseName)->statement("update
				".$value->ledger_id."_ledger_dtl set
				deleted_at = '".$mytime."'
				where jf_id = ".$jsonDecodedSaleData[0]->jf_id." and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
		}
		//delete journal data
		DB::beginTransaction();
		$deleteJournalData = DB::connection($databaseName)->statement("update
		journal_dtl set
		deleted_at = '".$mytime."'
		where jf_id = ".$jsonDecodedSaleData[0]->jf_id." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
			
		//delete product_trn data
		DB::beginTransaction();
		$deleteProductTrnData = DB::connection($databaseName)->statement("update
		product_trn set
		deleted_at = '".$mytime."'
		where jf_id = ".$jsonDecodedSaleData[0]->jf_id." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//delete bill data 
		DB::beginTransaction();
		$deleteBillData = DB::connection($databaseName)->statement("update
		sales_bill set
		deleted_at = '".$mytime."',
		deleted_by = '".$deletedBy."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();

		//delete bill expense data 
		DB::beginTransaction();
		$deleteBillExpenseData = DB::connection($databaseName)->statement("update
		sale_expense_dtl set
		deleted_at = '".$mytime."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//delete bill-transaction data 
		DB::beginTransaction();
		$deleteBillTrnData = DB::connection($databaseName)->statement("update
		sales_bill_trn set
		deleted_at = '".$mytime."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		
		if($deleteJournalData==1 && $deleteProductTrnData==1 && $deleteBillData==1 && $deleteBillTrnData==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * update print_count data
	 * @param  sale-id
	 * returns the exception-message/status
	*/
	function updatePrintCount($saleId)
	{
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		DB::beginTransaction();
		$deleteBillData = DB::connection($databaseName)->statement("update
		sales_bill set
		print_count = print_count + 1
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
	}
	/**
	 * delete bill-draft data
	 * @param  sale-id
	 * returns the exception-message/status
	*/
	function deleteBillDraftData($saleId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//delete bill data 
		DB::beginTransaction();
		$deleteBillData = DB::connection($databaseName)->statement("update
		sales_bill set
		deleted_at = '".$mytime."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();

		//delete bill expense data 
		DB::beginTransaction();
		$deleteBillExpenseData = DB::connection($databaseName)->statement("update
		sale_expense_dtl set
		deleted_at = '".$mytime."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();

		if( $deleteBillData==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * delete bill-draft data
	 * @param  sale-id
	 * returns the exception-message/status
	*/
	function deleteSaleOrderData($saleId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//delete bill data 
		DB::beginTransaction();
		$deleteBillData = DB::connection($databaseName)->statement("update
		sales_bill set
		deleted_at = '".$mytime."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		//delete bill expense data 
		DB::beginTransaction();
		$deleteBillExpenseData = DB::connection($databaseName)->statement("update
		sale_expense_dtl set
		deleted_at = '".$mytime."'
		where sale_id = ".$saleId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if( $deleteBillData==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}