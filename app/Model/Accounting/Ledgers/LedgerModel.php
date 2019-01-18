<?php
namespace ERP\Model\Accounting\Ledgers;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Journals\Entities\EncodeAllData;
use ERP\Model\Companies\CompanyModel;
use ERP\Core\Accounting\Ledgers\Entities\LedgerArray;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Clients\ClientModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerModel extends Model
{

	protected $table = 'ledger_mst';
	
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
		
		$getLedgerData = array();
		$getLedgerKey = array();
		$getLedgerData = func_get_arg(0);
		$getLedgerKey = func_get_arg(1);
		$ledgerData="";
		$keyName = "";
		for($data=0;$data<count($getLedgerData);$data++)
		{
			if($data == (count($getLedgerData)-1))
			{
				$ledgerData = $ledgerData."'".$getLedgerData[$data]."'";
				$keyName =$keyName.$getLedgerKey[$data];
			}
			else
			{
				$ledgerData = $ledgerData."'".$getLedgerData[$data]."',";
				$keyName =$keyName.$getLedgerKey[$data].",";
			}
		}


		try{

			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into ledger_mst(".$keyName.",created_at) 
			values(".$ledgerData.",'".$mytime."')");
			DB::commit();
		}catch(\Illuminate\Database\QueryException $ex){ 
		  dd($ex->getMessage()); 
		  // Note any method of class PDOException can be called on $ex.
		}

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			$ledgerId = DB::connection($databaseName)->select("SELECT  MAX(ledger_id) AS ledger_id from ledger_mst where deleted_at='0000-00-00 00:00:00'");
			$result = DB::connection($databaseName)->statement("CREATE TABLE ".$ledgerId[0]->ledger_id."_ledger_dtl (
			 `".$ledgerId[0]->ledger_id."_id` int(11) NOT NULL AUTO_INCREMENT,
			 `amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
			 `amount_type` enum('credit','debit') NOT NULL DEFAULT 'credit',
			 `entry_date` date NOT NULL DEFAULT '0000-00-00',
			 `jf_id` int(11) NOT NULL,
			 `balance_flag` enum('','opening','closing') NOT NULL DEFAULT '',
			 `created_at` datetime NOT NULL,
			 `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `ledger_id` int(11) NOT NULL,
			 PRIMARY KEY (`".$ledgerId[0]->ledger_id."_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf16");

			if($result==1)
			{
				DB::beginTransaction();
				$ledgerData = DB::connection($databaseName)->select("select 
				ledger_id,
				ledger_name,
				alias,
				inventory_affected,
				address1,
				address2,
				contact_no,
				email_id,
				is_dealer,
				invoice_number,
				outstanding_limit,
				outstanding_limit_type, 
				pan,
				tin,
				cgst,
				sgst,
				bank_id,
				bank_dtl_id,
				micr_code,
				created_at,
				updated_at,
				deleted_at,
				state_abb,
				city_id,
				ledger_group_id,
				company_id
				from ledger_mst 
				where ledger_id = (select max(ledger_id) from ledger_mst) and deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				return json_encode($ledgerData);
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
	 * insert all data (ledger data & amount data)
	 * @param  array
	 * returns the status
	*/
	public function insertAllData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$mytime = Carbon\Carbon::now();
		
		$getLedgerData = array();
		$getLedgerKey = array();
		$getLedgerData = func_get_arg(0);
		$getLedgerKey = func_get_arg(1);
		$getLedgerBalanceData = array();
		$getLedgerBalanceKey = array();
		$getLedgerBalanceData = func_get_arg(2);
		$getLedgerBalanceKey = func_get_arg(3);
		$ledgerData="";
		$ledgerBalanceData="";
		$keyName = "";
		$balanceKeyName = "";
		//make keys and values for query of ledger data
		for($data=0;$data<count($getLedgerData);$data++)
		{
			if($data == (count($getLedgerData)-1))
			{
				$ledgerData = $ledgerData."'".$getLedgerData[$data]."'";
				$keyName =$keyName.$getLedgerKey[$data];
			}
			else
			{
				$ledgerData = $ledgerData."'".$getLedgerData[$data]."',";
				$keyName =$keyName.$getLedgerKey[$data].",";
			}
		}
		//make keys and values for query of balance data
		for($balanceData=0;$balanceData<count($getLedgerBalanceData);$balanceData++)
		{
			if($balanceData == (count($getLedgerBalanceData)-1))
			{
				$ledgerBalanceData = $ledgerBalanceData."'".$getLedgerBalanceData[$balanceData]."'";
				$balanceKeyName =$balanceKeyName.$getLedgerBalanceKey[$balanceData];
			}
			else
			{
				$ledgerBalanceData = $ledgerBalanceData."'".$getLedgerBalanceData[$balanceData]."',";
				$balanceKeyName =$balanceKeyName.$getLedgerBalanceKey[$balanceData].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into ledger_mst(".$keyName.",created_at) 
		values(".$ledgerData.",'".$mytime."')");
		DB::commit();
		
		if($raw==1)
		{
			$ledgerId = DB::connection($databaseName)->select("SELECT  MAX(ledger_id) AS ledger_id from ledger_mst where deleted_at='0000-00-00 00:00:00'");
			$result = DB::connection($databaseName)->statement("CREATE TABLE ".$ledgerId[0]->ledger_id."_ledger_dtl (
			 `".$ledgerId[0]->ledger_id."_id` int(11) NOT NULL AUTO_INCREMENT,
			 `amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
			 `amount_type` enum('credit','debit') NOT NULL DEFAULT 'credit',
			 `entry_date` date NOT NULL DEFAULT '0000-00-00',
			 `jf_id` int(11) NOT NULL,
			 `balance_flag` enum('','opening','closing') NOT NULL  DEFAULT '',
			 `created_at` datetime NOT NULL,
			 `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			 `ledger_id` int(11) NOT NULL,
			 PRIMARY KEY (`".$ledgerId[0]->ledger_id."_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf16");

			if($result==1)
			{
				//insertion of balance data in ledger table
				$ledgerInsertionResult = DB::connection($databaseName)->statement("insert into ".$ledgerId[0]->ledger_id."_ledger_dtl(".$balanceKeyName.",ledger_id,entry_date,created_at) 
				values(".$ledgerBalanceData.",'".$ledgerId[0]->ledger_id."','".$mytime."','".$mytime."')");
				if($ledgerInsertionResult==1)
				{
					DB::beginTransaction();
					$ledgerData = DB::connection($databaseName)->select("select 
					ledger_id,
					ledger_name,
					alias,
					inventory_affected,
					address1,
					address2,
					contact_no,
					email_id,
					is_dealer,
					invoice_number,
					outstanding_limit,
					outstanding_limit_type, 	
					pan,
					tin,
					cgst,
					sgst,
					bank_id,
					bank_dtl_id,
					micr_code,
					created_at,
					updated_at,
					deleted_at,
					state_abb,
					city_id,
					ledger_group_id,
					company_id
					from ledger_mst 
					where ledger_id = (select max(ledger_id) from ledger_mst) and deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					$encodedData = json_encode($ledgerData);
					return $encodedData;
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
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * update data 
	 * @param  ledger-data,key of ledger-data,ledger-id
	 * returns the status
	*/
	public function insertGeneralLedger($companyId)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get company data as per given companyId
		$companyModel = new CompanyModel();
		$companyData = $companyModel->getData($companyId[0]->company_id);
		$decodedCompanyData = json_decode($companyData);
		$stateAbb = $decodedCompanyData[0]->state_abb;
		$cityId = $decodedCompanyData[0]->city_id;
		
		$ledgerArray = new ledgerArray();
		$generalLedgerArray = $ledgerArray->ledgerArrays();
		$generalLedgerGrpArray = $ledgerArray->ledgerGrpArray();
		
		for($arrayData=0;$arrayData<count($generalLedgerArray);$arrayData++)
		{
			DB::beginTransaction();
			$ledgerInsertionResult = DB::connection($databaseName)->statement("insert into ledger_mst
			(ledger_name,
			inventory_affected,
			state_abb,
			city_id,
			ledger_group_id,
			company_id,
			created_at)
			values
			('".$generalLedgerArray[$arrayData]."',
			'yes',
			'".$stateAbb."',
			'".$cityId."',
			'".$generalLedgerGrpArray[$arrayData]."',
			'".$companyId[0]->company_id."',
			'".$mytime."')");
			DB::commit();
			
			if($ledgerInsertionResult==0)
			{
				return $exceptionArray['500'];
			}
		}
		//get max ledgerId
		DB::beginTransaction();
		$ledgerIdData = DB::connection($databaseName)->select("select 
		ledger_id,
		created_at
		from ledger_mst 
		where deleted_at='0000-00-00 00:00:00' and company_id='".$companyId[0]->company_id."'");
		DB::commit();
		if(count($ledgerIdData)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			//insert ledger into ledgerId_ledger_dtl
			for($ledgerIdArray=0;$ledgerIdArray<count($ledgerIdData);$ledgerIdArray++)
			{
				DB::beginTransaction();
				$result = DB::connection($databaseName)->statement("CREATE TABLE ".$ledgerIdData[$ledgerIdArray]->ledger_id."_ledger_dtl (
				 `".$ledgerIdData[$ledgerIdArray]->ledger_id."_id` int(11) NOT NULL AUTO_INCREMENT,
				 `amount` decimal(20,4) NOT NULL DEFAULT '0.0000',
				 `amount_type` enum('credit','debit') NOT NULL DEFAULT 'credit',
				 `entry_date` date NOT NULL DEFAULT '0000-00-00',
				 `jf_id` int(11) NOT NULL,
				 `balance_flag` enum('','opening','closing') NOT NULL  DEFAULT '',
				 `created_at` datetime NOT NULL,
				 `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				 `deleted_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				 `ledger_id` int(11) NOT NULL,
				 PRIMARY KEY (`".$ledgerIdData[$ledgerIdArray]->ledger_id."_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf16");
				DB::commit();
				
				DB::beginTransaction();
				$ledgerTrnData = DB::connection($databaseName)->statement("insert into 
				".$ledgerIdData[$ledgerIdArray]->ledger_id."_ledger_dtl
				(amount,
				amount_type,
				balance_flag,
				entry_date,
				jf_id,
				ledger_id,
				created_at)
				values
				('0.00',
				'credit',
				'opening',
				'".$ledgerIdData[$ledgerIdArray]->created_at."',
				'0',
				'".$ledgerIdData[$ledgerIdArray]->ledger_id."',
				'".$mytime."')");
				DB::commit();
				if($ledgerTrnData==0)
				{
					return $exceptionArray['500'];
				}
			}
			return $exceptionArray['200'];
		}
		
	}
	
	/**
	 * update data 
	 * @param  ledger-data,key of ledger-data,ledger-id
	 * returns the status
	*/
	public function updateData($ledgerData,$key,$ledgerId)
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
		$keyValueStringForOtherLedger="";
		$keyValueStringAmt="";
		$clientkeyValueString='';
		$clientFlag=0;
		$clientIdFlag=0;
		$newClientId='';
		for($data=0;$data<count($ledgerData);$data++)
		{
			//get contact_no of ledger from database for updating the contact of client
			DB::beginTransaction();
			$ledgerResult = DB::connection($databaseName)->select("select 
			ledger_id,
			client_id,
			contact_no
			from ledger_mst where deleted_at='0000-00-00 00:00:00' and 
			ledger_id='".$ledgerId."'");
			DB::commit();
			
			$RequestUri = explode("/", $_SERVER['REQUEST_URI']);
			if(strcmp($RequestUri[2],"bills")!=0)
			{
				$clientNameData='';
				if(strcmp('contact_no',$key[$data])==0)
				{
					if(count($ledgerResult)!=0)
					{
						//update client contact_no
						// check contact_no exists or not?
						$clientModel = new ClientModel();
						$clientData = $clientModel->getClientData($ledgerData[$data]);
						if(strcmp($clientData,$exceptionArray['200'])!=0)
						{
							$clientDecodedData = json_decode($clientData)->clientData;
							if($clientDecodedData[0]->contact_no!=$ledgerData[$data])
							{
								return $exceptionArray['contact'];
							}
						}
						else
						{
							//update contact number
							DB::beginTransaction();
							$updateClientResult = DB::connection($databaseName)->statement("update client_mst 
							set contact_no= '".$ledgerData[$data]."',updated_at='".$mytime."'
							where contact_no = '".$ledgerResult[0]->contact_no."' and 
							deleted_at='0000-00-00 00:00:00'");
							DB::commit();
						}
					}
				}
				if(strcmp('ledger_name',$key[$data])==0 || strcmp('address1',$key[$data])==0 || strcmp('state_abb',$key[$data])==0 || strcmp('city_id',$key[$data])==0 || strcmp('email_id',$key[$data])==0)
				{
					$clientFlag=1;
					if(strcmp('ledger_name',$key[$data])==0)
					{
						$clientNameKey = "client_name";
					}
					else
					{
						$clientNameKey = $key[$data];
					}
					$clientkeyValueString = $clientkeyValueString.$clientNameKey."='".$ledgerData[$data]."',";
				}
			}
			
			if(strcmp($key[$data],"amount")==0 || strcmp($key[$data],"amount_type")==0 || strcmp($key[$data],"balance_flag")==0)
			{
				$keyValueStringAmt=$keyValueStringAmt.$key[$data]."='".$ledgerData[$data]."',";
			}
			else
			{
				$keyValueString=$keyValueString.$key[$data]."='".$ledgerData[$data]."',";
				if(strcmp($key[$data],'ledger_name')!=0)
				{
					$keyValueStringForOtherLedger=$keyValueStringForOtherLedger.$key[$data]."='".$ledgerData[$data]."',";
				}
				else
				{
					$keyValueString=$keyValueString."client_name = '".$ledgerData[$data]."',";
					$keyValueStringForOtherLedger=$keyValueStringForOtherLedger."client_name = '".$ledgerData[$data]."',";
				}
				if(strcmp($key[$data],'client_id')==0)
				{
					$clientIdFlag = 1;
					$newClientId = $ledgerData[$data];
				}
			}
		}
		if($clientFlag==1)
		{
			//update client-detail
			DB::beginTransaction();
			$updateClientDataResult = DB::connection($databaseName)->statement("update client_mst 
			set ".$clientkeyValueString." updated_at='".$mytime."'
			where contact_no = '".$ledgerResult[0]->contact_no."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		$updateAllLedgerFlag=0;
		//get ledger-data as per client-id 
		if(count($ledgerResult)!=0)
		{
			if($ledgerResult[0]->client_id!=0 || $ledgerResult[0]->client_id!=null || $ledgerResult[0]->client_id!='')
			{
				$updateAllLedgerFlag=1;
				$clientId = $ledgerResult[0]->client_id;
			}
		}
		if($clientIdFlag==1 && $newClientId!='' &&  $newClientId!=0 &&  $newClientId!=null)
		{
			$updateAllLedgerFlag=1;
			$clientId = $newClientId;
		}
		if($updateAllLedgerFlag==1)
		{			
			//get ledger as per client-id
			DB::beginTransaction();
			$allLedgerResult = DB::connection($databaseName)->select("select 
			ledger_id,
			client_id,
			contact_no
			from ledger_mst where deleted_at='0000-00-00 00:00:00' and 
			ledger_id!='".$ledgerId."' and client_id='".$clientId."'");
			DB::commit();
			if(count($allLedgerResult)!=0)
			{
				$allLedgerCount = count($allLedgerResult);
				for($allLedgerIndex=0;$allLedgerIndex<$allLedgerCount;$allLedgerIndex++)
				{
					if($keyValueStringAmt!="")
					{
						DB::beginTransaction();
						$ledgerTrnData = DB::connection($databaseName)->statement("update ".$allLedgerResult[0]->ledger_id."_ledger_dtl 
						set ".$keyValueStringAmt."updated_at='".$mytime."'
						where ledger_id = '".$allLedgerResult[0]->ledger_id."' and deleted_at='0000-00-00 00:00:00' and balance_flag='opening'");
						DB::commit();
					}
					if($keyValueStringForOtherLedger!="")
					{
						$clientNameString='';
						DB::beginTransaction();
						$raw = DB::connection($databaseName)->statement("update ledger_mst 
						set ".$keyValueStringForOtherLedger."updated_at='".$mytime."'".$clientNameString."
						where ledger_id = '".$allLedgerResult[0]->ledger_id."' and deleted_at='0000-00-00 00:00:00'");
						DB::commit();
					}
				}
			}
		}
		if($keyValueStringAmt!="")
		{
			DB::beginTransaction();
			$ledgerTrnData = DB::connection($databaseName)->statement("update ".$ledgerId."_ledger_dtl 
			set ".$keyValueStringAmt."updated_at='".$mytime."'
			where ledger_id = '".$ledgerId."' and deleted_at='0000-00-00 00:00:00' and balance_flag='opening'");
			DB::commit();
			
		}
		if($keyValueString!="")
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update ledger_mst 
			set ".$keyValueString."updated_at='".$mytime."'
			where ledger_id = '".$ledgerId."' and deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		if($keyValueStringAmt!="" && $ledgerTrnData==1 || $keyValueString!="" && $raw==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get All data 
	 * returns the status
	*/
	public function getAllData()
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$ledgerAllData = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id
		from ledger_mst where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($ledgerAllData)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$ledgerIdArray = array();
			$mergeArray = array();
			for($ledgerDataArray=0;$ledgerDataArray<count($ledgerAllData);$ledgerDataArray++)
			{
				$ledgerIdArray[$ledgerDataArray] = $ledgerAllData[$ledgerDataArray]->ledger_id;
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
						if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
					$currentBalanceType = "credit";
				}
				else
				{
					$amountData = $debitAmountArray-$creditAmountArray;
					$currentBalanceType = "debit";
				}
				$balanceAmountArray = array();
				$balanceAmountArray['openingBalance'] = $raw[0]->amount;
				$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
				$balanceAmountArray['currentBalance'] = $amountData;
				$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
				$mergeArray[$ledgerDataArray] = (Object)array_merge((array)$ledgerAllData[$ledgerDataArray],(array)((Object)$balanceAmountArray));
			}
			$enocodedData = json_encode($mergeArray);
			return $enocodedData;
		}
	}
	
	/**
	 * get data as per given Ledger Id
	 * @param $ledgerId
	 * returns the status
	*/
	public function getData($ledgerId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$ledgerData = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		client_id,
		client_name,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id
		from ledger_mst where ledger_id = ".$ledgerId." and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($ledgerData)==0)
		{
			
			return $exceptionArray['404'];
		}
		else
		{
			$ledgerId = $ledgerData[0]->ledger_id;
			$currentBalanceType="";
			
			//get opening balance
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			".$ledgerId."_id,
			amount,
			amount_type
			from ".$ledgerId."_ledger_dtl
			WHERE balance_flag='opening' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($raw)!=0)
			{
				//get current balance
				DB::beginTransaction();
				$ledgerResult = DB::connection($databaseName)->select("SELECT 
				".$ledgerId."_id,
				amount,
				amount_type
				from ".$ledgerId."_ledger_dtl
				WHERE deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				
				$creditAmountArray =0;
				$debitAmountArray = 0;
				for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
				{
					if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
				$currentBalanceType = "credit";
			}
			else
			{
				$amountData = $debitAmountArray-$creditAmountArray;
				$currentBalanceType = "debit";
			}
			$balanceAmountArray = array();
			$balanceAmountArray['openingBalance'] = $raw[0]->amount;
			$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
			$balanceAmountArray['currentBalance'] = $amountData;
			$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
			$mergeArray = (Object)array_merge((array)$ledgerData[0],(array)((Object)$balanceAmountArray));
			$enocodedData = json_encode($mergeArray,true); 	
			return $enocodedData;
		}
	}
	/**
	 * get All data 
	 * returns the status
	*/
	public function getAllLedgerData($ledgerGrpId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$ledgerAllData = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id
		from ledger_mst where ledger_group_id ='".$ledgerGrpId."' and  deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($ledgerAllData)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$ledgerIdArray = array();
			$mergeArray = array();
			for($ledgerDataArray=0;$ledgerDataArray<count($ledgerAllData);$ledgerDataArray++)
			{
				$ledgerIdArray[$ledgerDataArray] = $ledgerAllData[$ledgerDataArray]->ledger_id;
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
						if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
					$currentBalanceType = "credit";
				}
				else
				{
					$amountData = $debitAmountArray-$creditAmountArray;
					$currentBalanceType = "debit";
				}
				$balanceAmountArray = array();
				$balanceAmountArray['openingBalance'] = $raw[0]->amount;
				$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
				$balanceAmountArray['currentBalance'] = $amountData;
				$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
				$mergeArray[$ledgerDataArray] = (Object)array_merge((array)$ledgerAllData[$ledgerDataArray],(array)((Object)$balanceAmountArray));
			}
			$enocodedData = json_encode($mergeArray);
			return $enocodedData;
		}
	}
	
	/**
	 * get All data 
	 * returns the status
	*/
	public function getLedgerDetail($companyId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$ledgerAllData = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id
		from ledger_mst where company_id ='".$companyId."' and  deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($ledgerAllData)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$ledgerIdArray = array();
			$mergeArray = array();
			for($ledgerDataArray=0;$ledgerDataArray<count($ledgerAllData);$ledgerDataArray++)
			{
				$ledgerIdArray[$ledgerDataArray] = $ledgerAllData[$ledgerDataArray]->ledger_id;
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
						if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
					$currentBalanceType = "credit";
				}
				else
				{
					$amountData = $debitAmountArray-$creditAmountArray;
					$currentBalanceType = "debit";
				}
				$balanceAmountArray = array();
				$balanceAmountArray['openingBalance'] = $raw[0]->amount;
				$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
				$balanceAmountArray['currentBalance'] = $amountData;
				$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
				$mergeArray[$ledgerDataArray] = (Object)array_merge((array)$ledgerAllData[$ledgerDataArray],(array)((Object)$balanceAmountArray));
			}
			$enocodedData = json_encode($mergeArray);
			return $enocodedData;
		}
	}
	
	/**
	 * get All data 
	 * returns the status
	*/
	public function getLedgerTransactionDetail($ledgerId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();		
		$data = DB::connection($databaseName)->select("select 
		".$ledgerId."_id,
		amount,
		amount_type,
		entry_date,
		jf_id,
		created_at,
		updated_at,
		ledger_id
		from ".$ledgerId."_ledger_dtl where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if(count($data)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$ledgerId = $data[0]->ledger_id;
			$currentBalanceType="";
			
			//get opening balance
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			".$ledgerId."_id,
			amount,
			amount_type
			from ".$ledgerId."_ledger_dtl
			WHERE balance_flag='opening' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($raw)!=0)
			{
				//get current balance
				DB::beginTransaction();
				$ledgerResult = DB::connection($databaseName)->select("SELECT 
				".$ledgerId."_id,
				amount,
				amount_type
				from ".$ledgerId."_ledger_dtl
				WHERE deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				
				$creditAmountArray =0;
				$debitAmountArray = 0;
				for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
				{
					if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
				$currentBalanceType = "credit";
			}
			else
			{
				$amountData = $debitAmountArray-$creditAmountArray;
				$currentBalanceType = "debit";
			}
			
			$balanceAmountArray = array();
			$balanceAmountArray['openingBalance'] = $raw[0]->amount;
			$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
			$balanceAmountArray['currentBalance'] = $amountData;
			$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
			$mergeArray = array();
			for($arrayData=0;$arrayData<count($data);$arrayData++)
			{
				$mergeArray[$arrayData] = (Object)array_merge((array)$data[$arrayData],(array)((Object)$balanceAmountArray));
			}
			
			$enocodedData = json_encode($mergeArray);
			return $enocodedData;
		}
	}
	
	/**
	 * get data 
	 * @param  from-date and to-date
	 * get data between given date
	 * returns the error-message/data
	*/
	public function getLedgerData($fromDate,$toDate,$companyId,$ledgerType)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		if(strcmp($ledgerType,"all")==0)
		{
			DB::beginTransaction();
			$data = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE (ledger_name='retail_sales' OR 
			ledger_name='whole_sales') and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else
		{
			DB::beginTransaction();
			$data = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE ledger_name='".$ledgerType."' and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($data)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$mergeArray = array();
			for($ledgerData=0;$ledgerData<count($data);$ledgerData++)
			{				
				DB::beginTransaction();
				$ledgerAllData = DB::connection($databaseName)->select("SELECT 
				".$data[$ledgerData]->ledger_id."_id,
				amount,
				amount_type,
				entry_date,
				jf_id,
				created_at,
				updated_at,
				ledger_id
				FROM ".$data[$ledgerData]->ledger_id."_ledger_dtl
				WHERE (entry_date BETWEEN '".$fromDate."' AND '".$toDate."') and 
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				
				if(count($ledgerAllData)!=0)
				{
					$ledgerId = $data[$ledgerData]->ledger_id;
					$currentBalanceType="";
					//get opening balance
					DB::beginTransaction();
					$raw = DB::connection($databaseName)->select("SELECT 
					".$ledgerId."_id,
					amount,
					amount_type
					from ".$ledgerId."_ledger_dtl
					WHERE balance_flag='opening' and 
					deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					if(count($raw)!=0)
					{
						//get current balance
						DB::beginTransaction();
						$ledgerResult = DB::connection($databaseName)->select("SELECT 
						".$ledgerId."_id,
						amount,
						amount_type
						from ".$ledgerId."_ledger_dtl
						WHERE deleted_at='0000-00-00 00:00:00'");
						DB::commit();
						
						$creditAmountArray =0;
						$debitAmountArray = 0;
						for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
						{
							if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
						$currentBalanceType = "credit";
					}
					else
					{
						$amountData = $debitAmountArray-$creditAmountArray;
						$currentBalanceType = "debit";
					}
					
					$balanceAmountArray = array();
					$balanceAmountArray['openingBalance'] = $raw[0]->amount;
					$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
					$balanceAmountArray['currentBalance'] = $amountData;
					$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
					for($arrayData=0;$arrayData<count($ledgerAllData);$arrayData++)
					{
						array_push($mergeArray,(Object)array_merge((array)$ledgerAllData[$arrayData],(array)((Object)$balanceAmountArray)));
					}
				}
			}
			if(empty($mergeArray))
			{
				return $exceptionArray['404'];
			}
			else
			{
				return json_encode($mergeArray);
			}
		}
	}
	
	/**
	 * get ledger id 
	 * @param company-id,ledger-name
	 * returns the error-message/data
	*/
	public function getUserLedgerId($companyId,$userId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
	}
	/**
	 * get ledger id 
	 * @param company-id,ledger-name
	 * returns the error-message/data
	*/
	public function getLedgerId($companyId,$type)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($type,"all")==0)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE (ledger_name='retail_sales' OR 
			ledger_name='whole_sales') and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE ledger_name='".$type."' and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		
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
	 * get ledger id 
	 * @param company-id,ledger-name
	 * returns the error-message/data
	*/
	public function getLedgerDataId($companyId,$type,$contactNo)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($type,"all")==0)
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE (ledger_name='retail_sales' OR 
			ledger_name='whole_sales') and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else
		{
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE ledger_name='".$type."' and 
			company_id='".$companyId."' and 
			contact_no!='".$contactNo."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		
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
	public function getCurrentYearData($companyId,$ledgerType)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		if(strcmp($ledgerType,"all")==0)
		{
			DB::beginTransaction();
			$data = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE (ledger_name='retail_sales' OR 
			ledger_name='whole_sales') and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		else
		{
			DB::beginTransaction();
			$data = DB::connection($databaseName)->select("SELECT 
			ledger_id
			FROM ledger_mst
			WHERE ledger_name='".$ledgerType."' and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(count($data)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$mergeArray = array();
			for($ledgerData=0;$ledgerData<count($data);$ledgerData++)
			{
				DB::beginTransaction();
				$ledgerAllData = DB::connection($databaseName)->select("SELECT 
				".$data[$ledgerData]->ledger_id."_id,
				amount,
				amount_type,
				entry_date,
				jf_id,
				created_at,
				updated_at,
				ledger_id
				FROM ".$data[$ledgerData]->ledger_id."_ledger_dtl
				WHERE YEAR(entry_date)= YEAR(CURDATE()) and 
				deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				if(count($ledgerAllData)!=0)
				{
					$ledgerId = $data[$ledgerData]->ledger_id;
					$currentBalanceType="";
					
					//get opening balance
					DB::beginTransaction();
					$raw = DB::connection($databaseName)->select("SELECT 
					".$ledgerId."_id,
					amount,
					amount_type
					from ".$ledgerId."_ledger_dtl
					WHERE balance_flag='opening' and 
					deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					if(count($raw)!=0)
					{
						//get current balance
						DB::beginTransaction();
						$ledgerResult = DB::connection($databaseName)->select("SELECT 
						".$ledgerId."_id,
						amount,
						amount_type
						from ".$ledgerId."_ledger_dtl
						WHERE deleted_at='0000-00-00 00:00:00'");
						DB::commit();
						
						$creditAmountArray =0;
						$debitAmountArray = 0;
						for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
						{
							if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
						$currentBalanceType = "credit";
					}
					else
					{
						$amountData = $debitAmountArray-$creditAmountArray;
						$currentBalanceType = "debit";
					}
					$balanceAmountArray = array();
					$balanceAmountArray['openingBalance'] = $raw[0]->amount;
					$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
					$balanceAmountArray['currentBalance'] = $amountData;
					$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
					
					for($arrayData=0;$arrayData<count($ledgerAllData);$arrayData++)
					{
						array_push($mergeArray,(Object)array_merge((array)$ledgerAllData[$arrayData],(array)((Object)$balanceAmountArray)));
					}
				}
				else
				{
					return $exceptionArray['404'];
				}
			}
			return json_encode($mergeArray);
			
		}
	}
	
	/**
	 * get data as per company_id and ledger_group id
	 * returns the error-message/data
	*/
	public function getDataAsPerLedgerGrp($ledgerGrpArray,$companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$ledgerGrpArray = func_get_arg(0);
		$companyId = func_get_arg(1);
		$ledgerArray = array();
		$mainResult = array();
		$index=0;
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get data as per companyId and ledger-group-id
		for($ledgerGrpArrayData=0;$ledgerGrpArrayData<count($ledgerGrpArray);$ledgerGrpArrayData++)
		{
			DB::beginTransaction();
			$data = DB::connection($databaseName)->select("SELECT 
			ledger_id,
			ledger_name,
			alias,
			inventory_affected,
			address1,
			address2,
			contact_no,
			email_id,
			is_dealer,
			invoice_number,
			outstanding_limit,
			outstanding_limit_type,
			pan,
			tin,
			cgst,
			sgst,
			bank_id,
			bank_dtl_id,
			micr_code,
			created_at,
			updated_at,
			deleted_at,
			state_abb,
			city_id,
			ledger_group_id,
			company_id
			FROM ledger_mst
			WHERE ledger_group_id='".$ledgerGrpArray[$ledgerGrpArrayData]."' and 
			company_id='".$companyId."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($data)!=0)
			{
				$mainResult[$index] = $data; 
				for($arrayData=0;$arrayData<count($data);$arrayData++)
				{
					$ledgerArray[$index][$arrayData] = $data[$arrayData]->ledger_id;
				}
				$index++;
			}
		}
		//add balance in ledger data
		if(count($ledgerArray)!=0)
		{
			$ledgerIdArray = array();
			$mergeArray = array();
			for($ledgerDataArray=0;$ledgerDataArray<count($ledgerArray);$ledgerDataArray++)
			{
				for($innerArray=0;$innerArray<count($ledgerArray[$ledgerDataArray]);$innerArray++)
				{
					$currentBalanceType="";
					
					//get opening balance
					DB::beginTransaction();
					$raw = DB::connection($databaseName)->select("SELECT 
					".$ledgerArray[$ledgerDataArray][$innerArray]."_id,
					amount,
					amount_type
					from ".$ledgerArray[$ledgerDataArray][$innerArray]."_ledger_dtl
					WHERE balance_flag='opening' and 
					deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					
					if(count($raw)!=0)
					{
						//get current balance
						DB::beginTransaction();
						$ledgerResult = DB::connection($databaseName)->select("SELECT 
						".$ledgerArray[$ledgerDataArray][$innerArray]."_id,
						amount,
						amount_type
						from ".$ledgerArray[$ledgerDataArray][$innerArray]."_ledger_dtl
						WHERE deleted_at='0000-00-00 00:00:00'");
						DB::commit();
						
						$creditAmountArray =0;
						$debitAmountArray = 0;
						for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
						{
							if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
						$currentBalanceType = "credit";
					}
					else
					{
						$amountData = $debitAmountArray-$creditAmountArray;
						$currentBalanceType = "debit";
					}
					$balanceAmountArray = array();
					$balanceAmountArray['openingBalance'] = $raw[0]->amount;
					$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
					$balanceAmountArray['currentBalance'] = $amountData;
					$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
					
					$mergeArray[$ledgerDataArray][$innerArray] = (Object)array_merge((array)$mainResult[$ledgerDataArray][$innerArray],(array)((Object)$balanceAmountArray));
				}
			}
			return $mergeArray;
		}
		else
		{
			return $exceptionArray['404'];
		}
	}
	
	/**
	 * get data as per company_id and ledger_name
	 * returns the error-message/data
	*/
	public function getDataAsPerLedgerName($ledgerName,$companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$ledgerData = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id
		from ledger_mst where company_id ='".$companyId."' and ledger_name='".$ledgerName."' and  deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($ledgerData)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$ledgerId = $ledgerData[0]->ledger_id;
			$currentBalanceType="";
			
			//get opening balance
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->select("SELECT 
			".$ledgerId."_id,
			amount,
			amount_type
			from ".$ledgerId."_ledger_dtl
			WHERE balance_flag='opening' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($raw)!=0)
			{
				//get current balance
				DB::beginTransaction();
				$ledgerResult = DB::connection($databaseName)->select("SELECT 
				".$ledgerId."_id,
				amount,
				amount_type
				from ".$ledgerId."_ledger_dtl
				WHERE deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				
				$creditAmountArray =0;
				$debitAmountArray = 0;
				for($ledgerArrayData=0;$ledgerArrayData<count($ledgerResult);$ledgerArrayData++)
				{
					if(strcmp($ledgerResult[$ledgerArrayData]->amount_type,"credit")==0)
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
				$currentBalanceType = "credit";
			}
			else
			{
				$amountData = $debitAmountArray-$creditAmountArray;
				$currentBalanceType = "debit";
			}
			$balanceAmountArray = array();
			$balanceAmountArray['openingBalance'] = $raw[0]->amount;
			$balanceAmountArray['openingBalanceType'] = $raw[0]->amount_type;
			$balanceAmountArray['currentBalance'] = $amountData;
			$balanceAmountArray['currentBalanceType'] = $currentBalanceType;
			$mergeArray = (Object)array_merge((array)$ledgerData[0],(array)((Object)$balanceAmountArray));
			$enocodedData = json_encode($mergeArray);
			return $enocodedData;
		}
	}
	
	/**
	 * get data as per companyId
	 * @param: companyId,contactNo
	 * returns the error-message/data
	*/
	public function getDataAsPerContactNo($companyId,$contactNo)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$companyIdString = "";
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id 
		from ledger_mst 
		where deleted_at='0000-00-00 00:00:00' and
		contact_no='".$contactNo."' and
		company_id='".$companyId."'");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['500'];
		}
		else
		{
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}/**
	 * get data as per companyId
	 * @param: companyId,userId
	 * returns the error-message/data
	*/
	public function getDataAsPerUserId($companyId,$userId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$companyIdString = "";
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id,
		user_id
		from ledger_mst 
		where deleted_at='0000-00-00 00:00:00' and
		user_id='".$userId."' and
		company_id='".$companyId."'");
		DB::commit();
		if(count($raw)==0)
		{
			return $exceptionArray['500'];
		}
		else
		{
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}
	
	/**
	 * get data as per client-id
	 * @param: client-id
	 * returns the error-message/data
	*/
	public function getDataAsPerClientId($clientId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$companyIdString = "";
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id 
		from ledger_mst 
		where deleted_at='0000-00-00 00:00:00' and
		client_id='".$clientId."'");
		DB::commit();
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
	 * get data as per invoice-number
	 * @param: companyId,invoiceNumber
	 * returns the error-message/data
	*/
	public function getDataAsPerInvoiceNumber($companyId,$invoiceNumber)
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
		ledger_id,
		ledger_name,
		alias,
		inventory_affected,
		address1,
		address2,
		contact_no,
		email_id,
		is_dealer,
		invoice_number,
		outstanding_limit,
		outstanding_limit_type,
		pan,
		tin,
		cgst,
		sgst,
		bank_id,
		bank_dtl_id,
		micr_code,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id,
		ledger_group_id,
		company_id 
		from ledger_mst 
		where company_id='".$companyId."' and 
		invoice_number='".$invoiceNumber."' and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		if(count($raw)==0)
		{
			return $exceptionArray['500'];
		}
		else
		{
			$encodedData = json_encode($raw);
			return $encodedData;
		}
	}
	
	/**
	 * get data as per companyId
	 * @param: companyId
	 * returns the error-message/data
	*/
	public function getLedger($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$raw = array();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$ledgerArray = new LedgerArray();
		$ledgerResult = $ledgerArray->billLedgerArray();
		for($ledgerDataArray=0;$ledgerDataArray<count($ledgerResult);$ledgerDataArray++)
		{
			DB::beginTransaction();
			$raw[$ledgerDataArray] = DB::connection($databaseName)->select("select ledger_id 
			from ledger_mst 
			where company_id='".$companyId."' and 
			ledger_name='".$ledgerResult[$ledgerDataArray]."' and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($raw[$ledgerDataArray])==0)
			{
				return $exceptionArray['500'];
			}
		}
		$encodedData = json_encode($raw);
		return $encodedData;
	}
	
	/**
	 * get data as per given companyId and jfId
	 * @param: companyId and jfId
	 * returns the error-message/data
	*/
	public function getPersonalAccLedgerId($companyId,$jfId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		DB::beginTransaction();
		
		$raw = DB::connection($databaseName)->select("select ledger_id 
		from ledger_mst 
		where ledger_group_id=32 and
		ledger_id IN 
					(select ledger_id from journal_dtl 
					where company_id='".$companyId."' and 
					amount_type='debit' and jf_id='".$jfId."' and 
					deleted_at='0000-00-00 00:00:00') 
		and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if(count($raw)!=0)
		{
			$rawData = json_encode($raw);
			return $rawData;
		}
		else
		{
			return $exceptionArray['404'];
		}
	}
	
	/**
	 * delete the data
	 * @param: ledgerId
	 * returns the error-message/status
	*/
	public function deleteData($ledgerId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update ledger_mst 
		set deleted_at='".$mytime."' 
		where ledger_id=".$ledgerId);
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
}
