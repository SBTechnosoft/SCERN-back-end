<?php
namespace ERP\Model\Clients;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Clients\Entities\ClientArray;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Model\Crm\JobForm\JobFormModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ClientModel extends Model
{
	protected $table = 'client_mst';
	
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
		
		$getClientData = array();
		$getClientKey = array();
		$getClientData = func_get_arg(0);
		$getClientKey = func_get_arg(1);
		$clientData="";
		$keyName = "";
		for($index=0;$index<count($getClientKey);$index++)
		{
			if(strcmp($getClientKey[$index],'is_display')==0)
			{
				$dataIndex = $index;
				break;
			}
		}
		
		//set is_display yes(by_default)
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($getClientData[$dataIndex],"")==0)
		{
			$getClientData[$dataIndex]=$enumIsDispArray['display'];
		}
		for($data=0;$data<count($getClientData);$data++)
		{
			if($data == (count($getClientData)-1))
			{
				$clientData = $clientData."'".$getClientData[$data]."'";
				$keyName =$keyName.$getClientKey[$data];
			}
			else
			{
				$clientData = $clientData."'".$getClientData[$data]."',";
				$keyName =$keyName.$getClientKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into client_mst(".$keyName.",created_at) 
		values(".$clientData.",'".$mytime."')");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			DB::beginTransaction();
			$clientData = DB::connection($databaseName)->select("select 
			client_id,
			client_name,
			company_name,
			contact_no,
			contact_no1,
			email_id,
			address1,
			profession_id,
			birth_date,
			anniversary_date,
			other_date,
			credit_limit,
			credit_days,
			gst,
			is_display,
			created_at,
			updated_at,
			deleted_at,
			state_abb,
			city_id
			from client_mst where client_id = (select max(client_id) from client_mst) and deleted_at='0000-00-00 00:00:00'");
			DB::commit();			
			//get data from client-document
			DB::beginTransaction();
			$clientDocumentData = DB::connection($databaseName)->select("select 
			document_id,
			client_id,
			sale_id,
			document_name,
			document_size,
			document_format,
			document_type,
			created_at,
			updated_at,
			deleted_at
			from client_doc_dtl where client_id = '".$clientData[0]->client_id."' and deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			$clientArraydata = array();
			$clientArraydata['clientData'] = $clientData;
			$clientArraydata['clientDocumentData'] = $clientDocumentData;
			return json_encode($clientArraydata);
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get data as per given Client Id
	 * @param $clientId
	 * returns the status
	*/
	public function getData($clientId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$clientData = DB::connection($databaseName)->select("select 
		client_id,
		client_name,
		company_name,
		contact_no,
		contact_no1,
		email_id,
		address1,
		profession_id,
		birth_date,
		anniversary_date,
		other_date,
		credit_limit,
		credit_days,
		gst,
		is_display,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id
		from client_mst where client_id = ".$clientId." and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($clientData)==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			//get data from client-document
			DB::beginTransaction();
			$clientDocumentData = DB::connection($databaseName)->select("select 
			document_id,
			client_id,
			sale_id,
			document_name,
			document_size,
			document_format,
			document_type,
			created_at,
			updated_at,
			deleted_at
			from client_doc_dtl where client_id = '".$clientData[0]->client_id."' and deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			$clientArraydata = array();
			$clientArraydata['clientData'] = $clientData;
			$clientArraydata['clientDocumentData'] = $clientDocumentData;
			$enocodedData = json_encode($clientArraydata,true); 	
			return $enocodedData;
		}
	}
	
	/**
	 * get All data 
	 * returns the status
	*/
	public function getAllData($headerData,$processedData=null,$identifyFlag)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$billFlag=0;
		$jobFormFlag=0;
		$invoiceDateFlag=0;
		$jobCardDateFlag=0;
		$queryParameter="";
		$billModel = new BillModel();
		$jobFormModel = new JobFormModel();
		if(array_key_exists('invoicenumber',$headerData) && $headerData['invoicenumber'][0]!='')
		{
			$billResult = $billModel->getInvoiceNumberData($headerData['invoicenumber'][0]);
			if(strcmp($billResult,$exceptionArray['204'])!=0)
			{
				$decodedBillData = json_decode($billResult);
				$billFlag=1;
				$queryParameter = $queryParameter." client_id IN('".$decodedBillData[0]->client_id."'";
			}
		}
		if(array_key_exists('jobcardnumber',$headerData) && $headerData['jobcardnumber'][0]!='')
		{
			$jobFormResult = $jobFormModel->getData($headerData['jobcardnumber'][0]);
			if(strcmp($jobFormResult,$exceptionArray['204'])!=0)
			{
				$decodedJobCardData = json_decode($jobFormResult);
				if($billFlag==1)
				{
					$queryParameter = $queryParameter.",'".$decodedJobCardData[0]->client_id."'";
				}
				else
				{
					$queryParameter = $queryParameter." client_id IN('".$decodedJobCardData[0]->client_id."'";
				}
				$jobFormFlag=1;
			}
		}
		if(array_key_exists('invoicefromdate',$headerData) && array_key_exists('invoicetodate',$headerData))
		{
			$invoiceFromDate = $processedData->invoicefromdate;
			$invoiceToDate = $processedData->invoicetodate;
			$billResult = $billModel->getFromToDateData($invoiceFromDate,$invoiceToDate);
			if(strcmp($billResult,$exceptionArray['204'])!=0)
			{
				$decodedJsonData = json_decode($billResult);
				if($billFlag!=1 && $jobFormFlag!=1)
				{
					$queryParameter = $queryParameter." client_id IN(";
				}
				for($arrayData=0;$arrayData<count($decodedJsonData);$arrayData++)
				{
					$invoiceDateFlag=1;
					if(($billFlag==1 || $jobFormFlag==1) && $arrayData==0)
					{
						$queryParameter = $queryParameter.",'".$decodedJsonData[$arrayData]->client_id."',";
					}
					else
					{
						$queryParameter = $queryParameter.$decodedJsonData[$arrayData]->client_id.",";
					}
				}
			}
		}
		if(array_key_exists('jobcardfromdate',$headerData) && array_key_exists('jobcardtodate',$headerData))
		{
			$jobcardFromDate = $processedData->jobcardfromdate;
			$jobcardToDate = $processedData->jobcardfromdate;
			$jobCardResult = $jobFormModel->getFromToDateData($jobcardFromDate,$jobcardToDate);
			if(strcmp($jobCardResult,$exceptionArray['204'])!=0)
			{
				$decodedJsonData = json_decode($jobCardResult);
				if($billFlag!=1 && $jobFormFlag!=1 && $invoiceDateFlag!=1)
				{
					$queryParameter = $queryParameter." client_id IN(";
				}
				for($arrayData=0;$arrayData<count($decodedJsonData);$arrayData++)
				{
					$jobCardDateFlag=1;
					if(($billFlag==1 || $jobFormFlag==1) && $arrayData==0 && $invoiceDateFlag!=1)
					{
						$queryParameter = $queryParameter.",'".$decodedJsonData[$arrayData]->client_id."',";
					}
					else
					{
						$queryParameter = $queryParameter.$decodedJsonData[$arrayData]->client_id.",";
					}
				}
			}
		}	
		if($invoiceDateFlag==1 || $jobCardDateFlag==1)
		{
			$queryParameter = rtrim($queryParameter,",");
			$queryParameter = $queryParameter.") And ";
		}
		else if($billFlag==1 || $jobFormFlag==1)
		{
			$queryParameter = $queryParameter.") And ";
		}
		//simple data searching(without date)
		$clientArray = new ClientArray();
		$clientArrayData = $clientArray->searchClientData();
		$clientDataFlag=0;
		for($dataArray=0;$dataArray<count($clientArrayData);$dataArray++)
		{
			$key = $clientArrayData[array_keys($clientArrayData)[$dataArray]];
			$queryKey = array_keys($clientArrayData)[$dataArray];
			
			if(array_key_exists($clientArrayData[array_keys($clientArrayData)[$dataArray]],$headerData))
			{
				//address like query pending
				if(strcmp('address',$clientArrayData[array_keys($clientArrayData)[$dataArray]])==0)
				{
					$clientDataFlag=1;
					$queryParameter = $queryParameter."".$queryKey." LIKE '%".$headerData[$key][0]."%' OR ";
				}	
				else
				{
					$clientDataFlag=1;
					$queryParameter = $queryParameter."".$queryKey."='".$headerData[$key][0]."' OR ";		
				}
			}
		}
		if($queryParameter!='' && $clientDataFlag==1)
		{
			$queryParameter = rtrim($queryParameter,"OR ");
			$queryParameter= $queryParameter." and";
		}
		if($queryParameter=='' && $identifyFlag==1)
		{
			return $exceptionArray['204'];
		}
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$clientIdParam='';
		$clientFlag=0;
		if(array_key_exists('operation',$headerData))
		{
			$clientFlag=1;
			DB::beginTransaction();		
			$clientAllData = DB::connection($databaseName)->select("select 
			client_id,
			birth_date,
			anniversary_date,
			other_date
			from client_mst 
			where deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($clientAllData)!=0)
			{
				$mytime = Carbon::now();
				$currentDate = explode(' ',$mytime);
				$splitedCurrentDate = explode('-',$currentDate[0]);
				$finalDate = $splitedCurrentDate[1]."-".$splitedCurrentDate[2];
				$totalClientData = count($clientAllData);
				for($dateSearchIndex=0;$dateSearchIndex<$totalClientData;$dateSearchIndex++)
				{
					$splitedBirthDate = explode('-',$clientAllData[$dateSearchIndex]->birth_date);
					$birthDate = $splitedBirthDate[1]."-".$splitedBirthDate[2];
					$splitedAnniDate = explode('-',$clientAllData[$dateSearchIndex]->anniversary_date);
					$anniDate = $splitedAnniDate[1]."-".$splitedAnniDate[2];
					if(strcmp($birthDate,$finalDate)==0 &&
						strcmp("birthDate",$headerData['operation'][0])==0) 
					{
						$clientIdParam = $clientIdParam." client_id =".$clientAllData[$dateSearchIndex]->client_id." OR";
					}
					if(strcmp($anniDate,$finalDate)==0 &&
						strcmp("anniversaryDate",$headerData['operation'][0])==0) 
					{
						$clientIdParam = $clientIdParam." client_id =".$clientAllData[$dateSearchIndex]->client_id." OR";
					}
				}
			}
		}
		if($clientFlag==1)
		{
			if($clientIdParam!='')
			{
				$clientIdParam = rtrim($clientIdParam,"OR");
				$clientIdParam = $clientIdParam." and";
				$queryParameter='';
			}
			else
			{
				return $exceptionArray['204'];
			}
		}
		DB::beginTransaction();		
		$clientData = DB::connection($databaseName)->select("select 
		client_id,
		client_name,
		company_name,
		contact_no,
		contact_no1,
		email_id,
		address1,
		profession_id,
		birth_date,
		anniversary_date,
		other_date,
		credit_limit,
		credit_days,
		gst,
		is_display,
		created_at,
		updated_at,
		deleted_at,
		state_abb,
		city_id			
		from client_mst where ".$queryParameter.$clientIdParam." deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		if(count($clientData)==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$documentArray = array();
			for($documentArrayData=0;$documentArrayData<count($clientData);$documentArrayData++)
			{
				//get data from client-document
				DB::beginTransaction();
				$clientDocumentData = DB::connection($databaseName)->select("select 
				document_id,
				client_id,
				sale_id,
				document_name,
				document_size,
				document_format,
				document_type,
				created_at,
				updated_at,
				deleted_at
				from client_doc_dtl where client_id = '".$clientData[$documentArrayData]->client_id."' and deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				$documentArray[$documentArrayData] = $clientDocumentData;
			}
			$clientArraydata = array();
			$clientArraydata['clientData'] = $clientData;
			$clientArraydata['clientDocumentData'] = $documentArray;
			$enocodedData = json_encode($clientArraydata);
			return $enocodedData;
		}
	}
	
	/**-------------------------------
	 * get client data 
	 * @param contact-no
	 * returns the status
	*/
	public function getClientData($contactNo)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$clientData = DB::connection($databaseName)->select("select 
		client_id,
		client_name,
		email_id,
		contact_no,
		contact_no1,
		address1,
		is_display,
		state_abb,
		city_id,
		company_name,
		profession_id,
		birth_date,
		anniversary_date,
		other_date,
		credit_limit,
		credit_days,
		gst,
		created_at,
		updated_at
		from client_mst 
		where deleted_at='0000-00-00 00:00:00' and 
		contact_no='".$contactNo."'");
		DB::commit();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(count($clientData)==0)
		{
			return $exceptionArray['200'];
		}
		else
		{
			//get data from client-document
			DB::beginTransaction();
			$clientDocumentData = DB::connection($databaseName)->select("select 
			document_id,
			client_id,
			sale_id,
			document_name,
			document_size,
			document_format,
			document_type,
			created_at,
			updated_at,
			deleted_at
			from client_doc_dtl where client_id = '".$clientData[0]->client_id."' and deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			$clientArraydata = array();
			$clientArraydata['clientData'] = $clientData;
			$clientArraydata['clientDocumentData'] = $clientDocumentData;
		
			$enocodedData = json_encode($clientArraydata);
			return $enocodedData;
		}
	}
	
	/**
	 * get client data 
	 * @param client_name
	 * returns the status/error-message
	*/
	public function getClientName($clientName)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		client_id
		from client_mst 
		where deleted_at='0000-00-00 00:00:00' and 
		client_name='".$clientName."'");
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
			return $raw;
		}
	}
	
	/**
	 * get client data 
	 * @param contact-no,client-id
	 * returns the status/error-message
	*/
	public function getClientContactNoData($contactNo,$clientId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		client_id,
		client_name,
		email_id,
		contact_no,
		contact_no1,
		address1,
		is_display,
		state_abb,
		city_id,
		company_name,
		profession_id,
		birth_date,
		anniversary_date,
		other_date,
		credit_limit,
		credit_days,
		gst,
		created_at,
		updated_at
		from client_mst 
		where deleted_at='0000-00-00 00:00:00' and 
		contact_no='".$contactNo."' and
		client_id!='".$clientId."'");
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
			return $raw;
		}
	}
	
	/**
	 * get client data 
	 * @param client_name
	 * returns the status/error-message
	*/
	public function getClientNameValidate($clientName,$contactNo)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		client_id
		from client_mst 
		where deleted_at='0000-00-00 00:00:00' and 
		client_name='".$clientName."' and 
		contact_no!='".$contactNo."'");
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
			return $raw;
		}
	}
	
	/**
	 * get client data 
	 * @param client_name
	 * returns the status/error-message
	*/
	public function getClientNameForValidate($clientName,$contactNo)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($contactNo!='')
		{
			DB::beginTransaction();		
			$raw = DB::connection($databaseName)->select("select 
			client_id,
			contact_no,
			client_name
			from client_mst 
			where deleted_at='0000-00-00 00:00:00' and 
			client_name='".$clientName."' and contact_no!='".$contactNo."'");
			DB::commit();
			if(count($raw)!=0)
			{
				if(strcmp($clientName,$raw[0]->client_name)==0 && strcmp($contactNo,$raw[0]->contact_no)!=0)
				{
					return $exceptionArray['content'];
				}
			}
		}
		else
		{
			DB::beginTransaction();		
			$raw = DB::connection($databaseName)->select("select 
			client_id
			from client_mst 
			where deleted_at='0000-00-00 00:00:00' and 
			client_name='".$clientName."'");
			DB::commit();
		}
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
	 * update client data 
	 * @param client-data,client key(field-name) and client-id
	 * returns the status/error-message
	*/
	public function updateData($clientData,$key,$clientId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		for($data=0;$data<count($clientData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$clientData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update client_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where client_id = '".$clientId."' and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
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
