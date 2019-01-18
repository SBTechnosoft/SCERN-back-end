<?php
namespace ERP\Api\V1_0\Accounting\Journals\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Exceptions\ExceptionMessage;
use Carbon;
use ERP\Core\Accounting\Journals\Entities\AmountTypeEnum;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JournalTransformer extends LedgerModel
{
    /**
	 * trim request data for insertion	
     * @param 
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$amountTypeFlag=0;
		$requestArray = array();
		$exceptionArray = array();
		$numberOfArray = count($request->input()['data']);
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//data get from body and trim an input
		$jfId = trim($request->input()['jfId']); 
		$entryDate = trim($request->input()['entryDate']); 
		$companyId = trim($request->input()['companyId']); 
		if(array_key_exists("type",$request->header()))
		{
			if(strcmp($request->header()['type'][0],$constantArray['sales'])==0)
			{
				$journalType = $constantArray['saleType'];
			}
			else if(strcmp($request->header()['type'][0],$constantArray['purchase'])==0)
			{
				$journalType = $constantArray['purchaseType'];
			}
			else if(strcmp($request->header()['type'][0],$constantArray['paymentType'])==0)
			{
				$journalType = $constantArray['paymentType'];
			}
			else
			{
				$journalType = $constantArray['receiptType'];
			}
		}
		else
		{
			$journalType = $constantArray['specialJournalType'];
		}
		
		// entry date conversion
		$splitedDate = explode("-",$entryDate);
		$transformEntryDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		for($arrayData=0;$arrayData<$numberOfArray;$arrayData++)
		{
			$amountTypeFlag=0;
			$tempArray[$arrayData] = array();
			$tempArray[$arrayData][0] = trim($request->input()['data'][$arrayData]['amount']);
			$tempArray[$arrayData][1] = trim($request->input()['data'][$arrayData]['amountType']);
			$tempArray[$arrayData][2] = trim($request->input()['data'][$arrayData]['ledgerId']);
			
			//check enum type[amount-type]
			$enumAmountTypeArray = array();
			$amountTypeEnum = new AmountTypeEnum();
			$enumAmountTypeArray = $amountTypeEnum->enumArrays();
			foreach ($enumAmountTypeArray as $key => $value)
			{
				if(strcmp($value,$tempArray[$arrayData][1])==0)
				{
					$amountTypeFlag=1;
					break;
				}
				else
				{
					$amountTypeFlag=0;
				}
			}
			if($amountTypeFlag==0)
			{
				return "1";
			}
		}
		
		// make an array
		$simpleArray = array();
		$simpleArray['jfId'] = $jfId;
		$simpleArray['entryDate'] = $transformEntryDate;
		$simpleArray['companyId'] = $companyId;
		$simpleArray['journalType'] = $journalType;
		
		$trimArray = array();
		for($data=0;$data<$numberOfArray;$data++)
		{
			$trimArray[$data]= array(
				'amount' => $tempArray[$data][0],
				'amountType' => $tempArray[$data][1],
				'ledgerId' => $tempArray[$data][2]
			);
		}
		array_push($simpleArray,$trimArray);
		return $simpleArray;
	}
	
	/**
	 * trim fromdate-todate data
     * @param object
     * @return array
     */
	public function trimDateData(Request $request)
	{
		//get data from header
		$fromDate =$request->header('fromDate');
		$toDate =$request->header('toDate');
		
		//trim the data
		$tFromDate =  trim($fromDate);
		$tToDate = trim($toDate);
		
		//date format conversion
		$splitedFromDate = explode("-",$tFromDate);
		$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
		
		$splitedToDate = explode("-",$tToDate);
		$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
		// $transformFromDate = Carbon\Carbon::createFromFormat('d-m-Y', $tFromDate)->format('Y-m-d');
		// $transformToDate = Carbon\Carbon::createFromFormat('d-m-Y', $tToDate)->format('Y-m-d');
		
		//put date into an array
		$trimArray = array();
		$trimArray['fromDate'] = $transformFromDate;
		$trimArray['toDate'] = $transformToDate;
		return $trimArray;
	}
	/**
	 * trim request data for update
     * @param object
     * @return array
     */
	public function trimUpdateData($journalArray)
	{
		$amountTypeFlag=0;
		$creditAmountArray = 0;
		$debitAmountArray = 0;
		$requestArray = array();
		$exceptionArray = array();
		$tJournalArray = array();
		$convertedValue="";
		$arraySample = array();
		$tempArrayFlag=0;
		$journalArrayFlag=0;
		$tempFlag=0;
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		for($requestArray=0;$requestArray<count($journalArray);$requestArray++)
		{
			//check if array is exists
			if(strcmp(array_keys($journalArray)[$requestArray],$constantArray['data'])==0)
			{
				//number of array elements
				for($arrayElement=0;$arrayElement<count($journalArray['data']);$arrayElement++)
				{
					$tempArrayFlag=1;
					$tempArray[$arrayElement] = array();
					$tempArray[$arrayElement]['amount'] = trim($journalArray['data'][$arrayElement]['amount']);
					$tempArray[$arrayElement]['amount_type'] = trim($journalArray['data'][$arrayElement]['amountType']);
					$tempArray[$arrayElement]['ledger_id'] = trim($journalArray['data'][$arrayElement]['ledgerId']);
					
					//check enum type[amount-type]
					$enumAmountTypeArray = array();
					$amountTypeEnum = new AmountTypeEnum();
					$enumAmountTypeArray = $amountTypeEnum->enumArrays();
					foreach ($enumAmountTypeArray as $key => $value)
					{
						if(strcmp($value,$tempArray[$arrayElement]['amount_type'])==0)
						{
							$amountTypeFlag=1;
							break;
						}
						else
						{
							$amountTypeFlag=0;
						}
					}
					if($amountTypeFlag==0)
					{
						return "1";
					}
				}
			}
			else
			{
				$key = array_keys($journalArray)[$requestArray];
				$value = $journalArray[$key];
				$journalArrayFlag=1;
				for($asciiChar=0;$asciiChar<strlen($key);$asciiChar++)
				{
					if(ord($key[$asciiChar])<=90 && ord($key[$asciiChar])>=65) 
					{
						$convertedValue1 = "_".chr(ord($key[$asciiChar])+32);
						$convertedValue=$convertedValue.$convertedValue1;
					}
					else
					{
						$convertedValue=$convertedValue.$key[$asciiChar];
					}
				}
				// print_r($convertedValue);
				//conversion of entry_date
				if(strcmp($convertedValue,$constantArray['entry_date'])==0)
				{
					$transformEntryDate=trim($value);
					$splitedDate = explode("-",$transformEntryDate);
					$tJournalArray[$convertedValue] = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
					$convertedValue="";
				}
				else
				{
					$tJournalArray[$convertedValue]=trim($value);
					$convertedValue="";
				}
				$tempFlag=1;
			}
			if($tempFlag==1)
			{
				if($requestArray==count($journalArray)-1)
				{
					$tJournalArray['flag']="1";
				}
			}
		}
		if($journalArrayFlag==1 && $tempArrayFlag==1)
		{
			array_push($tJournalArray,$tempArray);
			return $tJournalArray;
		}
		else if($tempArrayFlag==1)
		{
			return $tempArray;
		}
		else
		{
			return $tJournalArray;
		}
	}
}