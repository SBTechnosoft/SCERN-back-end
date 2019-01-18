<?php
namespace ERP\Model\Accounting\CashFlow;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CashFlowModel extends Model
{
	protected $table = 'cash_flow_dtl';
	
	/**
	 * get data as per given companyId 
	 * returns the array-data/exception message
	*/
	public function getCashFlowData($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//truncate table cash-flow
		DB::beginTransaction();	
		$truncateTable = DB::connection($databaseName)->statement("truncate table cash_flow_dtl"); 
		DB::commit();

		$mytime = Carbon\Carbon::now();
		//get ledgerId from ledger 
		DB::beginTransaction();	
		$ledgerResult = DB::connection($databaseName)->select("select
		ledger_id
		from ledger_mst
		where company_id='".$companyId."' and
		(ledger_name = 'cash' OR ledger_name = 'bank') and 
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
		if($dateTime > $toDate)
		{
			$toDate = $dateTime;
		}
		for($ledgerData=0;$ledgerData<count($ledgerResult);$ledgerData++)
		{
			$flag=0;
			$balanceType="";
			// get amount,amount_type from particular ledgerId_ledger_dtl
			DB::beginTransaction();	
			$ledgerAmountResult = DB::connection($databaseName)->select("select
			amount,
			amount_type,
			ledger_id,
			entry_date
			from ".$ledgerResult[$ledgerData]->ledger_id."_ledger_dtl
			where deleted_at='0000-00-00 00:00:00' and
			entry_date BETWEEN '".$fromDate."' AND '".$toDate."'
			"); 
			DB::commit();
			for($arrayData=0;$arrayData<count($ledgerAmountResult);$arrayData++)
			{
				// insert amount,amount_type in cash-flow
				DB::beginTransaction();	
				$profitLossResult = DB::connection($databaseName)->statement("insert into cash_flow_dtl(
				amount,
				amount_type,
				entry_date,
				ledger_id,
				created_at)
				values('".$ledgerAmountResult[$arrayData]->amount."','".$ledgerAmountResult[$arrayData]->amount_type."','".$ledgerAmountResult[$arrayData]->entry_date."','".$ledgerAmountResult[$arrayData]->ledger_id."','".$mytime."')");
				DB::commit();
			}
		}
		
		//get cash-flow data
		DB::beginTransaction();	
		$cashFlowResult = DB::connection($databaseName)->select("select 
		cash_flow_id,
		amount,
		amount_type,
		entry_date,
		ledger_id,
		created_at,
		updated_at
		from cash_flow_dtl
		where deleted_at='0000-00-00 00:00:00'
		order by entry_date");
		DB::commit();
		if(count($cashFlowResult)==0)
		{
			$exceptionArray['404'];
		}
		else
		{
			$encodedData = json_encode($cashFlowResult);
			return $encodedData;
		}
	}
}
