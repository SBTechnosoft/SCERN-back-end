<?php
namespace ERP\Model\Accounting\PurchaseBills;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Accounting\Journals\JournalModel;
// use ERP\Core\Settings\InvoiceNumbers\Services\InvoiceService;
// use ERP\Api\V1_0\Settings\InvoiceNumbers\Controllers\InvoiceController;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use stdClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PurchaseBillModel extends Model
{
	protected $table = 'purchase_bill';
	
	/**
	 * insert data with document
	 * @param  array
	 * returns the status
	*/
	public function insertData($getData,$keyName,$documentArray,$requestData)
	{
		$mytime = Carbon\Carbon::now();
		// $requestInput = $requestData->input();

		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$isPurchaseOrderInsert = array_key_exists("ispurchaseorder",$requestData->header())?"ok":"not";
		$purchaseData="";
		$keyData="";
		$purchaseExpenseData="";
		$purchaseExpenseKey="";
		$decodedJsonExpense = array();
		for($data=0;$data<count($getData);$data++)
		{
			if(strcmp($keyName[$data],'expense')==0)
			{
				$decodedJsonExpense = json_decode($getData[$data]);
			}
			else
			{
				if($data == (count($getData)-1))
				{
					$purchaseData = $purchaseData."'".$getData[$data]."'";
					$keyData =$keyData.$keyName[$data];
				}
				else
				{
					$purchaseData = $purchaseData."'".$getData[$data]."',";
					$keyData =$keyData.$keyName[$data].",";
				}
			}
		}
		//purchase-data save
		DB::beginTransaction();
		$purchaseBillResult = DB::connection($databaseName)->statement("insert into purchase_bill(".$keyData.",is_purchaseorder,created_at) 
		values(".$purchaseData.",'".$isPurchaseOrderInsert."','".$mytime."')");
		DB::commit();

		//get latest purchase-id from database
		DB::beginTransaction();
		$purchaseIdResult = DB::connection($databaseName)->select("SELECT
		max(purchase_id) purchase_id
		FROM purchase_bill where deleted_at='0000-00-00 00:00:00' and is_purchaseorder = '".$isPurchaseOrderInsert."'");
		DB::commit();

		if(count($decodedJsonExpense)!=0)
		{
			$expenseCount = count($decodedJsonExpense);
			for($expenseData=0;$expenseData<$expenseCount;$expenseData++)
			{
				//Insertion in purchase_expense_dtl
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into purchase_expense_dtl(
				expense_name,
				expense_type,
				expense_value,
				expense_operation,
				purchase_id,
				expense_id,
				created_at)
				values(
				'".$decodedJsonExpense[$expenseData]->expenseName."',
				'".$decodedJsonExpense[$expenseData]->expenseType."',
				'".$decodedJsonExpense[$expenseData]->expenseValue."',
				'".$decodedJsonExpense[$expenseData]->expenseOperation."',
				'".$purchaseIdResult[0]->purchase_id."',
				'".$decodedJsonExpense[$expenseData]->expenseId."',
				'".$mytime."')");
				DB::commit();
			}
		}
		
		if(count($documentArray)!=0)
		{
			$documentCount = count($documentArray);
			//document insertion
			for($documentData=0;$documentData<$documentCount;$documentData++)
			{
				DB::beginTransaction();
				$purchaseBillResult = DB::connection($databaseName)->statement("insert into purchase_doc_dtl
				(document_name,
				document_size,
				document_format,
				purchase_id,
				created_at) 
				values(
				'".$documentArray[$documentData]['document_name']."',
				".$documentArray[$documentData]['document_size'].",
				'".$documentArray[$documentData]['document_format']."',
				".$purchaseIdResult[0]->purchase_id.",
				'".$mytime."')");
				DB::commit();
			}
		}
		if($purchaseBillResult==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * insert data with document
	 * @param  array
	 * returns the status
	*/
	public function udpateData($getData,$keyName,$documentArray,$purchaseId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		$decodedExpenseData = array();
		for($data=0;$data<count($getData);$data++)
		{
			if(strcmp($keyName[$data],'expense')==0)
			{
				$decodedExpenseData = json_decode($getData[$data]);
			}
			else
			{
				$keyValueString=$keyValueString.$keyName[$data]."='".$getData[$data]."',";
			}
			
		}
		DB::beginTransaction();
		$purchaseBillResult = DB::connection($databaseName)->statement("update purchase_bill
		set ".$keyValueString."updated_at='".$mytime."' where purchase_id='".$purchaseId."'");
		DB::commit();
		//delete expense data
		DB::beginTransaction();
		$deleteExpenseData = DB::connection($databaseName)->statement("update
		purchase_expense_dtl set
		deleted_at = '".$mytime."'
		where purchase_id = ".$purchaseId);
		DB::commit();
		$expenseCount = count($decodedExpenseData);
		if($expenseCount!=0)
		{
			for($expenseData=0;$expenseData<$expenseCount;$expenseData++)
			{
				//insert expense data for update expense data
				DB::beginTransaction();
				$insertExpenseData = DB::connection($databaseName)->statement("insert into
				purchase_expense_dtl(
				expense_type,
				expense_name,
				expense_value,
				expense_operation,
				purchase_id,
				expense_id,
				created_at)
				values('".$decodedExpenseData[$expenseData]->expenseType."',
				'".$decodedExpenseData[$expenseData]->expenseName."',
				'".$decodedExpenseData[$expenseData]->expenseValue."',
				'".$decodedExpenseData[$expenseData]->expenseOperation."',
				'".$purchaseId."',
				'".$decodedExpenseData[$expenseData]->expenseId."',
				'".$mytime."')");
				DB::commit();
			}
		}
	    $documentCount = count($documentArray);
		if($documentCount!=0)
		{
			//document insertion
			for($documentData=0;$documentData<$documentCount;$documentData++)
			{
				DB::beginTransaction();
				$purchaseBillResultDoc = DB::connection($databaseName)->statement("insert into purchase_doc_dtl
				(document_name,
				document_size,
				document_format,
				purchase_id,
				created_at) 
				values(
				'".$documentArray[$documentData]['document_name']."',
				".$documentArray[$documentData]['document_size'].",
				'".$documentArray[$documentData]['document_format']."',
				".$purchaseId.",
				'".$mytime."')");
				DB::commit();
			}
		}
		if($purchaseBillResult==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get purchase bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getPurchaseBillData($headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$isPurchaseOrder = array_key_exists('ispurchaseorder',$headerData) ? "p.is_purchaseorder='ok'" : "p.is_purchaseorder='not'";
		$orderBy = '';
		$extraQuery = '';
		if(array_key_exists('previouspurchaseid',$headerData))
		{
			$extraQuery = $headerData['previouspurchaseid'][0]==0 ? '' : ' and p.purchase_id < '.$headerData['previouspurchaseid'][0];
			$orderBy = ' order by p.purchase_id desc limit 1';
		}
		elseif(array_key_exists('nextpurchaseid',$headerData))
		{
			$extraQuery = $headerData['nextpurchaseid'][0]==0 ? '' : ' and p.purchase_id > '.$headerData['nextpurchaseid'][0];
			$orderBy = ' order by p.purchase_id asc limit 1';
			
		}
		elseif (array_key_exists('operation',$headerData))
		{

			if(strcmp($headerData['operation'][0],'first')==0)
			{
				$orderBy = " order by purchase_id asc limit 1";
			}
			elseif (strcmp($headerData['operation'][0], 'last')==0) 
			{
				$orderBy = " order by purchase_id desc limit 1";
			}
			else
			{
				return $exceptionArray['204'];
			}

		}
		elseif(array_key_exists('purchasebillid',$headerData))
		{
			$extraQuery = " and p.purchase_id = '".$headerData['purchasebillid'][0]."'";

		}
		//get all the purchase-bill data
		DB::beginTransaction();
		$purchaseIdDataResult = DB::connection($databaseName)->select("select 
		p.purchase_id,
		p.vendor_id,
		p.product_array,
		p.bill_number,
		p.total,
		p.tax,
		p.grand_total,
		p.payment_mode,
		p.bank_ledger_id,
		p.bank_name,
		p.check_number,
		p.total_discounttype,
		p.total_discount,
		p.total_cgst_percentage,
		p.total_sgst_percentage,
		p.total_igst_percentage,
		p.advance,
		p.extra_charge,
		p.balance,
		p.transaction_type,
		p.transaction_date,
		p.entry_date,
		p.due_date,
		p.bill_type,
		p.remark,
		p.company_id,
		p.jf_id,
		p.created_at,
		p.updated_at,
		e.expense,
		d.file
		from purchase_bill as p
		LEFT JOIN (
			SELECT 
				purchase_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"purchaseExpenseId\":', purchase_expense_id,
							 	', \"expenseType\":\"', IFNULL(expense_type,''),
							 	'\", \"expenseId\":', IFNULL(expense_id,0),
							 	', \"expenseName\":\"', IFNULL(expense_name,''),
							 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
							 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
							 	'\", \"purchaseId\":', IFNULL(purchase_id,0),
						 	' }'
						 ) SEPARATOR ', '),
					']'
				) expense
			FROM purchase_expense_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY purchase_id 
		) e ON e.purchase_id = p.purchase_id

		LEFT JOIN (
			SELECT 
				purchase_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"documentId\":', document_id,
							 	', \"purchaseId\":', IFNULL(purchase_id,0),
							 	', \"documentName\":\"', IFNULL(document_name,''),
							 	'\", \"documentSize\":\"', IFNULL(document_size,''),
							 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
							 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
							 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
						 	'\" }'
						 ) SEPARATOR ', '),
					']'
				) file
			FROM purchase_doc_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY purchase_id 
		) d ON d.purchase_id = p.purchase_id
		where p.bill_type='purchase_bill' and
		p.deleted_at='0000-00-00 00:00:00' and ".$isPurchaseOrder.$extraQuery.$orderBy);
		DB::commit();
		
		if(count($purchaseIdDataResult)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			return json_encode($purchaseIdDataResult);
		}
	}
	
	/**
	 * get previous-next purchase-bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getPurchaseBillByJfId($companyId,$jfId)
	{

		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		DB::beginTransaction();
		$purchaseData = DB::connection($databaseName)->select("select 
		p.purchase_id,
		p.vendor_id,
		p.product_array,
		p.bill_number,
		p.total,
		p.tax,
		p.grand_total,
		p.payment_mode,
		p.bank_ledger_id,
		p.bank_name,
		p.check_number,
		p.total_discounttype,
		p.total_discount,
		p.total_cgst_percentage,
		p.total_sgst_percentage,
		p.total_igst_percentage,
		p.advance,
		p.extra_charge,
		p.balance,
		p.transaction_type,
		p.transaction_date,
		p.entry_date,
		p.due_date,
		p.bill_type,
		p.remark,
		p.company_id,
		p.jf_id,
		p.created_at,
		p.updated_at,
		e.expense,
		d.file
		from purchase_bill as p
		LEFT JOIN (
			SELECT 
				purchase_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"purchaseExpenseId\":', purchase_expense_id,
							 	', \"expenseType\":\"', IFNULL(expense_type,''),
							 	'\", \"expenseId\":', IFNULL(expense_id,0),
							 	', \"expenseName\":\"', IFNULL(expense_name,''),
							 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
							 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
							 	'\", \"purchaseId\":', IFNULL(purchase_id,0),
						 	' }'
						 ) SEPARATOR ', '),
					']'
				) expense
			FROM purchase_expense_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY purchase_id 
		) e ON e.purchase_id = p.purchase_id

		LEFT JOIN (
			SELECT 
				purchase_id, 
				CONCAT( 
					'[', 
						GROUP_CONCAT( CONCAT( 
							'{\"documentId\":', document_id,
							 	', \"purchaseId\":', IFNULL(purchase_id,0),
							 	', \"documentName\":\"', IFNULL(document_name,''),
							 	'\", \"documentSize\":\"', IFNULL(document_size,''),
							 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
							 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
							 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
						 	'\" }'
						 ) SEPARATOR ', '),
					']'
				) file
			FROM purchase_doc_dtl
			WHERE deleted_at='0000-00-00 00:00:00'
			GROUP BY purchase_id 
		) d ON d.purchase_id = p.purchase_id
		where p.bill_type='purchase_bill' and
		p.company_id = '$companyId' and p.jf_id = '$jfId' and
		p.deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		return json_encode($purchaseData);
	}
	
	/**
	 * get document purchase-bill data(internal call)
	 * @param  purchase-bill-data
	 * returns the purchase-bill-data
	*/
	public function getDocumentData($purchaseArrayData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$documentResult = array();
		for($purchaseData=0;$purchaseData<count($purchaseArrayData);$purchaseData++)
		{
			DB::beginTransaction();
			$purchaseExpenseResult[$purchaseData] = DB::connection($databaseName)->select("select 
			purchase_expense_id as purchaseExpenseId,
			expense_id as expenseId,
			expense_name as expenseName,
			expense_type as expenseType,
			expense_value as expenseValue,
			expense_operation as expenseOperation,
			purchase_id as purchaseId
			from purchase_expense_dtl 
			where deleted_at='0000-00-00 00:00:00' and 
			purchase_id = ".$purchaseArrayData[$purchaseData]->purchase_id);
			DB::commit();
			$purchaseArrayData[$purchaseData]->expense = $purchaseExpenseResult[$purchaseData];
			
			DB::beginTransaction();
			$documentResult[$purchaseData] = DB::connection($databaseName)->select("select
			document_id,
			purchase_id,
			document_name,
			document_size,
			document_format,
			created_at,
			updated_at
			from purchase_doc_dtl
			where purchase_id='".$purchaseArrayData[$purchaseData]->purchase_id."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($documentResult[$purchaseData])==0)
			{
				$documentResult[$purchaseData] = array();
				$documentResult[$purchaseData][0] = new stdClass();
				$documentResult[$purchaseData][0]->document_id = 0;
				$documentResult[$purchaseData][0]->purchase_id = 0;
				$documentResult[$purchaseData][0]->document_name = '';
				$documentResult[$purchaseData][0]->document_size = 0;
				$documentResult[$purchaseData][0]->document_format = '';
				// $documentResult[$purchaseData][0]->document_type ='purchase-bill';
				$documentResult[$purchaseData][0]->created_at = '0000-00-00 00:00:00';
				$documentResult[$purchaseData][0]->updated_at = '0000-00-00 00:00:00';
			}
		}
		
		$purchaseDataArray = array();
		$purchaseDataArray['purchaseBillData'] = json_encode($purchaseArrayData);
		$purchaseDataArray['documentData'] = json_encode($documentResult);
		return json_encode($purchaseDataArray);
	}
	
	/**
	 * get purchase-bill data
	 * @param  company-id,from-date,to-date
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
		$isPurchaseOrder = array_key_exists("ispurchaseorder",$data) ? "p.is_purchaseorder='ok'" : "p.is_purchaseorder='not'";
		
		if(is_object($data))
		{
			$fromDate = $data->getFromDate();
			$toDate = $data->getToDate();
		
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("select 
			p.purchase_id,
			p.vendor_id,
			p.product_array,
			p.bill_number,
			p.total,
			p.tax,
			p.grand_total,
			p.payment_mode,
			p.bank_ledger_id,
			p.bank_name,
			p.check_number,
			p.total_discounttype,
			p.total_discount,
			p.total_cgst_percentage,
			p.total_sgst_percentage,
			p.total_igst_percentage,
			p.advance,
			p.extra_charge,
			p.balance,
			p.transaction_type,
			p.transaction_date,
			p.entry_date,
			p.due_date,
			p.bill_type,
			p.remark,
			p.company_id,
			p.jf_id,
			p.created_at,
			p.updated_at,
			e.expense,
			d.file
			from purchase_bill as p
			LEFT JOIN (
				SELECT 
					purchase_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"purchaseExpenseId\":', purchase_expense_id,
								 	', \"expenseType\":\"', IFNULL(expense_type,''),
								 	'\", \"expenseId\":', IFNULL(expense_id,0),
								 	', \"expenseName\":\"', IFNULL(expense_name,''),
								 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
								 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
								 	'\", \"purchaseId\":', IFNULL(purchase_id,0),
							 	' }'
							 ) SEPARATOR ', '),
						']'
					) expense
				FROM purchase_expense_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY purchase_id 
			) e ON e.purchase_id = p.purchase_id

			LEFT JOIN (
				SELECT 
					purchase_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"documentId\":', document_id,
								 	', \"purchaseId\":', IFNULL(purchase_id,0),
								 	', \"documentName\":\"', IFNULL(document_name,''),
								 	'\", \"documentSize\":\"', IFNULL(document_size,''),
								 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
								 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
								 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
							 	'\" }'
							 ) SEPARATOR ', '),
						']'
					) file
				FROM purchase_doc_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY purchase_id 
			) d ON d.purchase_id = p.purchase_id
			where p.bill_type='purchase_bill' and
			(p.entry_date BETWEEN '".$fromDate."' AND '".$toDate."') and 
			p.company_id='".$companyId."' and 
			p.deleted_at='0000-00-00 00:00:00' and ".$isPurchaseOrder);
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
			$raw = DB::connection($databaseName)->select("select 
			p.purchase_id,
			p.vendor_id,
			p.product_array,
			p.bill_number,
			p.total,
			p.tax,
			p.grand_total,
			p.payment_mode,
			p.bank_ledger_id,
			p.bank_name,
			p.check_number,
			p.total_discounttype,
			p.total_discount,
			p.total_cgst_percentage,
			p.total_sgst_percentage,
			p.total_igst_percentage,
			p.advance,
			p.extra_charge,
			p.balance,
			p.transaction_type,
			p.transaction_date,
			p.entry_date,
			p.due_date,
			p.bill_type,
			p.remark,
			p.company_id,
			p.jf_id,
			p.created_at,
			p.updated_at,
			e.expense,
			d.file
			from purchase_bill as p
			LEFT JOIN (
				SELECT 
					purchase_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"purchaseExpenseId\":', purchase_expense_id,
								 	', \"expenseType\":\"', IFNULL(expense_type,''),
								 	'\", \"expenseId\":', IFNULL(expense_id,0),
								 	', \"expenseName\":\"', IFNULL(expense_name,''),
								 	'\", \"expenseValue\":\"', IFNULL(expense_value,''),
								 	'\", \"expenseOperation\":\"', IFNULL(expense_operation,''),
								 	'\", \"purchaseId\":', IFNULL(purchase_id,0),
							 	' }'
							 ) SEPARATOR ', '),
						']'
					) expense
				FROM purchase_expense_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY purchase_id 
			) e ON e.purchase_id = p.purchase_id

			LEFT JOIN (
				SELECT 
					purchase_id, 
					CONCAT( 
						'[', 
							GROUP_CONCAT( CONCAT( 
								'{\"documentId\":', document_id,
								 	', \"purchaseId\":', IFNULL(purchase_id,0),
								 	', \"documentName\":\"', IFNULL(document_name,''),
								 	'\", \"documentSize\":\"', IFNULL(document_size,''),
								 	'\", \"documentFormat\":\"', IFNULL(document_format,''),
								 	'\", \"createdAt\":\"', DATE_FORMAT(created_at, '%d-%m-%Y'),
								 	'\", \"updatedAt\":\"', DATE_FORMAT(updated_at, '%d-%m-%Y'),
							 	'\" }'
							 ) SEPARATOR ', '),
						']'
					) file
				FROM purchase_doc_dtl
				WHERE deleted_at='0000-00-00 00:00:00'
				GROUP BY purchase_id 
			) d ON d.purchase_id = p.purchase_id
			where p.bill_type='purchase_bill' and
			".$isPurchaseOrder." and
			p.company_id='".$companyId."' and 
			p.deleted_at='0000-00-00 00:00:00' and 
			(p.bill_number='".$data['billnumber'][0]."' or p.vendor_id in (select ledger_id from ledger_mst where ledger_name like '%".$data['billnumber'][0]."%'))");
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
	 * delete purchase-bill data
	 * @param  purchase-bill-id
	 * returns status/error-message
	*/
	public function deletePurchaseBillData($requestData,$purchaseId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$purchaseArray = array();
		$purchaseArray['purchasebillid'][0] = $purchaseId;
		if(array_key_exists('ispurchaseorder', $requestData)){
			$purchaseArray['ispurchaseorder'][0] = 'ok';
		}
		
		$purchaseIdData = $this->getPurchaseBillData($purchaseArray);
		$jsonDecodedPurchaseData = json_decode(json_decode($purchaseIdData)->purchaseBillData);
		
		// $productArray = $jsonDecodedPurchaseData[0]->product_array;
		// $inventoryCount = count(json_decode($productArray));
		// for($productArrayData=0;$productArrayData<$inventoryCount;$productArrayData++)
		// {
		// 	$inventoryData = json_decode($productArray);
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
		// 		// $qty = $inventoryData[$productArrayData]->qty*(-1);
		// 		// //insert data
		// 		// DB::beginTransaction();
		// 		// $insertionResult[$productArrayData] = DB::connection($databaseName)->statement("insert into 
		// 		// product_trn_summary(qty,company_id,branch_id,product_id)
		// 		// values('".$qty."',
		// 		// 	   '".$jsonDecodedPurchaseData[0]->company_id."',
		// 		// 	   0,
		// 		// 	   '".$inventoryData[$productArrayData]->productId."')");
		// 		// DB::commit();
		// 	}
		// 	else
		// 	{
		// 		$qty = $getTransactionSummaryData[$productArrayData][0]->qty-$inventoryData[$productArrayData]->qty;
		// 		//update data
		// 		DB::beginTransaction();
		// 		$updateResult = DB::connection($databaseName)->statement("update 
		// 		product_trn_summary set qty='".$qty."'
		// 		where product_trn_summary_id='".$getTransactionSummaryData[$productArrayData][0]->product_trn_summary_id."' and
		// 		deleted_at='0000-00-00 00:00:00'");
		// 		DB::commit();
		// 	}
		// }
		//get ledger id from journal
		$journalModel = new JournalModel();
		$journalData = $journalModel->getJfIdArrayData($jsonDecodedPurchaseData[0]->jf_id);
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
				where jf_id = ".$jsonDecodedPurchaseData[0]->jf_id." and
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
			}
		}
		//delete journal data
		DB::beginTransaction();
		$deleteJournalData = DB::connection($databaseName)->statement("update
		journal_dtl set
		deleted_at = '".$mytime."'
		where jf_id = ".$jsonDecodedPurchaseData[0]->jf_id." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		//delete product_trn data
		DB::beginTransaction();
		$deleteProductTrnData = DB::connection($databaseName)->statement("update
		product_trn set
		deleted_at = '".$mytime."'
		where jf_id = ".$jsonDecodedPurchaseData[0]->jf_id." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		//delete purchase-bill data 
		DB::beginTransaction();
		$deleteBillData = DB::connection($databaseName)->statement("update
		purchase_bill set
		deleted_at = '".$mytime."'
		where purchase_id = ".$purchaseId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();

		//delete purchase-expense-bill data 
		DB::beginTransaction();
		$deleteBillData = DB::connection($databaseName)->statement("update
		purchase_expense_dtl set
		deleted_at = '".$mytime."'
		where purchase_id = ".$purchaseId." and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if($deleteJournalData==1 && $deleteProductTrnData==1 && $deleteBillData==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}
