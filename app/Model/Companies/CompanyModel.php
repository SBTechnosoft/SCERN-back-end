<?php
namespace ERP\Model\Companies;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\EnumClasses\IsDefaultEnum;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Settings\Templates\Entities\TemplateDesign;
use ERP\Core\Settings\Templates\Entities\TemplateTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyModel extends Model
{
	/**
	 * insert data with document
	 * returns the status
	*/
	public function insertAllData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$constantArray = $constantDatabase->constantVariable();
		
		$getCompanyData = array();
		$getCompanyKey = array();
		$getCompanyData = func_get_arg(0);
		$getCompanyKey = func_get_arg(1);
		$getDocumentData = func_get_arg(2);
		$companyData="";
		$keyName = "";
		for($data=0;$data<count($getCompanyData);$data++)
		{
			if($data == (count($getCompanyData)-1))
			{
				$companyData = $companyData."'".$getCompanyData[$data]."'";
				$keyName =$keyName.$getCompanyKey[$data];
			}
			else
			{
				$companyData = $companyData."'".$getCompanyData[$data]."',";
				$keyName =$keyName.$getCompanyKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into company_mst(".$keyName.",document_name,document_size,document_format,created_at) 
		values(".$companyData.",'".$getDocumentData[0][0]."','".$getDocumentData[0][1]."','".$getDocumentData[0][2]."','".$mytime."')");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			DB::beginTransaction();
			$companyId = DB::connection($databaseName)->select("SELECT 
			company_id,
			company_name,
			state_abb,
			city_id
			FROM `company_mst` 
			where deleted_at='0000-00-00 00:00:00'
			ORDER by company_id DESC limit 1");
			DB::commit();
			
			//branch insertion
			DB::beginTransaction();
			$branchInserion = DB::connection($databaseName)->statement("INSERT 
			into branch_mst(
			branch_name,
			address1,
			address2,
			pincode, 
			is_display, 
			is_default,
			updated_at,
			deleted_at,
			state_abb,
			city_id, 
			company_id)
			VALUES(
			'MainBranch',
			'address1',
			'address2', 
			'395000', 
			'yes',
			'not',
			'0000-00-00 00:00:00',
			'0000-00-00 00:00:00',
			'".$companyId[0]->state_abb."',
			'".$companyId[0]->city_id."', 
			'".$companyId[0]->company_id."')
			");
			DB::commit();
			
			$templateInsertionResult = $this->templateInsertion($companyId[0]->company_name,$companyId[0]->company_id);
			if(strcmp($templateInsertionResult,$exceptionArray['500'])==0)
			{
				return $exceptionArray['500'];
			}
			return $companyId;
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * insert only data 
	 * returns the status
	*/
	public function insertData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$constantArray = $constantDatabase->constantVariable();
		
		$getCompanyData = array();
		$getCompanyKey = array();
		$getCompanyData = func_get_arg(0);
		$getCompanyKey = func_get_arg(1);
		$companyData="";
		$keyName = "";
		for($data=0;$data<count($getCompanyData);$data++)
		{
			if($data == (count($getCompanyData)-1))
			{
				$companyData = $companyData."'".$getCompanyData[$data]."'";
				$keyName =$keyName.$getCompanyKey[$data];
			}
			else
			{
				$companyData = $companyData."'".$getCompanyData[$data]."',";
				$keyName =$keyName.$getCompanyKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into company_mst(".$keyName.",created_at) 
		values(".$companyData.",'".$mytime."')");
		DB::commit();
		
		//get latest company_id
		DB::beginTransaction();
		$latestCompanyId = DB::connection($databaseName)->select("SELECT 
		company_id,
		company_name,
		state_abb,
		city_id
		FROM `company_mst` 
		where deleted_at='0000-00-00 00:00:00'
      	ORDER by company_id DESC limit 1");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			DB::beginTransaction();
			$branchInsertion = DB::connection($databaseName)->statement("INSERT 
			into branch_mst(
			branch_name,
			address1,
			address2,
			pincode, 
			is_display, 
			is_default,
			created_at,
			updated_at,
			deleted_at,
			state_abb,
			city_id, 
			company_id)
			VALUES(
			'MainBranch',
			'address1',
			'address2', 
			'395000', 
			'yes',
			'not',
			'".$mytime."',
			'0000-00-00 00:00:00',
			'0000-00-00 00:00:00',
			'".$latestCompanyId[0]->state_abb."',
			'".$latestCompanyId[0]->city_id."', 
			'".$latestCompanyId[0]->company_id."')
			");
			DB::commit();

			$templateInsertionResult = $this->templateInsertion($latestCompanyId[0]->company_name,$latestCompanyId[0]->company_id);
			if(strcmp($templateInsertionResult,$exceptionArray['500'])==0)
			{
				return $exceptionArray['500'];
			}
			
			// DB::beginTransaction();
			// $companyId = DB::connection($databaseName)->select("select
			// max(company_id) as company_id 
			// from company_mst where deleted_at='0000-00-00 00:00:00'");
			// DB::commit();
			return $latestCompanyId;
		}
		else
		{
			return $exceptionArray['500'];
		}
	}

	/**
	 * insert template data 
	 * @param company_id,company-name
	 * returns the status
	*/
	public function templateInsertion($companyName,$companyId)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$constantTemplateData = $constantDatabase->templateConstants();

		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//get Template
		$templateDesign = new TemplateDesign();
		$templateArray = $templateDesign->getTemplate();

		$templateEnum = new TemplateTypeEnum();
		$templateData = $templateEnum->templateName();
		$templateCount = count($templateData);
		for($templateArrayData=0;$templateArrayData<$templateCount;$templateArrayData++)
		{
			
			DB::beginTransaction();
			$templateInsertion = DB::connection($databaseName)->statement("insert
			into template_mst(
			template_name,
			template_body,
			template_type,
			company_id,
			created_at)
			values(
			'".$companyName.' '.$templateData[$templateArrayData]."',
			'".$templateArray[$templateData[$templateArrayData]]."',
			'".$constantTemplateData[$templateData[$templateArrayData]]."',
			'".$companyId."',
			'".$mytime."')");
			DB::commit();
			if($templateInsertion!=1)
			{
				return $exceptionArray['500'];
			}
		}
		return $exceptionArray['200'];
	}

	/**
	 * update data 
	 * @param company_id,company-data,key of company-data and document-data
	 * returns the status
	*/
	public function updateData($companyData,$key,$companyId,$documentData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// only one company is checked by default
		$enumIsDefArray = array();
		$isDefEnum = new IsDefaultEnum();
		$enumIsDefArray = $isDefEnum->enumArrays();
		$isDefaultString='';
		for($keyData=0;$keyData<count($key);$keyData++)
		{
		    if(strcmp($key[array_keys($key)[$keyData]],"is_default")==0)
			{
				$raw  = DB::connection($databaseName)->statement("update company_mst 
				set is_default='".$enumIsDefArray['notDefault']."',updated_at='".$mytime."' 
				where deleted_at = '0000-00-00 00:00:00'");
				if($raw==0)
				{
					return $exceptionArray['500'];
				}
			}	
		}
		
		for($data=0;$data<count($companyData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$companyData[$data]."',";
		}
		$raw  = DB::connection($databaseName)->statement("update company_mst 
		set ".$keyValueString."updated_at='".$mytime."',
		'".$isDefaultString."'
		document_name='".$documentData[0][0]."',
		document_size='".$documentData[0][1]."',
		document_format='".$documentData[0][2]."' 
		where company_id = '".$companyId."' and deleted_at='0000-00-00 00:00:00'");
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
	 * update data 
	 * @param company_id,company-data,key of company-data
	 * returns the status
	*/
	public function updateCompanyData($companyData,$key,$companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		// only one company is checked by default
		$enumIsDefArray = array();
		$isDefEnum = new IsDefaultEnum();
		$enumIsDefArray = $isDefEnum->enumArrays();
		for($keyData=0;$keyData<count($key);$keyData++)
		{
		    if(strcmp($key[array_keys($key)[$keyData]],"is_default")==0)
			{
				if(strcmp($companyData[$keyData],$enumIsDefArray['default'])==0)
				{
					$raw  = DB::connection($databaseName)->statement("update company_mst 
					set is_default='".$enumIsDefArray['notDefault']."',updated_at='".$mytime."' 
					where deleted_at = '0000-00-00 00:00:00'");
					if($raw==0)
					{
						return $exceptionArray['500'];
					}
				}
			}	
		}
		for($data=0;$data<count($companyData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$companyData[$data]."',";
		}
		$raw  = DB::connection($databaseName)->statement("update company_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where company_id = '".$companyId."' and deleted_at='0000-00-00 00:00:00'");
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
	 * update data 
	 * @param company_id,company-data,key of company-data
	 * returns the status
	*/
	public function updateDocumentData($companyId,$documentData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$raw  = DB::connection($databaseName)->statement("update company_mst 
		set document_name='".$documentData[0][0]."',
		document_size='".$documentData[0][1]."',
		document_format='".$documentData[0][2]."',
		updated_at='".$mytime."'
		where company_id = '".$companyId."' and deleted_at='0000-00-00 00:00:00'");
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
		$raw = DB::connection($databaseName)->select("select 
		company_id,
		company_name,
		company_display_name,
		website_name,
		address1,
		address2,
		email_id,
		customer_care,
		pincode,
		pan,
		tin,
		vat_no,
		cgst,
		sgst,
		cess,
		service_tax_no,
		basic_currency_symbol,
		formal_name,
		no_of_decimal_points,
		currency_symbol,
		document_name,
		document_size,
		document_format,
		is_display,
		is_default,
		print_type,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id 
		from company_mst where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(count($raw)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$enocodedData = json_encode($raw);
			return $enocodedData;
		}
	}
	
	/**
	 * get data as per given Company Id
	 * @param $companyId
	 * returns the status
	*/
	public function getData($companyId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		company_id,
		company_name,
		company_display_name,
		website_name,
		address1,
		address2,
		email_id,
		customer_care,
		pincode,
		pan,
		tin,
		vat_no,
		cgst,
		sgst,
		cess,
		service_tax_no,
		basic_currency_symbol,
		formal_name,
		no_of_decimal_points,
		currency_symbol,
		document_name,
		document_size,
		document_format,
		is_display,
		is_default,
		print_type,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id 
		from company_mst where company_id = '".$companyId."' and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$enocodedData = json_encode($raw,true); 	
			return $enocodedData;
		}
	}
	
	/**
	 * get particular company data
	 * @param $companyName
	 * returns the status
	*/
	public function getCompanyName($companyName)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		company_id
		from company_mst 
		where company_name = '".$companyName."' and 
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($raw)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{	
			return $raw;
		}
	}
	
	/**
	 * get data as per given Comapany-Name
	 * @param $companyName
	 * returns the error-message/companyId
	*/
	public function getCompanyId($companyName)
	{
		$flag=0;
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$companyResult = DB::connection($databaseName)->select("SELECT 
		company_id,
		company_name
		from company_mst 
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		for($dataArray=0;$dataArray<count($companyResult);$dataArray++)
		{
			$convertedCompanyString = strtoupper($companyResult[$dataArray]->company_name);
			//replace string of db group-name
			$convertedDbCompanyString = preg_replace('/[^A-Za-z0-9]/', '',$convertedCompanyString);
			if(strcmp($convertedDbCompanyString,$companyName)==0)
			{
				$flag=1;
				$companyId = $companyResult[$dataArray]->company_id;
				break;
			}
		}
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($flag==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			return $companyId;
		}
	}
	
	//delete
	public function deleteData($companyId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$raw = DB::connection($databaseName)->statement("update company_mst 
		set deleted_at='".$mytime."' 
		where company_id=".$companyId);
		DB::commit();
		
		$mytime = Carbon\Carbon::now();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$conversationDtl = DB::connection($databaseName)->statement("update conversation_dtl 
		set deleted_at='".$mytime."' 
		where company_id=".$companyId);
		DB::commit();
		if($raw==1)
		{
			$ledgerId = DB::connection($databaseName)->select("select ledger_id 
			from ledger_mst 
			where company_id=".$companyId." and deleted_at='0000-00-00 00:00:00'");
			$userId = DB::connection($databaseName)->select("select user_id 
			from user_mst 
			where company_id=".$companyId." and user_type!='superadmin' and deleted_at='0000-00-00 00:00:00'");
			
			$branch = DB::connection($databaseName)->statement("update branch_mst 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);

			$productData = DB::connection($databaseName)->select("select product_id 
			from product_mst 
			where company_id=".$companyId." and deleted_at='0000-00-00 00:00:00'");
			$product = DB::connection($databaseName)->statement("update product_mst 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);

			$productCount = count($productData);
			for($productIndex=0;$productIndex<$productCount;$productIndex++)
			{
				$productDocDtl = DB::connection($databaseName)->statement("update product_doc_dtl 
				set deleted_at='".$mytime."' 
				where product_id=".$productData[$productIndex]->product_id);
			}
			$template = DB::connection($databaseName)->statement("update template_mst 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$invoice = DB::connection($databaseName)->statement("update invoice_dtl 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			
			$quotation = DB::connection($databaseName)->statement("update quotation_dtl 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$quotationArchive = DB::connection($databaseName)->statement("update quotation_bill_archives
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			
			$quotationData = DB::connection($databaseName)->select("select quotation_bill_id 
			from quotation_bill_dtl 
			where company_id=".$companyId." and deleted_at='0000-00-00 00:00:00'");
			$quotationBillDtl = DB::connection($databaseName)->statement("update quotation_bill_dtl 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$quotationCount = count($quotationData);
			for($quotationIndex=0;$quotationIndex<$quotationCount;$quotationIndex++)
			{
				$quotationBillDocDtl = DB::connection($databaseName)->statement("update quotation_bill_doc_dtl 
				set deleted_at='".$mytime."' 
				where quotation_bill_id=".$quotationData[$quotationIndex]->quotation_bill_id);

			}
			$journal = DB::connection($databaseName)->statement("update journal_dtl 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			
			$productTrn = DB::connection($databaseName)->statement("update product_trn 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$productTrnSummary = DB::connection($databaseName)->statement("update product_trn_summary 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$saleBillData = DB::connection($databaseName)->select("select sale_id 
			from sales_bill 
			where company_id=".$companyId." and deleted_at='0000-00-00 00:00:00'");
			$retailsalesDtl = DB::connection($databaseName)->statement("update sales_bill
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$retailsalesTrnDtl = DB::connection($databaseName)->statement("update sales_bill_trn
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$saleBillDataCount = count($saleBillData);
			for($saleBillDataIndex=0;$saleBillDataIndex<$saleBillDataCount;$saleBillDataIndex++)
			{
				$retailsalesDocDtl = DB::connection($databaseName)->statement("update sales_bill_doc_dtl
				set deleted_at='".$mytime."' 
				where sale_id=".$saleBillData[$saleBillDataIndex]->sale_id);

				$saleExpenseDtl = DB::connection($databaseName)->statement("update sale_expense_dtl
				set deleted_at='".$mytime."' 
				where sale_id=".$saleBillData[$saleBillDataIndex]->sale_id);
			}
			$purchaseBillData = DB::connection($databaseName)->select("select purchase_id 
			from purchase_bill 
			where company_id=".$companyId." and deleted_at='0000-00-00 00:00:00'");
			$purchaseDtl = DB::connection($databaseName)->statement("update purchase_bill
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$purchaseBillDataCount = count($purchaseBillData);
			for($purchaseBillDataIndex=0;$purchaseBillDataIndex<$purchaseBillDataCount;$purchaseBillDataIndex++)
			{
				$purchaseDocDtl = DB::connection($databaseName)->statement("update purchase_doc_dtl
				set deleted_at='".$mytime."' 
				where purchase_id=".$purchaseBillData[$purchaseBillDataIndex]->purchase_id);

				$purchaseExpenseDtl = DB::connection($databaseName)->statement("update purchase_expense_dtl
				set deleted_at='".$mytime."' 
				where purchase_id=".$purchaseBillData[$purchaseBillDataIndex]->purchase_id);
			}
			$jobCard = DB::connection($databaseName)->select("select job_card_id 
			from job_card_dtl 
			where company_id=".$companyId." and deleted_at='0000-00-00 00:00:00'");
			$jobCardDtl = DB::connection($databaseName)->statement("update job_card_dtl
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			$jobCardCount = count($jobCard);
			for($jobCardIndex=0;$jobCardIndex<$jobCardCount;$jobCardIndex++)
			{
				$jobCardDtl = DB::connection($databaseName)->statement("update job_card_number_dtl
				set deleted_at='".$mytime."' 
				where company_id=".$companyId);

				$jobCardDocDtl = DB::connection($databaseName)->statement("update job_card_doc_dtl
				set deleted_at='".$mytime."' 
				where job_card_id=".$jobCard[$jobCardIndex]->job_card_id);
			}

			if(count($userId) > 0)
			{
				$userCount = count($userId);
				//delete from active_session
				for($userData=0;$userData<$userCount;$userData++)
				{
					DB::beginTransaction();
					$userMst = DB::connection($databaseName)->statement("update user_mst
					set deleted_at='".$mytime."' 
					where company_id=".$companyId." and user_id='".$userId[$userData]->user_id."'");
					DB::commit();
					 
					DB::beginTransaction();
					$activeSession = DB::connection($databaseName)->statement("delete
					from active_session
					where user_id='".$userId[$userData]->user_id."'");
					DB::commit();
				}
			}
			
			$ledgerCount = count($ledgerId);
			//ledegerId_ledger_dtl drop
			for($ledgerArray=0;$ledgerArray<$ledgerCount;$ledgerArray++)
			{
				DB::beginTransaction();
				$dropLedger = DB::connection($databaseName)->statement("drop table
				".$ledgerId[$ledgerArray]->ledger_id."_ledger_dtl");
				DB::commit();

				DB::beginTransaction();
				$balanceSheetDtl = DB::connection($databaseName)->statement("update balance_sheet_dtl
				set deleted_at='".$mytime."' 
				where ledger_id=".$ledgerId[$ledgerArray]->ledger_id);
				DB::commit();

				DB::beginTransaction();
				$cashFlowDtl = DB::connection($databaseName)->statement("update cash_flow_dtl
				set deleted_at='".$mytime."' 
				where ledger_id=".$ledgerId[$ledgerArray]->ledger_id);
				DB::commit();
				
				DB::beginTransaction();
				$trialBalanceDtl = DB::connection($databaseName)->statement("update trial_balance_dtl
				set deleted_at='".$mytime."' 
				where ledger_id=".$ledgerId[$ledgerArray]->ledger_id);
				DB::commit();

				DB::beginTransaction();
				$profitLossDtl = DB::connection($databaseName)->statement("update profit_loss_dtl
				set deleted_at='".$mytime."' 
				where ledger_id=".$ledgerId[$ledgerArray]->ledger_id);
				DB::commit();
			}
			$ledger = DB::connection($databaseName)->statement("update ledger_mst 
			set deleted_at='".$mytime."' 
			where company_id=".$companyId);
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
}