<?php
namespace ERP\Api\V1_0\Accounting\Ledgers\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Accounting\Ledgers\Entities\InventoryAffectedEnum;
use ERP\Core\Accounting\Ledgers\Entities\BalanceFlagEnum;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerTransformer
{
    /**
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$inventoryAffectedFlag=0;
		$balanceTypeFlag=0;
		$amountTypeFlag=0;
		//data get from body
		$ledgerName = $request->input('ledgerName'); 
		$alias = $request->input('alias'); 
		$inventoryAffected = $request->input('inventoryAffected'); 
		$address1 = $request->input('address1'); 
		$address2 = $request->input('address2'); 
		$contactNo = $request->input('contactNo'); 
		$emailId = $request->input('emailId'); 
		$pan = $request->input('pan'); 
		$tin = $request->input('tin'); 
		$gstNo = $request->input('gst'); 		
		$balanceFlag = $request->input('balanceFlag'); 		
		$amount = $request->input('amount'); 		
		$amountType = $request->input('amountType'); 		
		$stateAbb = $request->input('stateAbb'); 			
		$cityId = $request->input('cityId'); 			
		$ledgerGrpId = $request->input('ledgerGroupId');  
		$companyId = $request->input('companyId');  
		
		//trim an input
		$tLedgerName = trim($ledgerName);
		$tAlias = trim($alias);
		$tInventoryAffected = trim($inventoryAffected);
		$tAddress1 = trim($address1);
		$tAddress2 = trim($address2);
		$tContactNo = trim($contactNo);
		$tEmailId = trim($emailId);
		$tPan = trim($pan);
		$tTin = trim($tin);
		$tGstNo = trim($gstNo);
		$tBalanceFlag = trim($balanceFlag);
		$tAmount = trim($amount);
		$tAmountType = trim($amountType);
		$tStateAbb = trim($stateAbb);
		$tCityId = trim($cityId);
		$tLedgerGrpId = trim($ledgerGrpId);
		$tcompanyId = trim($companyId);
		if($tInventoryAffected!="")
		{
			$enumInventoryAffectedArray = array();
			$inventoryAffectedEnum = new InventoryAffectedEnum();
			$enumInventoryAffectedArray = $inventoryAffectedEnum->enumArrays();
			foreach ($enumInventoryAffectedArray as $key => $value)
			{
				if(strcmp($value,$tInventoryAffected)==0)
				{
					$inventoryAffectedFlag=1;
					break;
				}
				else
				{
					$inventoryAffectedFlag=2;
				}
			}
		}
		
		if($tBalanceFlag!="")
		{
			$enumBalanceFlagArray = array();
			$balanceFlagEnum = new BalanceFlagEnum();
			$enumBalanceFlagArray = $balanceFlagEnum->enumArrays();
			foreach ($enumBalanceFlagArray as $key => $value)
			{
				if(strcmp($value,$tBalanceFlag)==0)
				{
					$balanceTypeFlag=1;
					break;
				}
				else
				{
					$balanceTypeFlag=2;
				}
			}
		}
		if($tAmountType!=="")
		{
			//check enum type[amount-type]
			$enumAmountTypeArray = array();
			$amountTypeEnum = new AmountTypeEnum();
			$enumAmountTypeArray = $amountTypeEnum->enumArrays();
			foreach ($enumAmountTypeArray as $key => $value)
			{
				if(strcmp($value,$tAmountType)==0)
				{
					$amountTypeFlag=1;
					break;
				}
				else
				{
					$amountTypeFlag=2;
				}
			}
		}	
		if($inventoryAffectedFlag==2 || $balanceTypeFlag==2 || $amountTypeFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['ledger_name'] = $tLedgerName;
			$data['alias'] = $tAlias;
			$data['inventory_affected'] = $tInventoryAffected;
			$data['address1'] = $tAddress1;
			$data['address2'] = $tAddress2;
			$data['contact_no'] = $tContactNo;
			$data['email_id'] = $tEmailId;
			$data['pan'] = $tPan;
			$data['tin'] = $tTin;
			$data['gst'] = $tGstNo;
			$data['balance_flag'] = $tBalanceFlag;
			$data['amount'] = $tAmount;
			$data['amount_type'] = $tAmountType;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
			$data['ledger_group_id'] = $tLedgerGrpId;
			$data['company_id'] = $tcompanyId;
			return $data;
		}
	}
	
	//trim update data
	public function trimUpdateData()
	{
		$tLedgerArray = array();
		$ledgerValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$inventoryAffectedEnumArray = array();
		$inventoryAffectedFlag=0;
		for($asciiChar=0;$asciiChar<strlen($keyValue);$asciiChar++)
		{
			if(ord($keyValue[$asciiChar])<=90 && ord($keyValue[$asciiChar])>=65) 
			{
				$convertedValue1 = "_".chr(ord($keyValue[$asciiChar])+32);
				$convertedValue=$convertedValue.$convertedValue1;
			}
			else
			{
				$convertedValue=$convertedValue.$keyValue[$asciiChar];
			}
		}
		$ledgerValue = func_get_arg(1);
		for($data=0;$data<count($ledgerValue);$data++)
		{
			$tLedgerArray[$data]= array($convertedValue=> trim($ledgerValue));
			$inventoryAffectedEnumArray = array_keys($tLedgerArray[$data])[0];
		}
		$enumInventoryAffectedArray = array();
		$inventoryAffectedEnum = new InventoryAffectedEnum();
		$enumInventoryAffectedArray = $inventoryAffectedEnum->enumArrays();
		if(strcmp($inventoryAffectedEnumArray,'inventory_affected')==0)
		{
			foreach ($enumInventoryAffectedArray as $key => $value)
			{
				if(strcmp($tLedgerArray[0]['inventory_affected'],$value)==0)
				{
					$inventoryAffectedFlag=1;
					break;
				}
				else
				{
					$inventoryAffectedFlag=2;
				}
			}
		}
		
		if($inventoryAffectedFlag==2)
		{
			return "1";
		}
		else
		{
			return $tLedgerArray;
		}
	}
	//trim fromdate-todate data
	public function trimDateData(Request $request)
	{
		//get data from header
		$fromDate =$request->header('fromDate');
		$toDate =$request->header('toDate');
		
		//trim the data
		$tFromDate =  trim($fromDate);
		$tToDate = trim($toDate);
		
		//date format conversion
		$transformFromDate = Carbon\Carbon::createFromFormat('d-m-Y', $tFromDate)->format('Y-m-d');
		$transformToDate = Carbon\Carbon::createFromFormat('d-m-Y', $tToDate)->format('Y-m-d');
		
		//put date into an array
		$trimArray = array();
		$trimArray['fromDate'] = $transformFromDate;
		$trimArray['toDate'] = $transformToDate;
		return $trimArray;
	}
}