<?php
namespace ERP\Model\Accounting\ProfitLoss;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfitLossModel extends Model
{
	protected $table = 'profit_loss_dtl';
	
	/**
	 * get data as per given companyId 
	 * returns the array-data/exception message
	*/
	public function getProfitLossData($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//truncate table profit-loss
		DB::beginTransaction();	
		$truncateTable = DB::connection($databaseName)->statement("truncate table profit_loss_dtl"); 
		DB::commit();
		
		$mytime = Carbon\Carbon::now();
		//get ledgerId from ledger 
		DB::beginTransaction();	
		$ledgerResult = DB::connection($databaseName)->select("select
		ledger_id
		from ledger_mst
		where company_id='".$companyId."' and 
		(ledger_group_id='16' OR
		ledger_group_id='17' OR
		ledger_group_id='19' OR
		ledger_group_id='20' OR
		ledger_group_id='26' OR
		ledger_group_id='28') and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		
		if(count($ledgerResult)==0)
		{
			return $exceptionArray['404'];
		}
		
		$currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');
		$dateTime = $mytime->toDateTimeString();
		$yearStartDate = $mytime->year.'-04-01 00:00:00';
		if($dateTime >= $yearStartDate)
		{
			$toYear = $mytime->year+1;
			$fromDate = $mytime->year.'-04-01 00:00:00';
			$toDate = $toYear.'-03-31 00:00:00';
		}
		else
		{
			$fromYear = $mytime->year-1;
			$fromDate = $fromYear.'-04-01 00:00:00';
			$toDate = $mytime->year.'-03-31 00:00:00';
		}
		for($ledgerData=0;$ledgerData<count($ledgerResult);$ledgerData++)
		{
			$flag=0;
			$balanceType="";
			
			// get amount,amount_type from particular ledgerId_ledger_dtl
			DB::beginTransaction();	
			$ledgerAmountResult = DB::connection($databaseName)->select("select
			amount,
			amount_type
			from ".$ledgerResult[$ledgerData]->ledger_id."_ledger_dtl
			where deleted_at='0000-00-00 00:00:00' and 
			entry_date BETWEEN '".$fromDate."' AND '".$toDate."'"); 
			DB::commit();
			$creditTotal=0;
			$debitTotal=0;
			for($ledgerAmountData=0;$ledgerAmountData<count($ledgerAmountResult);$ledgerAmountData++)
			{
				if(strcmp($ledgerAmountResult[$ledgerAmountData]->amount_type,"credit")==0)
				{
					$creditTotal = $creditTotal+$ledgerAmountResult[$ledgerAmountData]->amount;
				}
				else
				{
					$debitTotal = $debitTotal+$ledgerAmountResult[$ledgerAmountData]->amount;
				}
			}
			
			if($creditTotal>$debitTotal)
			{
				$totalBalance = $creditTotal-$debitTotal;
				$balanceType = "credit";
			}
			else if($creditTotal<$debitTotal)
			{
				$totalBalance = $debitTotal-$creditTotal;
				$balanceType = "debit";
			}
			else
			{
				$flag=1;
			}
			if($flag==0)
			{
				// insert amount,amount_type in profit-loss
				DB::beginTransaction();	
				$profitLossResult = DB::connection($databaseName)->statement("insert into profit_loss_dtl(
				amount,
				amount_type,
				ledger_id,
				created_at)
				values('".$totalBalance."','".$balanceType."','".$ledgerResult[$ledgerData]->ledger_id."','".$mytime."')");
				DB::commit();
			}
		}
		//get profit-loss data
		DB::beginTransaction();	
		$profitLossResult = DB::connection($databaseName)->select("select 
		profit_loss_id,
		amount,
		amount_type,
		ledger_id,
		created_at,
		updated_at
		from profit_loss_dtl
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		if(count($profitLossResult)==0)
		{
			$exceptionArray['404'];
		}
		else
		{
			$encodedData = json_encode($profitLossResult);
			return $encodedData;
		}
	}

	/**
	 * get data as per given companyId,fromdate-todate 
	 * returns the array-data/exception message
	*/
	public function getProfitLossDataForInEx($fromDate,$toDate,$companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//truncate table profit-loss
		// DB::beginTransaction();	
		// $truncateTable = DB::connection($databaseName)->statement("truncate table profit_loss_dtl"); 
		// DB::commit();
		
		$mytime = Carbon\Carbon::now();
		//get ledgerId from ledger 
		DB::beginTransaction();	
		$ledgerResult = DB::connection($databaseName)->select("select
		ledger_id
		from ledger_mst
		where company_id='".$companyId."' and 
		(ledger_group_id='16' OR
		ledger_group_id='17' OR
		ledger_group_id='19' OR
		ledger_group_id='20' OR
		ledger_group_id='26' OR
		ledger_group_id='28') and
		deleted_at='0000-00-00 00:00:00'"); 
		DB::commit();
		
		if(count($ledgerResult)==0)
		{
			return $exceptionArray['404'];
		}
		
		// $currentDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $mytime)->format('Y-m-d');
		// $dateTime = $mytime->toDateTimeString();
		// $yearStartDate = $mytime->year.'-04-01 00:00:00';
		// if($dateTime >= $yearStartDate)
		// {
		// 	$toYear = $mytime->year+1;
		// 	$fromDate = $mytime->year.'-04-01 00:00:00';
		// 	$toDate = $toYear.'-03-31 00:00:00';
		// }
		// else
		// {
		// 	$fromYear = $mytime->year-1;
		// 	$fromDate = $fromYear.'-04-01 00:00:00';
		// 	$toDate = $mytime->year.'-03-31 00:00:00';
		// }
		$creditAmount = 0;
		$debitAmount = 0;
		for($ledgerData=0;$ledgerData<count($ledgerResult);$ledgerData++)
		{
			$flag=0;
			$balanceType="";
			
			// get amount,amount_type from particular ledgerId_ledger_dtl
			DB::beginTransaction();	
			$ledgerAmountResult = DB::connection($databaseName)->select("select
			amount,
			amount_type
			from ".$ledgerResult[$ledgerData]->ledger_id."_ledger_dtl
			where deleted_at='0000-00-00 00:00:00' and 
			entry_date BETWEEN '".$fromDate."' AND '".$toDate."'"); 
			DB::commit();
			$creditTotal=0;
			$debitTotal=0;
			for($ledgerAmountData=0;$ledgerAmountData<count($ledgerAmountResult);$ledgerAmountData++)
			{
				if(strcmp($ledgerAmountResult[$ledgerAmountData]->amount_type,"credit")==0)
				{
					$creditTotal = $creditTotal+$ledgerAmountResult[$ledgerAmountData]->amount;
				}
				else
				{
					$debitTotal = $debitTotal+$ledgerAmountResult[$ledgerAmountData]->amount;
				}
			}
			
			if($creditTotal>$debitTotal)
			{
				$totalBalance = $creditTotal-$debitTotal;
				$balanceType = "credit";
			}
			else if($creditTotal<$debitTotal)
			{
				$totalBalance = $debitTotal-$creditTotal;
				$balanceType = "debit";
			}
			else
			{
				$flag=1;
			}
			if($flag==0)
			{
				if(strcmp($balanceType,"credit")==0)
				{
					$creditAmount = $creditAmount +$totalBalance;
				}
				else if(strcmp($balanceType,"debit")==0)
				{
					$debitAmount = $debitAmount + $totalBalance;  
				}
				// insert amount,amount_type in profit-loss
				// DB::beginTransaction();	
				// $profitLossResult = DB::connection($databaseName)->statement("insert into profit_loss_dtl(
				// amount,
				// amount_type,
				// ledger_id)
				// values('".$totalBalance."','".$balanceType."','".$ledgerResult[$ledgerData]->ledger_id."')");
				// DB::commit();
			}
		}
		$profitLossAmount =  $creditAmount - $debitAmount;
		return $profitLossAmount;
		//get profit-loss data
		// DB::beginTransaction();	
		// $profitLossResult = DB::connection($databaseName)->select("select 
		// profit_loss_id,
		// amount,
		// amount_type,
		// ledger_id,
		// created_at,
		// updated_at
		// from profit_loss_dtl
		// where deleted_at='0000-00-00 00:00:00'");
		// DB::commit();
		
		// if(count($profitLossResult)==0)
		// {
		// 	$exceptionArray['404'];
		// }
		// else
		// {
		// 	$encodedData = json_encode($profitLossResult);
		// 	return $encodedData;
		// }
	}
}
