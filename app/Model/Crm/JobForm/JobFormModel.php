<?php
namespace ERP\Model\Crm\JobForm;

use Illuminate\Database\Eloquent\Model;
use ERP\Model\Crm\JobFormNumber\JobFormNumberModel;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormModel extends Model
{
	protected $table = 'job_form_dtl';
	
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
		
		$dataArray = func_get_arg(0);
		$productArray = func_get_arg(1);
		$encodedArray = json_encode($productArray);
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into job_card_dtl(
		client_name,
		address,
		contact_no,
		email_id,
		job_card_no,
		labour_charge,
		service_type,
		entry_date,
		delivery_date,
		advance,
		total,
		tax,
		payment_mode,
		state_abb,
		city_id,
		company_id,
		client_id,
		bank_name,
		cheque_no,
		product_array,
		created_at) 
		values(
		'".$dataArray['clientName']."',
		'".$dataArray['address']."',
		'".$dataArray['contactNo']."',
		'".$dataArray['emailId']."',
		'".$dataArray['jobCardNo']."',
		'".$dataArray['labourCharge']."',
		'".$dataArray['serviceType']."',
		'".$dataArray['entryDate']."',
		'".$dataArray['deliveryDate']."',
		'".$dataArray['advance']."',
		'".$dataArray['total']."',
		'".$dataArray['tax']."',
		'".$dataArray['paymentMode']."',
		'".$dataArray['stateAbb']."',
		'".$dataArray['cityId']."',
		'".$dataArray['companyId']."',
		'".$dataArray['clientId']."',
		'".$dataArray['bankName']."',
		'".$dataArray['chequeNo']."',
		'".$encodedArray."',
		'".$mytime."') on duplicate key update 
		client_name='".$dataArray['clientName']."',
		address='".$dataArray['address']."',
		contact_no='".$dataArray['contactNo']."',
		email_id='".$dataArray['emailId']."',
		job_card_no='".$dataArray['jobCardNo']."',
		labour_charge='".$dataArray['labourCharge']."',
		service_type='".$dataArray['serviceType']."',
		entry_date='".$dataArray['entryDate']."',
		delivery_date='".$dataArray['deliveryDate']."',
		advance='".$dataArray['advance']."',
		total='".$dataArray['total']."',
		tax='".$dataArray['tax']."',
		payment_mode='".$dataArray['paymentMode']."',
		state_abb='".$dataArray['stateAbb']."',
		city_id='".$dataArray['cityId']."',
		company_id='".$dataArray['companyId']."',
		client_id='".$dataArray['clientId']."',
		bank_name='".$dataArray['bankName']."',
		cheque_no='".$dataArray['chequeNo']."',
		product_array='".$encodedArray."',
		updated_at='".$mytime."'");
		DB::commit();
		
		//if insertion is performed job-form-number should be updated...otherwise not
		// for updating job-form-number,get updated_at time
		DB::beginTransaction();	
		$getLatestJobCardData = DB::connection($databaseName)->select("SELECT
		updated_at	
		FROM job_card_dtl
		where deleted_at='0000-00-00 00:00:00' 
		group by job_card_no desc limit 1");
		DB::commit();
		if(strcmp($getLatestJobCardData[0]->updated_at,'0000-00-00 00:00:00')==0)
		{
			//update job-form-number
			$jobFormNumberModel = new JobFormNumberModel();
			$latestJobFormNumber = $jobFormNumberModel->getLatestJobFormNumberData($dataArray['companyId']);
			if(strcmp($latestJobFormNumber,$exceptionArray['204'])==0)
			{
				return $exceptionArray['204'];
			}
			$decodedJobFormNumber = json_decode($latestJobFormNumber);
			$endAt = $decodedJobFormNumber[0]->end_at+1;
			$updateResult = $jobFormNumberModel->updateJobCardNo($dataArray['companyId'],$endAt);
		}
		if($raw==1)
		{
			DB::beginTransaction();		
			$jobCardDtl = DB::connection($databaseName)->select("select 
			job_card_id,
			client_name,
			address,
			contact_no,
			email_id,
			job_card_no,
			labour_charge,
			service_type,
			entry_date,
			delivery_date,
			advance,
			total,
			tax,
			payment_mode,
			state_abb,
			city_id,
			company_id,
			client_id,
			bank_name,
			cheque_no,
			product_array,
			created_at,
			updated_at
			from job_card_dtl 
			where deleted_at='0000-00-00 00:00:00' and
			job_card_no='".$dataArray['jobCardNo']."'");
			DB::commit();
			if(count($raw)==0)
			{
				return $exceptionArray['204'];;
			}
			else
			{	
				return json_encode($jobCardDtl);
			}	
		}
		else
		{
			return $exceptionArray['500'];
		}
	}
	
	/**
	 * get all data 
	 * returns the status/array-data
	*/
	public function getAllData()
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		job_card_id,
		client_name,
		address,
		contact_no,
		email_id,
		job_card_no,
		labour_charge,
		service_type,
		entry_date,
		delivery_date,
		advance,
		total,
		tax,
		payment_mode,
		state_abb,
		city_id,
		company_id,
		client_id,
		bank_name,
		cheque_no,
		product_array,
		created_at,
		updated_at
		from job_card_dtl where deleted_at='0000-00-00 00:00:00'");
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
			return json_encode($raw);
		}
	}
	
	/**
	 * get specific data as per given job-card-no 
	 * returns the status/array-data
	*/
	public function getData($jobCardNo)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		job_card_id,
		client_name,
		address,
		contact_no,
		email_id,
		job_card_no,
		labour_charge,
		service_type,
		entry_date,
		delivery_date,
		advance,
		total,
		tax,
		payment_mode,
		state_abb,
		city_id,
		company_id,
		client_id,
		bank_name,
		cheque_no,
		product_array,
		created_at,
		updated_at
		from job_card_dtl 
		where deleted_at='0000-00-00 00:00:00' and
		job_card_no='".$jobCardNo."'");
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
			return json_encode($raw);
		}
	}
	
	/**
	 * get job-card data
	 * @param  fromdate,todate
	 * returns the exception-message/data
	*/
	public function getFromToDateData($fromDate,$toDate)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$jobCardData = DB::connection($databaseName)->select("select 
		job_card_id,
		client_name,
		address,
		contact_no,
		email_id,
		job_card_no,
		labour_charge,
		service_type,
		entry_date,
		delivery_date,
		advance,
		total,
		tax,
		payment_mode,
		state_abb,
		city_id,
		company_id,
		client_id,
		bank_name,
		cheque_no,
		product_array,
		created_at,
		updated_at 
		from job_card_dtl 
		where (entry_Date BETWEEN '".$fromDate."' AND '".$toDate."') and
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if(count($jobCardData)!=0)
		{
			return json_encode($jobCardData);
		}
		else
		{
			return $exceptionArray['204'];
		}
	}
	
	/**
	 * insert document data
	 * @param  jobForm-id,document-name,document-format,document-type
	 * returns the exception-message
	*/
	public function jobFormDocumentData($jobFormId,$documentName,$documentFormat,$documentType)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$raw = DB::connection($databaseName)->statement("insert into job_card_doc_dtl(
		job_card_id,
		document_name,
		document_format,
		document_type,
		created_at)
		values('".$jobFormId."','".$documentName."','".$documentFormat."','".$documentType."','".$mytime."')");
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
