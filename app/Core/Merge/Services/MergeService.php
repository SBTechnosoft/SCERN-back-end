<?php
namespace ERP\Core\Merge\Services;

use Carbon;
use DB;
use ERP\Core\Accounting\Ledgers\Entities\LedgerArray;
use ERP\Core\Merge\Persistables\MergePersistable;
use ERP\Core\Support\Service\AbstractService;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Merge\MergeModel;
use ERP\Model\Products\ProductModel;
use ERP\Core\Merge\Services\SpecialUtilityService;
use Exception;

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
	public function get($id, $name)
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
		$newProductData = json_decode($newProductStatus, true);
		$mergeModel = new MergeModel();
		DB::beginTransaction();
		try {
			$status = $mergeModel->getProductTrnData($fromProductId);
			if (strcmp($exceptionArray['404'], $status) == 0) {
				throw new Exception($status);
			}
			$productTrnArray = json_decode($status, true);
			foreach ($productTrnArray as $productTrn) {
				$jf_id = $productTrn['jf_id'];
				$mergeArray = [
					'to_product' => $toProductId,
					'from_product' => $fromProductId,
					'new_product' => $newProductData,
				];
				if ($productTrn['transaction_type'] == 'Balance') {
					$status = $mergeModel->deleteProductTrnById($productTrn['product_trn_id']);
					if (strcmp($status, $exceptionArray['500']) == 0) {
						throw new Exception($status);
					}
					continue;
				} elseif ($productTrn['transaction_type'] == 'Inward') {
					if ($productTrn['bill_number'] != '') {
						$purchaseBillStatus = $this->changePurchaseBillData($mergeArray, [
							'jf_id' => $jf_id,
						], 'productArray');
						if (strcmp($purchaseBillStatus, $exceptionArray['500']) == 0) {
							throw new Exception($purchaseBillStatus);
						}
						if (strcmp($purchaseBillStatus, $exceptionArray['404']) == 0) {
							goto salesReturnUpdate;
						}
					} else {
						salesReturnUpdate:
						$salesReturnStatus = $this->changeSalesReturnData($mergeArray, [
							'jf_id' => $jf_id,
						], 'productArray');
						if (strcmp($salesReturnStatus, $exceptionArray['500']) == 0) {
							throw new Exception($salesReturnStatus);
						}
					}
				} elseif ($productTrn['transaction_type'] == 'Outward') {
					if ($productTrn['invoice_number'] != '') {
						$billStatus = $this->changeSalesBillData($mergeArray, [
							'jf_id' => $jf_id,
						], 'productArray');
						if (strcmp($billStatus, $exceptionArray['500']) == 0) {
							throw new Exception($billStatus);
						}
					}
				}
			}
			$productIdUpdate['product_id'] = $toProductId;
			$status = $mergeModel->updateProductTrnByProductId($productIdUpdate, $fromProductId);
			if (strcmp($exceptionArray['500'], $status) == 0) {
				throw new Exception($status);
			}
			$status = $mergeModel->updateItemizeTrnByProductId($productIdUpdate, $fromProductId);
			if (strcmp($exceptionArray['500'], $status) == 0) {
				throw new Exception($status);
			}
			$status = $mergeModel->updateItemwiseCommissionByProductId($productIdUpdate, $fromProductId);
			if (strcmp($exceptionArray['500'], $status) == 0) {
				throw new Exception($status);
			}
			DB::commit();
			return $status;
		} catch (\Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}
	}

	/**
	 * @param mergeArray, mergeCondition, mergeParam
	 * @return query status
	 * Update Sales Bill Data using JfId
	 */
	public function changeSalesBillData($mergeArray, $mergeCondition, $param = 'productArray')
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
			if (strcmp($status, $exceptionArray['404']) == 0) {
				return $status;
			}
			$salesBillArray = json_decode($status, true);
			foreach ($salesBillArray as $salesBill) {
				$productArray = json_decode($salesBill['product_array'], true);
				$productArray['inventory'] = array_map(function ($pr) use ($mergeArray) {
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
				$updateStatus = $mergeModel->updateSalesBillBySaleId($updateArray, $salesBill['sale_id']);
				if (strcmp($updateStatus, $exceptionArray['500']) == 0) {
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
	public function changePurchaseBillData($mergeArray, $mergeCondition, $param = 'productArray')
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
			if (strcmp($status, $exceptionArray['404']) == 0) {
				return $status;
			}
			$purchaseBillArray = json_decode($status, true);
			foreach ($purchaseBillArray as $purchaseBill) {
				$productArray = json_decode($purchaseBill['product_array'], true);
				$productArray['inventory'] = array_map(function ($pr) use ($mergeArray) {
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
				$updateStatus = $mergeModel->updatePurchaseBillByPurchaseId($updateArray, $purchaseBill['purchase_id']);
				if (strcmp($updateStatus, $exceptionArray['500']) == 0) {
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
	public function changeSalesReturnData($mergeArray, $mergeCondition, $param = 'productArray')
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
			if (strcmp($status, $exceptionArray['404']) == 0) {
				return $status;
			}
			$salesReturnArray = json_decode($status, true);
			foreach ($salesReturnArray as $salesReturn) {
				$productArray = json_decode($salesReturn['product_array'], true);
				$productArray['inventory'] = array_map(function ($pr) use ($mergeArray) {
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
				$updateStatus = $mergeModel->updateSalesReturnById($updateArray, $salesReturn['sale_return_id']);
				if (strcmp($updateStatus, $exceptionArray['500']) == 0) {
					break;
				}
			}
			return $updateStatus;
		}
	}

	/**
	 * @param companyId
	 * @return status
	 */
	public function mergeLedgers($companyId)
	{
		// Step 1 assign Ledgers to Expenses

		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();

		goto Step3Migration;

		Step1Migration:

		$ledgerArray = new LedgerArray();
		$expenseGroupArray = $ledgerArray->expenseLedgerArray();

		DB::beginTransaction();
		try
		{
			$company = DB::connection($databaseName)->select("select
				city_id,
				state_abb
				from company_mst
				where deleted_at='0000-00-00 00:00:00'
				and company_id = '" . $companyId . "'");
			if (!count($company)) {
				throw new Exception('Invalid Company selection!');
			}
			$expenses = DB::connection($databaseName)->select("select
				expense_id,
				expense_name,
				expense_group_type,
				company_id
				from expense_type_mst
				where deleted_at='0000-00-00 00:00:00'
				and ledger_id = 0
				and company_id = '" . $companyId . "'");
			if (!count($expenses)) {
				throw new Exception('No expenses to be seeded!');
			}
			foreach ($expenses as $expense) {
				$ledger_name = $expense->expense_name;
				$state_abb = $company[0]->state_abb;
				$city_id = $company[0]->city_id;
				$ledger_group_id = isset($expenseGroupArray[$expense->expense_group_type]) ? $expenseGroupArray[$expense->expense_group_type] : 0;
				$status = DB::connection($databaseName)->statement("insert into ledger_mst
					(ledger_name, state_abb, city_id, ledger_group_id, created_at)
					values
					('$ledger_name', '$state_abb', '$city_id', '$ledger_group_id', '$mytime' );");
				if (!$status) {
					throw new Exception("Failed to insert Ledger Try again!");
				}
				$ledgerId = DB::connection($databaseName)->select('SELECT LAST_INSERT_ID() as ledger_id;');
				if (!count($status)) {
					throw new Exception("Insert ID not found!");
				}

				$result = DB::connection($databaseName)->statement("CREATE TABLE " . $ledgerId[0]->ledger_id . "_ledger_dtl (
					`" . $ledgerId[0]->ledger_id . "_id` int(11) NOT NULL AUTO_INCREMENT,
					`amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
					`amount_type` enum('credit','debit') NOT NULL DEFAULT 'credit',
					`entry_date` date NOT NULL DEFAULT '0000-00-00',
					`jf_id` int(11) NOT NULL,
					`balance_flag` enum('','opening','closing') NOT NULL DEFAULT '',
					`created_at` datetime NOT NULL,
					`updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					`deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					`ledger_id` int(11) NOT NULL,
					PRIMARY KEY (`" . $ledgerId[0]->ledger_id . "_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf16");

				if (!$result) {
					throw new Exception("Failed to create Ledger Table");
				}
				$status = DB::connection($databaseName)->statement("insert into " . $ledgerId[0]->ledger_id . "_ledger_dtl(amount, amount_type, entry_date, balance_flag, created_at) values(0, 'credit', '$mytime', 'opening', '$mytime')");
				if (!$status) {
					throw new Exception("Failed inserting Opening Balance!");
				}
				$status = DB::connection($databaseName)->statement("update expense_type_mst set `ledger_id`='" . $ledgerId[0]->ledger_id . "' where expense_id = '" . $expense->expense_id . "'");
				if (!$status) {
					throw new Exception("Failed to update Expense Entry!");
				}
			}
			return "200: OK";
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}

		Step2Migration:

		DB::beginTransaction();
		try
		{
			$salesBill = DB::connection($databaseName)->select("select
				sales_bill.sale_id,
				sales_bill.product_array,
				sales_bill.invoice_number,
				sales_bill.total,
				sales_bill.total_discounttype,
				sales_bill.total_discount,
				sales_bill.total_cgst_percentage,
				sales_bill.total_sgst_percentage,
				sales_bill.total_igst_percentage,
				sales_bill.extra_charge,
				sales_bill.tax,
				sales_bill.entry_date,
				sales_bill.jf_id
				from sales_bill
				JOIN sale_expense_dtl on sales_bill.sale_id = sale_expense_dtl.sale_id
				where sales_bill.company_id = '" . $companyId . "'
				and sales_bill.is_draft='no' and sales_bill.deleted_at = '0000-00-00 00:00:00' group by sales_bill.sale_id");
			// print_r($salesBill);
			if (!count($salesBill)) {
				throw new Exception("Sale Expenses not found!");
			}
			$saleTaxLedger = DB::connection($databaseName)->select("select
				ledger_id,
				ledger_name
				from ledger_mst
				where ledger_name in ('whole_sales','tax(output)')
				and company_id = '$companyId'
				and deleted_at = '0000-00-00 00:00:00'
				");

			$saleTaxLedgerArray = json_decode(json_encode($saleTaxLedger), true);

			$salesLedger = array_first($saleTaxLedgerArray, function ($key, $value) {
				return $value['ledger_name'] == 'whole_sales';
			});
			$taxLedger = array_first($saleTaxLedgerArray, function ($key, $value) {
				return $value['ledger_name'] == 'tax(output)';
			});
			foreach ($salesBill as $billData) {
				// Get the credit journals from jf_id
				// Get ledger Ids for Sales and tax
				// Need Expense Ledger Id to insert
				// Can remove expense JSON from query to use it here

				$jfId = $billData->jf_id;
				$saleId = $billData->sale_id;
				$saleJournals = DB::connection($databaseName)->select("select
					journal_id,
					jf_id,
					amount,
					amount_type,
					entry_date,
					journal_type,
					ledger_id
					from journal_dtl
					where jf_id = '$jfId'
					and deleted_at = '0000-00-00 00:00:00'
					and journal_type = 'sale' ");
				$totalJournals = count($saleJournals);
				if (!$totalJournals) {
					throw new Exception("No Journal Entries!");
				}
				$saleExpenses = DB::connection($databaseName)->select("select
					sale_expense_dtl.expense_name,
					sale_expense_dtl.expense_operation,
					sale_expense_dtl.expense_type,
					sale_expense_dtl.expense_value,
					sale_expense_dtl.expense_tax,
					sale_expense_dtl.expense_id,
					expense_type_mst.ledger_id
					from sale_expense_dtl
					join expense_type_mst on sale_expense_dtl.expense_id = expense_type_mst.expense_id
					where sale_expense_dtl.sale_id = '$saleId' ");
				// Get  Credit Journals and expense Entries
				$salesExpenseLedgerCheck = array_column(json_decode(json_encode($saleExpenses), true), 'ledger_id');
				$expenseCalcFlag = false;
				$considerArray = array(
					'sales_jv' => array(),
					'tax_jv' => array(),
					'other_jv' => array(),
				);
				for ($journalIter = 0; $journalIter < $totalJournals; $journalIter++) {
					if (in_array($saleJournals[$journalIter]->ledger_id, $salesExpenseLedgerCheck)) {
						$expenseCalcFlag = true;
						break;
					}
					switch ($saleJournals[$journalIter]->ledger_id) {
						case $salesLedger['ledger_id']:
						$considerArray['sales_jv'] = $saleJournals[$journalIter];
						break;

						case $taxLedger['ledger_id']:
						$considerArray['tax_jv'] = $saleJournals[$journalIter];
						break;

						default:
						$considerArray['other_jv'][] = $saleJournals[$journalIter];
						break;
					}
				}
				if ($expenseCalcFlag) {
					continue;
				}
				$total = (float) $billData->total;
				$tax = (float) $billData->tax;
				$productArray = json_decode($billData->product_array, true);
				if (!is_array($productArray)) {
					continue;
				}
				$invArray = $productArray['inventory'];

				$total_inv_amount = array_sum(array_column($invArray, 'amount'));
				$total_amt = $total_inv_amount + (float) $billData->extra_charge;
				$total_tax = (float) $billData->total_cgst_percentage + (float) $billData->total_sgst_percentage + (float) $billData->total_igst_percentage;
				$total_amt -= ($total_inv_amount * $total_tax / 100);
				// Expense Calculation begin
				$expense_journals = array();
				$batchString = '';
				$separator = '';
				$entry_date = $billData->entry_date;
				$saleLedgerEntry = DB::connection($databaseName)->select("select
					{$considerArray['sales_jv']->ledger_id}_id as entry_id,
					ledger_id
					from {$considerArray['sales_jv']->ledger_id}_ledger_dtl 
					where jf_id = '$jfId' and entry_date='$entry_date' order by {$considerArray['sales_jv']->ledger_id}_id desc limit 1");
				
				foreach ($saleExpenses as $expense) {
					if ($expense->expense_type == 'flat') {
						$expense_amt = abs(round($expense->expense_value, 2));
						$total -= $expense->expense_value;
						if ($expense->expense_operation == 'plus') {
							$amt_type = 'credit';
						} else {
							$amt_type = 'debit';
						}

						$batchString .= $separator . "('$jfId', '{$expense_amt}', '$amt_type', '$entry_date', 'sale', '{$expense->ledger_id}', '{$mytime}')";
					} else {
						$expense_amt = round($expense->expense_value * $total_amt / 100, 2);
						$total -= $expense_amt;
						$expense_amt = abs($expense_amt);
						$amt_type = $expense->expense_operation == 'plus' ? 'credit' : 'debit';
						$batchString .= $separator . "('$jfId', '{$expense_amt}', '$amt_type', '$entry_date', 'sale', '{$expense->ledger_id}', '{$mytime}')";
					}
					$separator = ", ";
					$opposite_ledger_id = $amt_type == 'credit' ? $saleLedgerEntry[0]->ledger_id : $considerArray['sales_jv']->ledger_id;

					$insertLedger = DB::connection($databaseName)->statement("insert into {$expense->ledger_id}_ledger_dtl (amount, amount_type, entry_date, jf_id, ledger_id, created_at) VALUES ('{$expense_amt}', '$amt_type', '$entry_date', '$jfId', '{$opposite_ledger_id}', '{$mytime}')");
					if(!$insertLedger){
						throw new Exception("Fail inserting expense Ledger Entries!");
						
					}
				}
				if ($batchString == '') {
					continue;
				}
				$batchInsert = DB::connection($databaseName)->statement("insert into journal_dtl (jf_id, amount, amount_type, entry_date, journal_type, ledger_id, created_at) VALUES $batchString");
				if ($batchInsert) {
					$updateSales = DB::connection($databaseName)->statement("update journal_dtl set amount = '$total' where journal_id = '" . $considerArray['sales_jv']->journal_id . "'");
					if(!$updateSales){
						throw new Exception("Failed to update Sales Journal!");
					}
					$updateSales = DB::connection($databaseName)->statement("update {$considerArray['sales_jv']->ledger_id}_ledger_dtl set amount = '$total' where {$considerArray['sales_jv']->ledger_id}_id = {$saleLedgerEntry[0]->entry_id}");
					if(!$updateSales){
						throw new Exception("Failed to update Sales ledger!");
					}
				} else {
					throw new Exception("Error Inserting Journals");
				}
			}
			
			return "200: OK";
			DB::commit();
			
		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}

		Step3Migration:

		DB::beginTransaction();
		try
		{
			$purchaseBill = DB::connection($databaseName)->select("select
				purchase_bill.purchase_id,
				purchase_bill.product_array,
				purchase_bill.bill_number,
				purchase_bill.total,
				purchase_bill.total_discounttype,
				purchase_bill.total_discount,
				purchase_bill.total_cgst_percentage,
				purchase_bill.total_sgst_percentage,
				purchase_bill.total_igst_percentage,
				purchase_bill.extra_charge,
				purchase_bill.tax,
				purchase_bill.entry_date,
				purchase_bill.jf_id
				from purchase_bill
				JOIN purchase_expense_dtl on purchase_bill.purchase_id = purchase_expense_dtl.purchase_id
				where purchase_bill.company_id = '" . $companyId . "'
				and purchase_bill.jf_id != 0
				and purchase_bill.deleted_at = '0000-00-00 00:00:00' group by purchase_bill.purchase_id");

			if (!count($purchaseBill)) {
				throw new Exception("Purchase Expenses not found!");
			}

			$purchaseTaxLedger = DB::connection($databaseName)->select("select
				ledger_id,
				ledger_name
				from ledger_mst
				where ledger_name in ('purchase_tax','tax(input)')
				and company_id = '$companyId'
				and deleted_at = '0000-00-00 00:00:00'
				");

			$purchaseTaxLedgerArray = json_decode(json_encode($purchaseTaxLedger), true);

			$purchaseLedger = array_first($purchaseTaxLedgerArray, function ($key, $value) {
				return $value['ledger_name'] == 'purchase_tax';
			});
			$taxLedger = array_first($purchaseTaxLedgerArray, function ($key, $value) {
				return $value['ledger_name'] == 'tax(input)';
			});
			foreach ($purchaseBill as $billData) {
				// Get the credit journals from jf_id
				// Get ledger Ids for Sales and tax
				// Need Expense Ledger Id to insert
				// Can remove expense JSON from query to use it here

				$jfId = $billData->jf_id;
				$purchaseId = $billData->purchase_id;
				$purchaseJournals = DB::connection($databaseName)->select("select
					journal_id,
					jf_id,
					amount,
					amount_type,
					entry_date,
					journal_type,
					ledger_id
					from journal_dtl
					where jf_id = '$jfId'
					and deleted_at = '0000-00-00 00:00:00'
					and journal_type = 'purchase' ");
				$totalJournals = count($purchaseJournals);
				if (!$totalJournals) {
					throw new Exception("No Journal Entries!");
				}

				$purchaseExpenses = DB::connection($databaseName)->select("select
					purchase_expense_dtl.expense_name,
					purchase_expense_dtl.expense_operation,
					purchase_expense_dtl.expense_type,
					purchase_expense_dtl.expense_value,
					purchase_expense_dtl.expense_tax,
					purchase_expense_dtl.expense_id,
					expense_type_mst.ledger_id
					from purchase_expense_dtl
					join expense_type_mst on purchase_expense_dtl.expense_id = expense_type_mst.expense_id
					where purchase_expense_dtl.purchase_id = '$purchaseId' ");
				// Get  Credit Journals and expense Entries
				$purchaseExpenseLedgerCheck = array_column(json_decode(json_encode($purchaseExpenses), true), 'ledger_id');
				$expenseCalcFlag = false;
				$considerArray = array(
					'purchase_jv' => array(),
					'tax_jv' => array(),
					'other_jv' => array(),
				);
				for ($journalIter = 0; $journalIter < $totalJournals; $journalIter++) {
					if (in_array($purchaseJournals[$journalIter]->ledger_id, $purchaseExpenseLedgerCheck)) {
						$expenseCalcFlag = true;
						break;
					}
					switch ($purchaseJournals[$journalIter]->ledger_id) {
						case $purchaseLedger['ledger_id']:
						$considerArray['purchase_jv'] = $purchaseJournals[$journalIter];
						break;

						case $taxLedger['ledger_id']:
						$considerArray['tax_jv'] = $purchaseJournals[$journalIter];
						break;

						default:
						$considerArray['other_jv'][] = $purchaseJournals[$journalIter];
						break;
					}
				}
				if ($expenseCalcFlag) {
					continue;
				}
				$total = (float) $billData->total;
				$tax = (float) $billData->tax;
				$productArray = json_decode($billData->product_array, true);
				if (!is_array($productArray)) {
					continue;
				}
				$invArray = $productArray['inventory'];

				$total_inv_amount = array_sum(array_column($invArray, 'amount'));
				$total_amt = $total_inv_amount + (float) $billData->extra_charge;
				$total_tax = (float) $billData->total_cgst_percentage + (float) $billData->total_sgst_percentage + (float) $billData->total_igst_percentage;
				$total_amt -= ($total_inv_amount * $total_tax / 100);
				// Expense Calculation begin
				$expense_journals = array();
				$batchString = '';
				$separator = '';
				$entry_date = $billData->entry_date;
				$saleLedgerEntry = DB::connection($databaseName)->select("select
					{$considerArray['purchase_jv']->ledger_id}_id as entry_id,
					ledger_id
					from {$considerArray['purchase_jv']->ledger_id}_ledger_dtl 
					where jf_id = '$jfId' and entry_date='$entry_date' order by {$considerArray['purchase_jv']->ledger_id}_id desc limit 1");
				
				foreach ($purchaseExpenses as $expense) {
					if ($expense->expense_type == 'flat') {
						$expense_amt = abs(round($expense->expense_value, 2));
						$total -= $expense->expense_value;
						if ($expense->expense_operation == 'plus') {
							$amt_type = 'debit';
						} else {
							$amt_type = 'credit';
						}

						$batchString .= $separator . "('$jfId', '{$expense_amt}', '$amt_type', '$entry_date', 'sale', '{$expense->ledger_id}', '{$mytime}')";
					} else {
						$expense_amt = round($expense->expense_value * $total_amt / 100, 2);
						$total -= $expense_amt;
						$expense_amt = abs($expense_amt);
						$amt_type = $expense->expense_operation == 'plus' ? 'debit' : 'credit';
						$batchString .= $separator . "('$jfId', '{$expense_amt}', '$amt_type', '$entry_date', 'sale', '{$expense->ledger_id}', '{$mytime}')";
					}
					$separator = ", ";
					$opposite_ledger_id = $amt_type == 'debit' ? $saleLedgerEntry[0]->ledger_id : $considerArray['purchase_jv']->ledger_id;

					$insertLedger = DB::connection($databaseName)->statement("insert into {$expense->ledger_id}_ledger_dtl (amount, amount_type, entry_date, jf_id, ledger_id, created_at) VALUES ('{$expense_amt}', '$amt_type', '$entry_date', '$jfId', '{$opposite_ledger_id}', '{$mytime}')");
					if(!$insertLedger){
						throw new Exception("Fail inserting expense Ledger Entries!");
						
					}
				}
				if ($batchString == '') {
					continue;
				}
				$batchInsert = DB::connection($databaseName)->statement("insert into journal_dtl (jf_id, amount, amount_type, entry_date, journal_type, ledger_id, created_at) VALUES $batchString");
				if ($batchInsert) {
					$updatePurchase = DB::connection($databaseName)->statement("update journal_dtl set amount = '$total' where journal_id = '" . $considerArray['purchase_jv']->journal_id . "'");
					if(!$updatePurchase){
						throw new Exception("Failed to update Purchase Journal!");
					}
					$updatePurchase = DB::connection($databaseName)->statement("update {$considerArray['purchase_jv']->ledger_id}_ledger_dtl set amount = '$total' where {$considerArray['purchase_jv']->ledger_id}_id = {$saleLedgerEntry[0]->entry_id}");
					if(!$updatePurchase){
						throw new Exception("Failed to update Purchase ledger!");
					}
				} else {
					throw new Exception("Error Inserting Journals");
				}
			}
			return "200: OK";
			DB::commit();

		} catch (Exception $e) {
			DB::rollback();
			return $e->getMessage();
		}

	}

	public function fixInventory($inventoryType)
	{
		$specialService = new SpecialUtilityService();
		switch ($inventoryType) {
			case 'sales':
				return $specialService->fixSaleInventory();
			break;
			case 'purchase':
				return $specialService->fixPurchaseInventory();
			break;
			case 'sales_return':
				return $specialService->fixSaleReturnInventory();
			break;
			case 'purchase_return':
				return $specialService->fixPurchaseReturnInventory();
			break;
			
			default:
				return '404: Not Found!';
			break;
		}
	}
}
