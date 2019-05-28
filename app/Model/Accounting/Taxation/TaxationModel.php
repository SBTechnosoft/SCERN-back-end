<?php
namespace ERP\Model\Accounting\Taxation;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use stdClass;
use ERP\Model\Accounting\ProfitLoss\ProfitLossModel;
use ERP\Model\Accounting\BalanceSheet\BalanceSheetModel;
use ERP\Model\Accounting\Bills\BillModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TaxationModel extends Model
{
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getSaleTaxData($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$dateString = '';
		$mytime = Carbon\Carbon::now();
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		//get saleTax from sales bill 
		DB::beginTransaction();	
		$saleTaxResult = DB::connection($databaseName)->select("select
		sale_id,
		product_array,
		invoice_number,
		total,
		total_discounttype,
		total_discount,
		extra_charge,
		tax,
		grand_total,
		advance,
		balance,
		sales_type,
		refund,
		entry_date,
		client_id,
		company_id,
		jf_id
		from sales_bill
		where deleted_at='0000-00-00 00:00:00' and 
		sales_type='whole_sales' and ".$dateString."
		company_id='".$companyId."' and is_draft='no' and is_salesorder='not'"); 
		DB::commit();

		if(count($saleTaxResult)!=0)
		{
			return json_encode($saleTaxResult);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getOutwardSupplies($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$dateString = '';
		$mytime = Carbon\Carbon::now();
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(sales_bill.entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		//get saleTax from sales bill 
		DB::beginTransaction();	
		$saleTaxResult = DB::connection($databaseName)->select("select
		sales_bill.sale_id,
		sales_bill.product_array,
		sales_bill.invoice_number,
		sales_bill.total,
		sales_bill.total_discounttype,
		sales_bill.total_discount,
		sales_bill.extra_charge,
		sales_bill.tax,
		sales_bill.grand_total,
		sales_bill.advance,
		sales_bill.balance,
		sales_bill.sales_type,
		sales_bill.refund,
		sales_bill.entry_date,
		sales_bill.client_id,
		sales_bill.company_id,
		sales_bill.jf_id,
		client_mst.client_name,
		client_mst.company_name,
		client_mst.gst as gstin,
		client_mst.state_abb as place_of_supply
		FROM sales_bill
		LEFT JOIN client_mst ON client_mst.client_id = sales_bill.client_id
		where sales_bill.deleted_at='0000-00-00 00:00:00' and 
		sales_bill.sales_type='whole_sales' and ".$dateString."
		sales_bill.company_id='".$companyId."' and sales_bill.is_draft='no' and sales_bill.is_salesorder='not'"); 
		DB::commit();

		if(count($saleTaxResult)!=0)
		{
			return json_encode($saleTaxResult);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getPurchaseTaxData($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$mytime = Carbon\Carbon::now();
		$dateString='';
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		//get purchaseTax from purchase bill 
		DB::beginTransaction();	
		$purchaseTaxResult = DB::connection($databaseName)->select("select
		purchase_id,
		vendor_id,
		product_array,
		bill_number,
		total,
		tax,
		grand_total,
		total_discounttype,
		total_discount,
		advance,
		bill_type,
		extra_charge,
		balance,
		transaction_type,
		transaction_date,
		entry_date,
		company_id,
		jf_id
		from purchase_bill
		where bill_type='purchase_bill' and ".$dateString."
		company_id='".$companyId."' and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		if(count($purchaseTaxResult)!=0)
		{
			return json_encode($purchaseTaxResult);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getInwardSupplies($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$mytime = Carbon\Carbon::now();
		$dateString='';
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(purchase_bill.entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		//get purchaseTax from purchase bill 
		DB::beginTransaction();	
		$purchaseTaxResult = DB::connection($databaseName)->select("select
		purchase_bill.purchase_id,
		purchase_bill.vendor_id,
		purchase_bill.product_array,
		purchase_bill.bill_number,
		purchase_bill.total,
		purchase_bill.tax,
		purchase_bill.grand_total,
		purchase_bill.total_discounttype,
		purchase_bill.total_discount,
		purchase_bill.advance,
		purchase_bill.bill_type,
		purchase_bill.extra_charge,
		purchase_bill.balance,
		purchase_bill.transaction_type,
		purchase_bill.transaction_date,
		purchase_bill.entry_date,
		purchase_bill.company_id,
		purchase_bill.jf_id,
		ledger_mst.ledger_name,
		ledger_mst.cgst as gstin,
		ledger_mst.state_abb as supplier_state
		from purchase_bill
		LEFT JOIN ledger_mst ON ledger_mst.ledger_id = purchase_bill.vendor_id
		where purchase_bill.bill_type='purchase_bill' and ".$dateString."
		purchase_bill.company_id='".$companyId."' and
		purchase_bill.deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		if(count($purchaseTaxResult)!=0)
		{
			return json_encode($purchaseTaxResult);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getPurchaseData($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$mytime = Carbon\Carbon::now();
		//date conversion
		//from-date conversion
		$splitedFromDate = explode("-",$headerData['fromdate'][0]);
		$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
		//from-date conversion
		$splitedToDate = explode("-",$headerData['todate'][0]);
		$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
		
		//get saleTax from purchase bill 
		DB::beginTransaction();	
		$purchaseTaxResult = DB::connection($databaseName)->select("select
		product_array,
		bill_number,
		total,
		tax,
		grand_total,
		transaction_type,
		transaction_date,
		client_name,
		company_id,
		jf_id
		from purchase_bill
		where deleted_at='0000-00-00 00:00:00' 
		and company_id='".$companyId."' and
		(transaction_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."')"); 
		DB::commit();
		
		if(count($purchaseTaxResult)!=0)
		{
			return json_encode($purchaseTaxResult);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * get income-expense data
	 * returns the array-data/exception message
	*/
	public function getIncomeExpenseData($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$mytime = Carbon\Carbon::now();
		$dateString='';
		$transformFromDate='';
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		//get purchase data from purchase-bill
		DB::beginTransaction();	
		$purchaseResult = DB::connection($databaseName)->select("select
		SUM(grand_total) as grand_total,
		purchase_id,
		vendor_id,
		product_array,
		bill_number,
		total,
		tax,
		entry_date,
		company_id,
		jf_id
		from purchase_bill
		where bill_type='purchase_bill' and ".$dateString."
		company_id='".$companyId."' and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();

		//get sales data from sales-bill
		DB::beginTransaction();	
		$saleResult = DB::connection($databaseName)->select("select
		SUM(grand_total) as grand_total,
		sale_id,
		product_array,
		invoice_number,
		total,
		tax,
		entry_date,
		company_id,
		jf_id
		from sales_bill
		where sales_type='whole_sales' and ".$dateString."
		company_id='".$companyId."' and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		if(count($purchaseResult)!=0 && count($saleResult)!=0)
		{
			$tradingAmount = $purchaseResult[0]->grand_total - $saleResult[0]->grand_total;
		}
		else if(count($purchaseResult)==0 && count($saleResult)==0)
		{
			$tradingAmount =0;
		}
		else if(count($purchaseResult)==0)
		{
			$tradingAmount = - $saleResult[0]->grand_total;
		}
		else if(count($saleResult)==0)
		{
			$tradingAmount =  $purchaseResult[0]->grand_total;
		}
		else
		{
			$tradingAmount = 0;
		}
		$profitLoassModal = new ProfitLossModel();
		$profitLossData = $profitLoassModal->getProfitLossDataForInEx($transformFromDate,$transformToDate,$companyId);

		$balancesheetModal = new BalanceSheetModel();
		$balancesheetData = $balancesheetModal->getBalanceSheetDataOfInEx($transformFromDate,$transformToDate,$companyId);
		$profitLossArray = array();
		$profitLossArray['tradingAmount'] = $tradingAmount;
		$profitLossArray['profitLossAmount'] = $profitLossData;
		$profitLossArray['balancesheetAmount'] = $balancesheetData;
		return json_encode($profitLossArray);
	}

	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getStockDetailData($companyId,$headerData)
	{

		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$mytime = Carbon\Carbon::now();
		$dateString='';
		$transformFromDate='';
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		$openingBalance = array();
		//get opening balance
		$openingBalance = $this->getOpeningBalance($transformFromDate,$companyId);
		//get purchase data from purchase bill 
		DB::beginTransaction();	
		$purchaseResult = DB::connection($databaseName)->select("select
		purchase_id,
		vendor_id,
		product_array,
		bill_number,
		total,
		tax,
		grand_total,
		total_discounttype,
		total_discount,
		advance,
		bill_type,
		extra_charge,
		balance,
		transaction_type,
		transaction_date,
		entry_date,
		company_id,
		jf_id
		from purchase_bill
		where bill_type='purchase_bill' and ".$dateString."
		company_id='".$companyId."' and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		//get sales data from sale bill 
		DB::beginTransaction();	
		$saleResult = DB::connection($databaseName)->select("select
		sale_id,
		product_array,
		invoice_number,
		total,
		total_discounttype,
		total_discount,
		extra_charge,
		tax,
		grand_total,
		advance,
		balance,
		sales_type,
		refund,
		entry_date,
		client_id,
		company_id,
		jf_id
		from sales_bill
		where deleted_at='0000-00-00 00:00:00' and 
		sales_type='whole_sales' and ".$dateString."
		company_id='".$companyId."' and is_draft='no' and is_salesorder='not'"); 
		DB::commit();
		$calculationPurchaseResult = array();
		$calculationSaleResult = array();
		if(count($purchaseResult)!=0)
		{
			$calculationPurchaseResult = $this->calculationOfQty($purchaseResult);
		}
		if(count($saleResult)!=0)
		{
			$calculationSaleResult = $this->calculationOfQty($saleResult);
		}
		$openingBalanceCount = count($openingBalance);
		$stockArray = array();
		//loop for opening balance data(comparing with sale-purchase data)
		for($openingBalanceArray=0;$openingBalanceArray<$openingBalanceCount;$openingBalanceArray++)
		{
			$stockArray[$openingBalanceArray]['openingQty'] = $openingBalance[$openingBalanceArray]['qty'];	
			$stockArray[$openingBalanceArray]['openingPrice'] = $openingBalance[$openingBalanceArray]['price'];
			$stockArray[$openingBalanceArray]['productId'] = $openingBalance[$openingBalanceArray]['productId'];
			if(count($calculationPurchaseResult)!=0)
			{
				$calPurchaseResult = array_search($openingBalance[$openingBalanceArray]['productId'], array_column($calculationPurchaseResult, 'productId'));
				if($calPurchaseResult!='')
				{
					$stockArray[$openingBalanceArray]['purchaseQty'] = $calculationPurchaseResult[$calPurchaseResult]['qty'];	
					$stockArray[$openingBalanceArray]['purchasePrice'] = $calculationPurchaseResult[$calPurchaseResult]['price'];
					array_splice($calculationPurchaseResult,$calPurchaseResult,1);
				}
				else
				{
					$stockArray[$openingBalanceArray]['purchaseQty'] = 0;	
					$stockArray[$openingBalanceArray]['purchasePrice'] = 0;	
				}
			}
			else
			{
				$stockArray[$openingBalanceArray]['purchaseQty'] = 0;	
				$stockArray[$openingBalanceArray]['purchasePrice'] = 0;	
			}
			if(count($calculationSaleResult)!=0)
			{
				$calSaleResult = array_search($openingBalance[$openingBalanceArray]['productId'], array_column($calculationSaleResult, 'productId'));
				if($calSaleResult!='')
				{
					$stockArray[$openingBalanceArray]['saleQty'] = $calculationSaleResult[$calSaleResult]['qty'];	
					$stockArray[$openingBalanceArray]['salePrice'] = $calculationSaleResult[$calSaleResult]['price'];
					array_splice($calculationSaleResult,$calSaleResult,1);
				}
				else
				{
					$stockArray[$openingBalanceArray]['saleQty'] = 0;	
					$stockArray[$openingBalanceArray]['salePrice'] = 0;
				}	
			}	
			else
			{
				$stockArray[$openingBalanceArray]['saleQty'] = 0;	
				$stockArray[$openingBalanceArray]['salePrice'] = 0;
			}	
		}
		

		$purchaseResultCount = count($calculationPurchaseResult);
		//loop for purchase-data(comparing with sale-data)
		for($purchaseResultArray=0;$purchaseResultArray<$purchaseResultCount;$purchaseResultArray++)
		{
			
			$stockArray[$purchaseResultArray+$openingBalanceCount]['openingQty'] = 0;	
			$stockArray[$purchaseResultArray+$openingBalanceCount]['openingPrice'] = 0;
			$stockArray[$purchaseResultArray+$openingBalanceCount]['purchaseQty']=$calculationPurchaseResult[$purchaseResultArray]['qty'];
			$stockArray[$purchaseResultArray+$openingBalanceCount]['purchasePrice'] = $calculationPurchaseResult[$purchaseResultArray]['price'];
			$stockArray[$purchaseResultArray+$openingBalanceCount]['productId'] = $calculationPurchaseResult[$purchaseResultArray]['productId'];
			if(count($calculationSaleResult)!=0)
			{
				$calSaleResult = array_search($calculationPurchaseResult[$purchaseResultArray]['productId'], array_column($calculationSaleResult, 'productId'));
				if($calSaleResult!='' || $calSaleResult==0)
				{
					$stockArray[$purchaseResultArray+$openingBalanceCount]['saleQty'] = $calculationSaleResult[$calSaleResult]['qty'];	
					$stockArray[$purchaseResultArray+$openingBalanceCount]['salePrice'] = $calculationSaleResult[$calSaleResult]['price'];
					array_splice($calculationSaleResult,$calSaleResult,1);
				}
				else
				{
					$stockArray[$purchaseResultArray+$openingBalanceCount]['saleQty'] = 0;	
					$stockArray[$purchaseResultArray+$openingBalanceCount]['salePrice'] = 0;
				}	
			}	
			else
			{
				$stockArray[$purchaseResultArray+$openingBalanceCount]['saleQty'] = 0;	
				$stockArray[$purchaseResultArray+$openingBalanceCount]['salePrice'] = 0;
			}
		}

		$saleResultCount = count($calculationSaleResult);
		$totalNumber = $purchaseResultCount+$openingBalanceCount-2;
		//loop for sales-data
		for($saleResultArray=0;$saleResultArray<$saleResultCount;$saleResultArray++)
		{
			$stockArray[$saleResultArray+$totalNumber]['openingQty'] = 0;	
			$stockArray[$saleResultArray+$totalNumber]['openingPrice'] = 0;
			$stockArray[$saleResultArray+$totalNumber]['purchaseQty']=0;
			$stockArray[$saleResultArray+$totalNumber]['purchasePrice'] = 0;
			$stockArray[$saleResultArray+$totalNumber]['saleQty']=$calculationSaleResult[$saleResultArray]['qty'];
			$stockArray[$saleResultArray+$totalNumber]['salePrice'] = $calculationSaleResult[$saleResultArray]['price'];
			$stockArray[$saleResultArray+$totalNumber]['productId'] = $calculationSaleResult[$saleResultArray]['productId'];
		}


		$stockCount = count($stockArray);
		for($stockDataArray=0;$stockDataArray<$stockCount;$stockDataArray++)
		{
			if($stockArray[$stockDataArray]['productId'] != '')
			{

				//get hsn-code of product
				DB::beginTransaction();	
				$stockResult = DB::connection($databaseName)->select("select 
				product_id,
				product_name,
				hsn,
				created_at,
				updated_at,
				deleted_at,
				product_category_id,
				product_group_id,
				branch_id,
				company_id			
				from product_mst 
				where deleted_at='0000-00-00 00:00:00' and 
				product_id=".$stockArray[$stockDataArray]['productId']); 
				DB::commit();
				if(count($stockResult)!=0)
				{
					$stockArray[$stockDataArray]['hsn'] = $stockResult[0]->hsn;
					$stockArray[$stockDataArray]['productName'] = $stockResult[0]->product_name;
				}
				else
				{
					$stockArray[$stockDataArray]['hsn'] ="";
					$stockArray[$stockDataArray]['productName'] ="";
				}
			}
		}

		return json_encode($stockArray);
	}
	
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getOpeningBalance($transformFromDate,$companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$splitedFromDate = explode("-",$transformFromDate);
		
		// $finantialDate = $constantDatabase->constantAccountingDate();
		$fromDate = $splitedFromDate[0]."-04-01";
		$toDate = $transformFromDate;
		// $toDate = $transformToDate;
		$databaseName = $constantDatabase->constantDatabase();
		//get purchase data from purchase-bill 
		DB::beginTransaction();	
		$purchaseResult = DB::connection($databaseName)->select("select
		purchase_id,
		product_array,
		company_id,
		jf_id
		from purchase_bill
		where bill_type='purchase_bill' and 
		entry_date>='".$fromDate."' and entry_date<='".$toDate."' and
		company_id='".$companyId."' and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		
		//get sales data from sale-bill 
		DB::beginTransaction();	
		$saleResult = DB::connection($databaseName)->select("select
		sale_id,
		product_array,
		invoice_number,
		company_id,
		jf_id
		from sales_bill
		where deleted_at='0000-00-00 00:00:00' and 
		sales_type='whole_sales' and
		entry_date>='".$fromDate."' and entry_date<='".$toDate."' and
		company_id='".$companyId."' and is_draft='no' and is_salesorder='not'"); 
		DB::commit();
		$calculationPurchaseResult = array();
		$calculationSaleResult = array();
		if(count($purchaseResult)!=0)
		{
			$calculationPurchaseResult = $this->calculationOfQty($purchaseResult);
		}
		if(count($saleResult)!=0)
		{
			$calculationSaleResult = $this->calculationOfQty($saleResult);
		}
		// $calculationSaleResult[0]['productId'] = 1242;
			$intersectArray = array();
			$data=0;
			if(count($purchaseResult)!=0 && count($saleResult)!=0)
			{
				foreach($calculationPurchaseResult as $key=>$value)
				{
					$result = array_search($calculationPurchaseResult[$key]['productId'], array_column($calculationSaleResult, 'productId'));
					if(count($intersectArray)==0)
					{
						if($result=='')
						{
							$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
							$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty'];
							$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price'];
							// unset($calculationPurchaseResult[$key]);
							$data++;
						}
						else
						{
							$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
							$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty']-$calculationSaleResult[$result]['qty'];
							$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price']-$calculationSaleResult[$result]['price'];
							// unset($calculationPurchaseResult[$key]);
							array_splice($calculationSaleResult,$result,1);
							$data++;
						}
					}
					else if($result=='')
					{
						if(array_key_exists('0',$calculationSaleResult))
						{
							if($calculationPurchaseResult[$key]['productId']!=$calculationSaleResult[0]['productId'])
							{
								$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
								$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty'];
								$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price'];
								// unset($calculationPurchaseResult[$key]);
								$data++;
							}
							else
							{
								$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
								$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty']-$calculationSaleResult[$result]['qty'];
								$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price']-$calculationSaleResult[$result]['price'];
								// unset($calculationPurchaseResult[$key]);
								array_splice($calculationSaleResult,$result,1);
								$data++;
							}
						}
						else
						{
							$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
							$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty'];
							$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price'];
							// unset($calculationPurchaseResult[$key]);
							$data++;
						}
					}
					else
					{
						$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
						$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty']-$calculationSaleResult[$result]['qty'];
						$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price']-$calculationSaleResult[$result]['price'];
						// unset($calculationPurchaseResult[$key]);
						array_splice($calculationSaleResult,$result,1);
						$data++;
					}
				}
			}
			$saleCount = count($calculationSaleResult);

			if($saleCount!=0 && count($purchaseResult)==0)
			{
				foreach($calculationSaleResult as $key=>$value)
				{
					$intersectArray[$data]['productId']=$calculationSaleResult[$key]['productId'];
					$intersectArray[$data]['qty']=$calculationSaleResult[$key]['qty'];
					$intersectArray[$data]['price']=$calculationSaleResult[$key]['price'];
					$data++;
				}
			}
			$purchaseCount = count($calculationPurchaseResult);
			if($purchaseCount!=0 && count($saleResult)==0)
			{
				foreach($calculationPurchaseResult as $key=>$value)
				{
					$intersectArray[$data]['productId']=$calculationPurchaseResult[$key]['productId'];
					$intersectArray[$data]['qty']=$calculationPurchaseResult[$key]['qty'];
					$intersectArray[$data]['price']=$calculationPurchaseResult[$key]['price'];
					$data++;
				}
			}
		return $intersectArray;
	}
	
	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function calculationOfQty($result)
	{
		$mainArray = array();
		$outerCount = count($result);
		$data=0;
		for($dataArray=0;$dataArray<$outerCount;$dataArray++)
		{
			$inventoryArray = json_decode($result[$dataArray]->product_array)->inventory;
			$inventoryCount = count(json_decode($result[$dataArray]->product_array)->inventory);
			for($inventoryDataArray=0;$inventoryDataArray<$inventoryCount;$inventoryDataArray++)
			{
				if(count($mainArray)==0)
				{
					$mainArray[$data]['productId'] = $inventoryArray[$inventoryDataArray]->productId;
					$mainArray[$data]['qty'] = $inventoryArray[$inventoryDataArray]->qty;
					$mainArray[$data]['price'] = $inventoryArray[$inventoryDataArray]->price*$inventoryArray[$inventoryDataArray]->qty;
					$data++;
				}
				else
				{
					$key = array_search($inventoryArray[$inventoryDataArray]->productId, array_column($mainArray, 'productId'));
					if($key=='' && $inventoryArray[$inventoryDataArray]->productId!=$mainArray[0]['productId'])
					{
						$mainArray[$data]['productId'] = $inventoryArray[$inventoryDataArray]->productId;
						$mainArray[$data]['qty'] = $inventoryArray[$inventoryDataArray]->qty;
						$mainArray[$data]['price'] = $inventoryArray[$inventoryDataArray]->price*$inventoryArray[$inventoryDataArray]->qty;
						$data++;
					}
					else
					{
						$mainArray[$data]['productId'] = $inventoryArray[$inventoryDataArray]->productId;
						$mainArray[$data]['qty'] = $mainArray[$key]['qty']+$inventoryArray[$inventoryDataArray]->qty;
						$mainArray[$data]['price'] = ($mainArray[$key]['qty']*$mainArray[$key]['price'])+($inventoryArray[$inventoryDataArray]->qty*$inventoryArray[$inventoryDataArray]->price);
					}
				}
			}
		}
		return $mainArray;
	}

	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getGstr2Data($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$dateString='';
		$transformFromDate='';
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		$billModel = new BillModel();
		$billData = $billModel->getFromToDateCompanyData($transformFromDate,$transformToDate,$companyId);
		$billImpsData = $billModel->getImpsData($transformFromDate,$transformToDate,$companyId);
		$gstr2Array = array();
		$gstr2Array['b2b'] = json_decode($billData);
		$gstr2Array['imps'] = json_decode($billImpsData);
		return json_encode($gstr2Array);
	}

	/**
	 * get data
	 * returns the array-data/exception message
	*/
	public function getGstr3Data($companyId,$headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		$dateString='';
		$transformFromDate='';
		if(array_key_exists('fromdate',$headerData) && array_key_exists('todate',$headerData))
		{
			//date conversion
			//from-date conversion
			$splitedFromDate = explode("-",$headerData['fromdate'][0]);
			$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
			//to-date conversion
			$splitedToDate = explode("-",$headerData['todate'][0]);
			$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
			$dateString = "(entry_date BETWEEN '".$transformFromDate."' AND '".$transformToDate."') and";
		}
		$billModel = new BillModel();
		$billData = $billModel->getFromToDateCompanyData($transformFromDate,$transformToDate,$companyId);
		$billImpsData = $billModel->getImpsData($transformFromDate,$transformToDate,$companyId);
		$gstr3Array = array();
		$gstr3Array['gstr1Invoice'] = json_decode($billData);
		// $gstr2Array['imps'] = json_decode($billImpsData);
		return json_encode($gstr3Array);
	}
}
