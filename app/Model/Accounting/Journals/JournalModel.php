<?php
namespace ERP\Model\Accounting\Journals;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Journals\Entities\EncodeAllData;
use ERP\Core\Products\Entities\EncodeProductTrnAllData;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JournalModel extends Model
{
	protected $table = 'journal_dtl';
	/**
	 * insert data 
	 * @param  array
	 * returns the status
	*/
	public function insertData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$amountArray = array();
		$amountTypeArray = array();
		$ledgerIdArray = array();
		$jfIdArray = array();
		$entryDateArray = array();
		$companyIdArray = array();
		$journalTypeArray = array();
		
		$amountArray = func_get_arg(0);
		$amountTypeArray = func_get_arg(1);
		$jfIdArray = func_get_arg(2);
		$ledgerIdArray = func_get_arg(3);
		$entryDateArray = func_get_arg(4);
		$companyIdArray = func_get_arg(5);
		$journalTypeArray = func_get_arg(6);
		$debitAmount = array();
		$debitLedger = array();
		$creditAmount = array();
		$creditLedger = array();
		$debitArray=0;
		$creditArray=0;
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		for($data=0;$data<count($jfIdArray);$data++)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into 
			journal_dtl(
			jf_id,
			amount,
			amount_type,
			entry_date,
			ledger_id,
			company_id,
			journal_type,
			created_at) 
			values('".$jfIdArray[$data]."','".$amountArray[$data]."','".$amountTypeArray[$data]."','".$entryDateArray[$data]."','".$ledgerIdArray[$data]."','".$companyIdArray[$data]."','".$journalTypeArray[$data]."','".$mytime."')");
			DB::commit();
			if($raw==1)
			{
				if($amountTypeArray[$data]==$constantArray['credit'])
				{
					$creditAmount[$creditArray] = $amountArray[$data];
					$creditLedger[$creditArray] = $ledgerIdArray[$data];
					$creditArray++;
				}
				else
				{
					$debitAmount[$debitArray] = $amountArray[$data];
					$debitLedger[$debitArray] = $ledgerIdArray[$data];
					$debitArray++;
				}
			}
		}
		//related ledger entry
		for($data=0;$data<count($jfIdArray);$data++)
		{
			if($amountTypeArray[$data]==$constantArray['debit'])
			{
				//purchase case
				if(count($creditLedger)>1)
				{
					for($creditLoop=0;$creditLoop<count($creditLedger);$creditLoop++)
					{
						DB::beginTransaction();
						$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
						".$ledgerIdArray[$data]."_ledger_dtl(
						jf_id,
						amount,
						amount_type,
						entry_date,
						ledger_id,
						created_at) 
						values('".$jfIdArray[$data]."','".$creditAmount[$creditLoop]."','".$amountTypeArray[$data]."','".$entryDateArray[$data]."','".$creditLedger[$creditLoop]."','".$mytime."')");
						DB::commit();
					}
				}
				//sale case
				else
				{
					DB::beginTransaction();
					$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
					".$ledgerIdArray[$data]."_ledger_dtl(
					jf_id,
					amount,
					amount_type,
					entry_date,
					ledger_id,
					created_at) 
					values('".$jfIdArray[$data]."','".$amountArray[$data]."','".$amountTypeArray[$data]."','".$entryDateArray[$data]."','".$creditLedger[0]."','".$mytime."')");
					DB::commit();
				}
			}
			else
			{
				//sale case
				if(count($debitLedger)>1)
				{
					for($debitLoop=0;$debitLoop<count($debitLedger);$debitLoop++)
					{
						DB::beginTransaction();
						$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
						".$ledgerIdArray[$data]."_ledger_dtl(
						jf_id,
						amount,
						amount_type,
						entry_date,
						ledger_id,
						created_at) 
						values('".$jfIdArray[$data]."','".$debitAmount[$debitLoop]."','".$amountTypeArray[$data]."','".$entryDateArray[$data]."','".$debitLedger[$debitLoop]."','".$mytime."')");
						DB::commit();
					}
				}
				//purchase case
				else
				{
					DB::beginTransaction();
					$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
					".$ledgerIdArray[$data]."_ledger_dtl(
					jf_id,
					amount,
					amount_type,
					entry_date,
					ledger_id,
					created_at) 
					values('".$jfIdArray[$data]."','".$amountArray[$data]."','".$amountTypeArray[$data]."','".$entryDateArray[$data]."','".$debitLedger[0]."','".$mytime."')");
					DB::commit();
				}
			}
			if($ledgerEntryResult==0)
			{
				return $exceptionArray['500'];
			}
		}
		if($ledgerEntryResult==1)
		{
			return $exceptionArray['200'];
		}
	}
	
	/**
	 * insert purchase document data 
	 * @param document data array
	 * returns the error-message/status
	*/
	public function insertPurchaseDocumentData($documentArray,$type)
	{
		if(strcmp($type,"sales")==0)
		{
			$type = "sale";
		}
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT  MAX(jf_id) AS jf_id 
		from journal_dtl
		where deleted_at='0000-00-00 00:00:00' and 
		journal_type='".$type."'");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			for($arrayData=0;$arrayData<count($documentArray);$arrayData++)
			{
				DB::beginTransaction();
				$documentResult = DB::connection($databaseName)->statement("insert 
				into ".$type."_doc_dtl(
				document_name,
				document_size,
				document_format,
				jf_id)
				values(
				'".$documentArray[$arrayData][0]."',
				'".$documentArray[$arrayData][1]."',
				'".$documentArray[$arrayData][2]."',
				'".$raw[0]->jf_id."')");
				DB::commit();
				if($documentResult!=1)
				{
					return $exceptionArray['500'];
				}
			}
			return $exceptionArray['200'];
		}
	}
	
	/**
	 * insert purchase document data 
	 * @param document data array and jfId
	 * returns the error-message/status
	*/
	public function updatePurchaseDocumentData($jfId,$documentArray,$type)
	{
		if(strcmp($type,"sales")==0)
		{
			$type = "sale";
		}
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
	
		for($arrayData=0;$arrayData<count($documentArray);$arrayData++)
		{
			DB::beginTransaction();
			$documentResult = DB::connection($databaseName)->statement("insert 
			into ".$type."_doc_dtl(
			document_name,
			document_size,
			document_format,
			jf_id)
			values(
			'".$documentArray[$arrayData][0]."',
			'".$documentArray[$arrayData][1]."',
			'".$documentArray[$arrayData][2]."',
			'".$jfId."')");
			DB::commit();
			if($documentResult!=1)
			{
				return $exceptionArray['500'];
			}
		}
		return $exceptionArray['200'];
	}
	
	/**
	 * get data 
	 * get next jf id
	 * returns the error-message/data
	*/
	public function getJournalData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT  MAX(jf_id) AS jf_id 
		from journal_dtl
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$enocodedData = json_encode($raw);
			$decodedJson = json_decode($enocodedData,true);
			$nextValue = $decodedJson[0]['jf_id']+1;
			$data = array();
			$data['nextValue']=$nextValue;
			return json_encode($data);
		}
	}
	
	/**
	 * get data 
	 * @param  from-date and to-date
	 * get data between given date
	 * returns the error-message/data
	*/
	public function getData($fromDate,$toDate,$companyId,$headerType)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT 
		journal_id,
		jf_id,
		amount,
		amount_type,
		entry_date,
		created_at,
		updated_at,
		ledger_id,
		company_id
		FROM journal_dtl
		WHERE (entry_date BETWEEN '".$fromDate."' AND '".$toDate."') and 
		company_id='".$companyId."' and 
		journal_type='".$headerType."' and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$enocodedData = json_encode($raw);
			return $enocodedData;
		}
	}
	/**
	 * get data 
	 * get current year data
	 * returns the error-message/data
	*/
	public function getCurrentYearData($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT 
		journal_id,
		jf_id,
		amount,
		amount_type,
		entry_date,
		created_at,
		updated_at,
		ledger_id,
		company_id
		FROM journal_dtl  
		WHERE YEAR(created_at)= YEAR(CURDATE()) and 
		company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$enocodedData = json_encode($raw);
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($enocodedData);
			return $encodeAllData;
		}
	}
	
	/**
	 * get data 
	 * get data from jf_id(jf_id is get from journal_id)
	 * returns the error-message/data
	*/
	public function getJournalArrayData($journalId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$jfIdResult = DB::connection($databaseName)->select("SELECT 
		jf_id
		FROM journal_dtl  
		WHERE journal_id='".$journalId."' and 
		deleted_at='0000-00-00 00:00:00'");
		
		DB::commit();
		if(count($jfIdResult)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			journal_id,
			jf_id,
			amount,
			amount_type,
			entry_date,
			created_at,
			updated_at,
			ledger_id,
			company_id
			FROM journal_dtl  
			WHERE jf_id='".$jfIdResult[0]->jf_id."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			
			$enocodedData = json_encode($raw);
			return $enocodedData;
		}
	}
	
	/**
	 * get ledger balance data and add it to the ledger data
	 * @param:array
	 * returns the error-message/data
	*/
	public function getLedgerBalanceData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$journalArray = func_get_arg(0);
		$ledgerIdArray = array();
		$mergeArray = array();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		for($ledgerDataArray=0;$ledgerDataArray<count($journalArray);$ledgerDataArray++)
		{
			$ledgerIdArray[$ledgerDataArray] = $journalArray[$ledgerDataArray]->ledger->ledgerId;
			$currentBalanceType="";
			
			//get opening balance
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			".$ledgerIdArray[$ledgerDataArray]."_id,
			amount,
			amount_type
			from ".$ledgerIdArray[$ledgerDataArray]."_ledger_dtl
			WHERE balance_flag='opening' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($raw)!=0)
			{
				//get current balance
				DB::beginTransaction();
				$ledgerResult = DB::connection($databaseName)->select("SELECT 
				".$ledgerIdArray[$ledgerDataArray]."_id,
				amount,
				amount_type
				from ".$ledgerIdArray[$ledgerDataArray]."_ledger_dtl
				WHERE deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				
				$creditAmountArray =0;
				$debitAmountArray = 0;
				for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
				{
					if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,$constantArray['credit'])==0)
					{
						$creditAmountArray = $creditAmountArray+$ledgerResult[$ledgerArrayData]->amount;
						
					}
					else
					{
						$debitAmountArray = $debitAmountArray+$ledgerResult[$ledgerArrayData]->amount;
					}
				}
				if(count($ledgerResult)==0)
				{
					return $exceptionArray['404'];
				}
			}
			else
			{
				return $exceptionArray['404'];
			}
			//calculate opening balance
			if($creditAmountArray>$debitAmountArray)
			{
				$amountData = $creditAmountArray-$debitAmountArray;
				$currentBalanceType = $constantArray['credit'];
			}
			else
			{
				$amountData = $debitAmountArray-$creditAmountArray;
				$currentBalanceType =$constantArray['debit'];
			}
			$balanceAmountArray = array();
			$balanceAmountArray['openingBalance'] = $raw[0]->amount;
			$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
			$balanceAmountArray['currentBalance'] = $amountData;
			$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
			$mergeArray[$ledgerDataArray] = (Object)array_merge((array)$journalArray[$ledgerDataArray]->ledger,(array)((Object)$balanceAmountArray));
			$journalArray[$ledgerDataArray]->ledger=$mergeArray[$ledgerDataArray];
		}
		return json_encode($journalArray);
	}
	
	/**
	 * get journal data and product transaction-data 
	 * @param:jf_id,company_id and journal-type
	 * returns the error-message/data
	*/
	public function getJournalTransactionData($companyId,$journalType,$jfId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$journalResult = DB::connection($databaseName)->select("SELECT 
		journal_id,
		amount,
		jf_id,
		amount_type,	
		entry_date,
		created_at,
		updated_at,
		ledger_id,
		company_id
		FROM journal_dtl
		WHERE jf_id='".$jfId[0]."' and 
		company_id='".$companyId."' and 
		deleted_at='0000-00-00 00:00:00'");
		
		DB::commit();
		if(count($journalResult)!=0)
		{
			DB::beginTransaction();
			$transactionResult = DB::connection($databaseName)->select("SELECT 
			product_trn_id,
			transaction_date,
			transaction_type,
			qty,
			price,
			discount,
			discount_value,
			discount_type,
			is_display,
			invoice_number,
			bill_number,
			tax,
			created_at,
			updated_at,
			company_id,
			branch_id,
			product_id,
			jf_id
			FROM product_trn
			WHERE jf_id='".$jfId[0]."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($transactionResult)!=0)
			{
				$enocodedData = json_encode($journalResult);
				$encode = new EncodeAllData();
				$result = $encode->getEncodedAllData($enocodedData);
				$encodedResult = json_decode($result);
				
				$ledgerIdArray = array();
				$mergeArray = array();
				for($ledgerDataArray=0;$ledgerDataArray<count($encodedResult);$ledgerDataArray++)
				{
					$ledgerIdArray[$ledgerDataArray] = $encodedResult[$ledgerDataArray]->ledger->ledgerId;
					$currentBalanceType="";
					
					//get opening balance
					DB::beginTransaction();
					$raw = DB::connection($databaseName)->select("SELECT 
					".$ledgerIdArray[$ledgerDataArray]."_id,
					amount,
					amount_type
					from ".$ledgerIdArray[$ledgerDataArray]."_ledger_dtl
					WHERE balance_flag='opening' and 
					deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					
					if(count($raw)!=0)
					{
						//get current balance
						DB::beginTransaction();
						$ledgerResult = DB::connection($databaseName)->select("SELECT 
						".$ledgerIdArray[$ledgerDataArray]."_id,
						amount,
						amount_type
						from ".$ledgerIdArray[$ledgerDataArray]."_ledger_dtl
						WHERE deleted_at='0000-00-00 00:00:00'");
						DB::commit();
						
						$creditAmountArray =0;
						$debitAmountArray = 0;
						for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
						{
							if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,$constantArray['credit'])==0)
							{
								$creditAmountArray = $creditAmountArray+$ledgerResult[$ledgerArrayData]->amount;
								
							}
							else
							{
								$debitAmountArray = $debitAmountArray+$ledgerResult[$ledgerArrayData]->amount;
							}
						}
						if(count($ledgerResult)==0)
						{
							return $exceptionArray['404'];
						}
					}
					else
					{
						return $exceptionArray['404'];
					}
					//calculate opening balance
					if($creditAmountArray>$debitAmountArray)
					{
						$amountData = $creditAmountArray-$debitAmountArray;
						$currentBalanceType = $constantArray['credit'];
					}
					else
					{
						$amountData = $debitAmountArray-$creditAmountArray;
						$currentBalanceType = $constantArray['debit'];
					}
					$balanceAmountArray = array();
					$balanceAmountArray['openingBalance'] = $raw[0]->amount;
					$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
					$balanceAmountArray['currentBalance'] = $amountData;
					$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
					$mergeArray[$ledgerDataArray] = (Object)array_merge((array)$encodedResult[$ledgerDataArray]->ledger,(array)((Object)$balanceAmountArray));
					$encodedResult[$ledgerDataArray]->ledger=$mergeArray[$ledgerDataArray];
				}		
				$enocodedProductData = json_encode($transactionResult);
				$encodeProductData = new EncodeProductTrnAllData();
				$getEncodedData = $encodeProductData->getEncodedAllData($enocodedProductData);
				
				$ledgerTransactionarray = array();
				$ledgerTransactionarray['journal'] = $encodedResult;
				$ledgerTransactionarray['productTransaction'] = json_decode($getEncodedData);
				
				if(strcmp($journalType,"sales")==0)
				{
					//get sale document
					DB::beginTransaction();
					$journalDocumentResult = DB::connection($databaseName)->select("SELECT 
					document_id,
					document_name,
					document_size,
					document_format,
					jf_id,
					created_at,
					updated_at
					from sale_doc_dtl
					WHERE deleted_at='0000-00-00 00:00:00' and 
					jf_id='".$jfId[0]."'");
					DB::commit();
				}
				else
				{
					//get purchase document
					DB::beginTransaction();
					$journalDocumentResult = DB::connection($databaseName)->select("SELECT 
					document_id,
					document_name,
					document_size,
					document_format,
					jf_id,
					created_at,
					updated_at
					from purchase_doc_dtl
					WHERE deleted_at='0000-00-00 00:00:00' and 
					jf_id='".$jfId[0]."'");
					DB::commit();
				}
				if(!empty($journalDocumentResult))
				{
					$documentArray = array();
					for($arrayData=0;$arrayData<count($journalDocumentResult);$arrayData++)
					{
						$documentArray[$arrayData] = array();
						$documentArray[$arrayData]['documentId'] = $journalDocumentResult[$arrayData]->document_id;
						$documentArray[$arrayData]['documentName'] = $journalDocumentResult[$arrayData]->document_name;
						$documentArray[$arrayData]['documentSize'] = $journalDocumentResult[$arrayData]->document_size;
						$documentArray[$arrayData]['documentFormat'] = $journalDocumentResult[$arrayData]->document_format;
						$documentArray[$arrayData]['documentUrl'] = $constantArray['journalDocumentUrl'];
					}
					$ledgerTransactionarray['document'] = $documentArray;
				}
				if(strcmp($journalType,"purchase")==0)
				{
					//get clientName from purchase-bill
					DB::beginTransaction();
					$clientNameResult = DB::connection($databaseName)->select("SELECT 
					client_name
					from purchase_bill
					WHERE deleted_at='0000-00-00 00:00:00' and 
					jf_id='".$jfId[0]."'");
					DB::commit();
					
					$ledgerTransactionarray['clientName'] = $clientNameResult[0]->client_name;
				}
				return json_encode($ledgerTransactionarray);
			}
			else
			{
				return $exceptionArray['404'];
			}
		}
		else
		{
			return $exceptionArray['404'];
		}
	}
	
	/**
	 * get data 
	 * get journal data as per jf id
	 * returns the error-message/data
	*/
	public function getJfIdArrayData($jfId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("SELECT 
		journal_id,
		jf_id,
		amount,
		amount_type,
		entry_date,
		created_at,
		updated_at,
		ledger_id,
		company_id
		from journal_dtl
		WHERE jf_id='".$jfId."' and 
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$enocodedData = json_encode($raw);
			return $enocodedData;
		}
	}
	
	/**
	 * update data 
	 * @param array
	 * returns the error-message/status
	*/
	public function updateData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$arrayDataFlag=0;
		$ledgerResult=0;
		$journalRaw=0;
		$creditAmount = array();
		$creditLedger = array();
		$debitLedger = array();
		$debitAmount = array();
		$journalArray = func_get_arg(0);
		$jfId = func_get_arg(1);
		$journalType = func_get_arg(2);
		$mytime = Carbon\Carbon::now();
		$journalRaw="";
		$ledgerResult="";
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get journal data as per jf_id
		$journalModel = new JournalModel();
		$jfIdArrayData = $journalModel->getJfIdArrayData($jfId);
		$jfIdData = json_decode($jfIdArrayData);
		if(array_key_exists(0,$journalArray))
		{
			$arrayDataFlag=1;
		}
		//array exists
		if($arrayDataFlag==1)
		{
			//delete ledger data and journal data
			for($dbJournalData=0;$dbJournalData<count($jfIdData);$dbJournalData++)
			{
				$entryDate = $jfIdData[0]->entry_date;
				$companyId = $jfIdData[0]->company_id;
				//delete the ledger entry
				DB::beginTransaction();
				$ledgerResult = DB::connection($databaseName)->statement("update ".$jfIdData[$dbJournalData]->ledger_id."_ledger_dtl
				set deleted_at='".$mytime."' 
				where jf_id='".$jfId."'");
				DB::commit();
				if($ledgerResult==0)
				{
					return $exceptionArray['500'];
				}
			}
			//delete the journal entry
			if($ledgerResult==1)
			{
				DB::beginTransaction();
				$journalResult = DB::connection($databaseName)->statement("update journal_dtl
				set deleted_at='".$mytime."' where jf_id='".$jfId."'");
				DB::commit();
				if($journalResult==0)
				{
					return $exceptionArray['500'];
				}
			}
			//insert into journal as well as related ledger table
			if($journalResult==1)
			{
				$creditArray=0;
				$debitArray=0;
				//journal entry
				for($data=0;$data<count($journalArray);$data++)
				{
					DB::beginTransaction();
					$journalInsertResult = DB::connection($databaseName)->statement("insert into 
					journal_dtl(
					jf_id,
					amount,
					amount_type,
					entry_date,
					ledger_id,
					company_id,
					journal_type,
					updated_at,
					created_at) 
					values('".$jfId."','".$journalArray[$data]['amount']."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$journalArray[$data]['ledger_id']."','".$companyId."','".$journalType."','".$mytime."','".$mytime."')");
					DB::commit();
					if($journalInsertResult==1)
					{
						if($journalArray[$data]['amount_type']==$constantArray['credit'])
						{
							$creditAmount[$creditArray] = $journalArray[$data]['amount'];
							$creditLedger[$creditArray] = $journalArray[$data]['ledger_id'];
							$creditArray++;
						}
						else
						{
							$debitAmount[$debitArray] = $journalArray[$data]['amount'];
							$debitLedger[$debitArray] = $journalArray[$data]['ledger_id'];
							$debitArray++;
						}
					}
					else
					{
						return $exceptionArray['500'];
					}
				}
				//related ledger entry
				for($data=0;$data<count($journalArray);$data++)
				{
					if($journalArray[$data]['amount_type']==$constantArray['debit'])
					{
						//purchase case
						if(count($creditLedger)>1)
						{
							for($creditLoop=0;$creditLoop<count($creditLedger);$creditLoop++)
							{
								DB::beginTransaction();
								$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
								".$journalArray[$data]['ledger_id']."_ledger_dtl(
								jf_id,
								amount,
								amount_type,
								entry_date,
								ledger_id,
								updated_at,
								created_at) 
								values('".$jfId."','".$creditAmount[$creditLoop]."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$creditLedger[$creditLoop]."','".$mytime."','".$mytime."')");
								DB::commit();
							}
						}
						//sale case
						else
						{
							DB::beginTransaction();
							$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
							".$journalArray[$data]['ledger_id']."_ledger_dtl(
							jf_id,
							amount,
							amount_type,
							entry_date,
							ledger_id,
							updated_at,
							created_at) 
							values('".$jfId."','".$journalArray[$data]['amount']."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$creditLedger[0]."','".$mytime."','".$mytime."')");
							DB::commit();
						}
					}
					else
					{
						//sale case
						if(count($debitLedger)>1)
						{
							for($debitLoop=0;$debitLoop<count($debitLedger);$debitLoop++)
							{
								DB::beginTransaction();
								$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
								".$journalArray[$data]['ledger_id']."_ledger_dtl(
								jf_id,
								amount,
								amount_type,
								entry_date,
								ledger_id,
								updated_at,
								created_at) 
								values('".$jfId."','".$debitAmount[$debitLoop]."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$debitLedger[$debitLoop]."','".$mytime."','".$mytime."')");
								DB::commit();
							}
						}
						//purchase case
						else
						{
							DB::beginTransaction();
							$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
							".$journalArray[$data]['ledger_id']."_ledger_dtl(
							jf_id,
							amount,
							amount_type,
							entry_date,
							ledger_id,
							updated_at,
							created_at) 
							values('".$jfId."','".$journalArray[$data]['amount']."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$debitLedger[0]."','".$mytime."','".$mytime."')");
							DB::commit();
						}
					}
					if($ledgerEntryResult==0)
					{
						return $exceptionArray['500'];
					}
				}
				if($ledgerEntryResult==1)
				{
					return $exceptionArray['200'];
				}
			}
		}
		else
		{
			//update company_id from journal
			if(array_key_exists($constantArray['company_id'],$journalArray))
			{
				//update the company_id from journal
				DB::beginTransaction();
				$journalRaw = DB::connection($databaseName)->statement("update journal_dtl
				set company_id='".$journalArray['company_id']."',updated_at='".$mytime."' where jf_id='".$jfId."' and deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				if($journalRaw==0)
				{
					return $exceptionArray['500'];
				}
			}
			//update entryDate from joural and ledgerId_ledger_dtl
			if(array_key_exists($constantArray['entry_date'],$journalArray))
			{
				//update entry_date from journal 
				DB::beginTransaction();
				$journalResult = DB::connection($databaseName)->statement("update journal_dtl
				set entry_date='".$journalArray['entry_date']."',updated_at='".$mytime."' where jf_id='".$jfId."' and deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				if($journalResult==1)
				{
					//update entry_date from ledgerId_ledger_dtl
					for($data=0;$data<count($jfIdData);$data++)
					{
						DB::beginTransaction();
						$ledgerResult = DB::connection($databaseName)->statement("update ".$jfIdData[$data]->ledger_id."_ledger_dtl
						set entry_date='".$journalArray['entry_date']."',updated_at='".$mytime."' where jf_id='".$jfId."' and deleted_at='0000-00-00 00:00:00'");
						DB::commit();
						if($ledgerResult==0)
						{
							return $exceptionArray['500'];
						}
					}
				}
			}
			if($journalRaw==1 || $ledgerResult==1)
			{
				return $exceptionArray['200'];
			}
		}
		
	}
	

	/**
	 * update array with data 
	 * @param array,data,jf_id
	 * returns the error-message/status
	*/
	public function updateArrayData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//update array with data
		$journalArray = func_get_arg(0);
		$journalData = func_get_arg(1);
		$jfId = func_get_arg(2);
		$journalType = func_get_arg(3);
		$mytime = Carbon\Carbon::now();
		$journalRaw=0;
		$ledgerResult=0;
		$ledgerEntryResult=0;

		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$journalModel = new JournalModel();
		$jfIdArrayData = $journalModel->getJfIdArrayData($jfId);
		$jfIdData = json_decode($jfIdArrayData);
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(count($journalArray)!=0)
		{
			//delete ledger data and journal data
			for($dbJournalData=0;$dbJournalData<count($jfIdData);$dbJournalData++)
			{
				$entryDate = $jfIdData[0]->entry_date;
				$companyId = $jfIdData[0]->company_id;
				//delete the ledger entry
				DB::beginTransaction();
				$ledgerResult = DB::connection($databaseName)->statement("update ".$jfIdData[$dbJournalData]->ledger_id."_ledger_dtl
				set deleted_at='".$mytime."' where jf_id='".$jfId."'");
				DB::commit();
				if($ledgerResult==0)
				{
					return $exceptionArray['500'];
				}
			}
			//delete the journal entry
			if($ledgerResult==1)
			{
				DB::beginTransaction();
				$journalResult = DB::connection($databaseName)->statement("update journal_dtl
				set deleted_at='".$mytime."' where jf_id='".$jfId."'");
				DB::commit();
				if($journalResult==0)
				{
					return $exceptionArray['500'];
				}
			}
			//insert into journal as well as related ledger table
			if($journalResult==1)
			{
				$creditArray=0;
				$debitArray=0;
				//journal entry
				for($data=0;$data<count($journalArray);$data++)
				{
					DB::beginTransaction();
					$journalInsertResult = DB::connection($databaseName)->statement("insert into 
					journal_dtl(
					jf_id,
					amount,
					amount_type,
					entry_date,
					ledger_id,
					company_id,
					journal_type,
					updated_at,
					created_at) 
					values('".$jfId."','".$journalArray[$data]['amount']."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$journalArray[$data]['ledger_id']."','".$companyId."','".$journalType."','".$mytime."','".$mytime."')");
					DB::commit();
					if($journalInsertResult==1)
					{
						if($journalArray[$data]['amount_type']==$constantArray['credit'])
						{
							$creditAmount[$creditArray] = $journalArray[$data]['amount'];
							$creditLedger[$creditArray] = $journalArray[$data]['ledger_id'];
							$creditArray++;
						}
						else
						{
							$debitAmount[$debitArray] = $journalArray[$data]['amount'];
							$debitLedger[$debitArray] = $journalArray[$data]['ledger_id'];
							$debitArray++;
						}
					}
					else
					{
						return $exceptionArray['500'];
					}
				}
				//related ledger entry
				for($data=0;$data<count($journalArray);$data++)
				{
					if($journalArray[$data]['amount_type']==$constantArray['debit'])
					{
						//purchase case
						if(count($creditLedger)>1)
						{
							for($creditLoop=0;$creditLoop<count($creditLedger);$creditLoop++)
							{
								DB::beginTransaction();
								$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
								".$journalArray[$data]['ledger_id']."_ledger_dtl(
								jf_id,
								amount,
								amount_type,
								entry_date,
								ledger_id,
								updated_at,
								created_at) 
								values('".$jfId."','".$creditAmount[$creditLoop]."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$creditLedger[$creditLoop]."','".$mytime."','".$mytime."')");
								DB::commit();
							}
						}
						//sale case
						else
						{
							DB::beginTransaction();
							$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
							".$journalArray[$data]['ledger_id']."_ledger_dtl(
							jf_id,
							amount,
							amount_type,
							entry_date,
							ledger_id,
							updated_at,
							created_at) 
							values('".$jfId."','".$journalArray[$data]['amount']."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$creditLedger[0]."','".$mytime."','".$mytime."')");
							DB::commit();
						}
					}
					else
					{
						//sale case
						if(count($debitLedger)>1)
						{
							for($debitLoop=0;$debitLoop<count($debitLedger);$debitLoop++)
							{
								DB::beginTransaction();
								$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
								".$journalArray[$data]['ledger_id']."_ledger_dtl(
								jf_id,
								amount,
								amount_type,
								entry_date,
								ledger_id,
								updated_at,
								created_at) 
								values('".$jfId."','".$debitAmount[$debitLoop]."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$debitLedger[$debitLoop]."','".$mytime."','".$mytime."')");
								DB::commit();
							}
						}
						//purchase case
						else
						{
							DB::beginTransaction();
							$ledgerEntryResult = DB::connection($databaseName)->statement("insert into 
							".$journalArray[$data]['ledger_id']."_ledger_dtl(
							jf_id,
							amount,
							amount_type,
							entry_date,
							ledger_id,
							updated_at,
							created_at) 
							values('".$jfId."','".$journalArray[$data]['amount']."','".$journalArray[$data]['amount_type']."','".$entryDate."','".$debitLedger[0]."','".$mytime."','".$mytime."')");
							DB::commit();
						}
					}
					if($ledgerEntryResult==0)
					{
						return $exceptionArray['500'];
					}
				}
			}
		}
		//update company_id from journal
		if(array_key_exists($constantArray['company_id'],$journalData))
		{
			//update the company_id from journal
			DB::beginTransaction();
			$journalRaw = DB::connection($databaseName)->statement("update journal_dtl
			set company_id='".$journalData['company_id']."',updated_at='".$mytime."' where jf_id='".$jfId."' and deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if($journalRaw==0)
			{
				return $exceptionArray['500'];
			}
		}
		//update entryDate from joural and ledgerId_ledger_dtl
		if(array_key_exists($constantArray['entry_date'],$journalData))
		{
			//update entry_date from journal 
			DB::beginTransaction();
			$journalResult = DB::connection($databaseName)->statement("update journal_dtl
			set entry_date='".$journalData['entry_date']."',updated_at='".$mytime."' where jf_id='".$jfId."' and deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if($journalResult==1)
			{
				//update entry_date from ledgerId_ledger_dtl
				for($data=0;$data<count($jfIdData);$data++)
				{
					DB::beginTransaction();
					$ledgerResult = DB::connection($databaseName)->statement("update ".$jfIdData[$data]->ledger_id."_ledger_dtl
					set entry_date='".$journalData['entry_date']."',updated_at='".$mytime."' where jf_id='".$jfId."' and deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					if($ledgerResult==0)
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
		if($journalRaw==1 || $ledgerResult==1 || $ledgerEntryResult==1)
		{
			return $exceptionArray['200'];
		}
	}

	/*
	 * get current finantialyear data of remaining payment
	*/
	public function getReminingPayment()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$finantialyearData = $constantDatabase->constantAccountingDate();
		$journalDocumentResult = array();
		//get purchase document
		DB::beginTransaction();
		$journalDocumentResult = DB::connection($databaseName)->select("select 
		sum(j.amount) as amount,
		j.ledger_id,
		j.amount_type
		from journal_dtl as j 
		INNER JOIN ledger_mst as l ON l.ledger_id=j.ledger_id
		where (j.entry_date BETWEEN '".$finantialyearData['fromDate']."' AND '".$finantialyearData['toDate']."') and 
		j.deleted_at='0000-00-00 00:00:00' and 
		l.ledger_group_id=32 
		GROUP by ledger_id,amount_type
		ORDER by j.ledger_id");
		DB::commit();
		return json_encode($journalDocumentResult);
	}	
}
