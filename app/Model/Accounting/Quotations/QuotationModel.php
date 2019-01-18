<?php
namespace ERP\Model\Accounting\Quotations;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Settings\QuotationNumbers\Services\QuotationService;
use ERP\Api\V1_0\Settings\QuotationNumbers\Controllers\QuotationController;
use Illuminate\Container\Container;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use stdClass;
use ERP\Core\Accounting\Quotations\Entities\QuotationArray;
use ERP\Model\Accounting\Bills\BillModel;
use ERP\Core\Accounting\Bills\Entities\EncodeData;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationModel extends Model
{
	protected $table = 'quotation_bill_dtl';
	
	/**
	 * insert only data 
	 * @param  array
	 * returns the status
	*/
	public function insertData($productArray,$quotationNumber,$total,$extraCharge,$tax,$grandTotal,$remark,$entryDate,$companyId,$branchId,$ClientId,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$documentArray,$headerData,$poNumber,$paymentMode,$invoiceNumber,$bankName,$checkNumber)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if(array_key_exists("issalesorder",$headerData))
		{
			//insert sales-order data
			$result = $this->insertSalesOrderData($productArray,$total,$extraCharge,$tax,$grandTotal,$remark,$entryDate,$companyId,$branchId,$ClientId,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$documentArray,$headerData,$poNumber,$paymentMode,$invoiceNumber,$bankName,$checkNumber);
			if(is_array($result))
			{
				$decodedData = json_encode($result);
				$encoded = new EncodeData();
				$encodeData = $encoded->getEncodedData($decodedData);
				return $encodeData;
			}
			else
			{
				return $exceptionArray['500'];
			}
		}
		else
		{
			//insert bill data
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("insert into quotation_bill_dtl(
			product_array,
			quotation_number,
			total,
			total_discounttype,
			total_discount,
			total_cgst_percentage,
			total_sgst_percentage,
			total_igst_percentage,
			extra_charge,
			tax,
			grand_total,
			remark,
			entry_date,
			company_id,
			branch_id,
			client_id,
			jf_id,
			created_at) 
			values('".$productArray."','".$quotationNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$remark."','".$entryDate."','".$companyId."','".$branchId."','".$ClientId."','".$jfId."','".$mytime."')");
			DB::commit();
			//update quotation-number
			$quotationResult = $this->updateQuotationNumber($companyId);
			if(strcmp($quotationResult,$exceptionArray['200'])!=0)
			{
				return $quotationResult;
			}
			if($raw==1)
			{
				DB::beginTransaction();
				$quotationId = DB::connection($databaseName)->select("SELECT 
				max(quotation_bill_id) quotation_bill_id
				FROM quotation_bill_dtl where deleted_at='0000-00-00 00:00:00'");
				DB::commit();
				//insertion in quotation bill archives
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into quotation_bill_archives(
				product_array,
				quotation_number,
				total,
				total_discounttype,
				total_discount,
				total_cgst_percentage,
				total_sgst_percentage,
				total_igst_percentage,
				extra_charge,
				tax,
				grand_total,
				remark,
				entry_date,
				company_id,
				branch_id,
				client_id,
				quotation_bill_id,
				jf_id,
				created_at) 
				values('".$productArray."','".$quotationNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$remark."','".$entryDate."','".$companyId."','".$branchId."','".$ClientId."','".$quotationId[0]->quotation_bill_id."','".$jfId."','".$mytime."')");
				DB::commit();
				
				//get latest inserted quotation bill data
				DB::beginTransaction();
				$quotationResult = DB::connection($databaseName)->select("select
				quotation_bill_id,
				product_array,
				quotation_number,
				total,
				total_discounttype,
				total_discount,
				total_cgst_percentage,
				total_sgst_percentage,
				total_igst_percentage,
				extra_charge,
				tax,
				grand_total,
				remark,
				entry_date,
				client_id,
				company_id,
				branch_id,
				jf_id,
				created_at,
				updated_at 
				from quotation_bill_dtl where quotation_bill_id=(select MAX(quotation_bill_id) as quotation_bill_id from quotation_bill_dtl) and deleted_at='0000-00-00 00:00:00'"); 
				DB::commit();
				if(count($quotationResult)==1)
				{
					return json_encode($quotationResult);
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
	}
	
	/**
	 * insert sales-order data
	 * returns the exception-message/status
	*/
	
	public function insertSalesOrderData($productArray,$total,$extraCharge,$tax,$grandTotal,$remark,$entryDate,$companyId,	 $ClientId,$jfId,$totalDiscounttype,$totalDiscount,$totalCgstPercentage,$totalSgstPercentage,$totalIgstPercentage,$documentArrayData,$headerData,$poNumber,$paymentMode,$invoiceNumber,$bankName,$checkNumber)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//insert bill data
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into sales_bill(
		product_array,
		invoice_number,
		total,
		total_discounttype,
		total_discount,
		total_cgst_percentage,
		total_sgst_percentage,
		total_igst_percentage,
		extra_charge,
		tax,
		grand_total,
		remark,
		entry_date,
		company_id,
		client_id,
		po_number,
		payment_mode,
		bank_name,
		check_number,
		is_salesorder,
		jf_id,
		created_at) 
		values('".$productArray."','".$invoiceNumber."','".$total."','".$totalDiscounttype."','".$totalDiscount."','".$totalCgstPercentage."','".$totalSgstPercentage."','".$totalIgstPercentage."','".$extraCharge."','".$tax."','".$grandTotal."','".$remark."','".$entryDate."','".$companyId."','".$ClientId."',
		'".$poNumber."','".$paymentMode."','".$bankName."','".$checkNumber."','ok','".$jfId."','".$mytime."')");
		DB::commit();
		//update invoice-number
		$billModel = new BillModel();
		$invoiceResult = $billModel->updateInvoiceNumber($companyId);
		if(strcmp($invoiceResult,$exceptionArray['200'])!=0)
		{
			return $invoiceResult;
		}
		//get sale-id
		DB::beginTransaction();
		$saleId = DB::connection($databaseName)->select("SELECT 
		max(sale_id) as sale_id,
		product_array,
		payment_mode,
		bank_name,
		invoice_number,
		job_card_number,
		check_number,
		total,
		total_discounttype,
		total_discount,
		total_cgst_percentage,
		total_sgst_percentage,
		total_igst_percentage,
		extra_charge,
		tax,
		grand_total,
		advance,
		balance,
		po_number,
		remark,
		entry_date,
		sales_type,
		client_id,
		company_id,
		jf_id,
		created_at,
		updated_at
		FROM sales_bill where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		$documentCount = count($documentArrayData);
		if($documentCount!=0)
		{
			//insert document data
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into sales_bill_doc_dtl(
				sale_id,
				document_name,
				document_format,
				document_size,
				created_at)
				values('".$saleId[0]->sale_id."','".$documentArrayData[$documentArray][0]."','".$documentArrayData[$documentArray][2]."','".$documentArrayData[$documentArray][1]."','".$mytime."')");
				DB::commit();
			}
		}
		return $saleId;
	}
	
	/**
	 * after insertion quotation-bill data update quotation-number
	 * @param  company-id
	 * returns the exception-message
	*/
	public function updateQuotationNumber($companyId)
	{
		//get constants from constant class
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$quotationService = new QuotationService();	
		$quotationData = $quotationService->getLatestQuotationData($companyId);
		if(strcmp($exceptionArray['204'],$quotationData)==0)
		{
			return $quotationData;
		}
		$endAt = json_decode($quotationData)[0]->endAt;
		$quotationController = new QuotationController(new Container());
		$quotationMethod=$constantArray['postMethod'];
		$quotationPath=$constantArray['quotationUrl'];
		$quotationDataArray = array();
		$quotationDataArray['endAt'] = $endAt+1;
		$quotationRequest = Request::create($quotationPath,$quotationMethod,$quotationDataArray);
		$updateResult = $quotationController->update($quotationRequest,json_decode($quotationData)[0]->quotationId);
		return $updateResult;
	}
	
	/**
	 * insert document data
	 * @param  quotation-id,document-name,document-format,document-type
	 * returns the exception-message
	*/
	public function quotationDocumentData($quotationBillId,$documentName,$documentFormat,$documentType)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$raw = DB::connection($databaseName)->statement("insert into quotation_bill_doc_dtl(
		quotation_bill_id,
		document_name,
		document_format,
		document_type,
		created_at)
		values('".$quotationBillId."','".$documentName."','".$documentFormat."','".$documentType."','".$mytime."')");
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
	
	/**
	 * get specific data
	 * @param  headerdata
	 * returns the exception-message/array data
	*/
	public function getSpecifiedData($headerData)
	{
		if(array_key_exists('previousquotationid',$headerData) || array_key_exists('nextquotationid',$headerData)
			|| array_key_exists('operation',$headerData))
		{
			$resultData = $this->getPreviousNextData($headerData);
			return $resultData;
		}
		else
		{
			$quotationArray = new QuotationArray();
			$quotationArrayData = $quotationArray->searchQuotationData();
			$queryParameter="";
			for($dataArray=0;$dataArray<count($quotationArrayData);$dataArray++)
			{
				$key = $quotationArrayData[array_keys($quotationArrayData)[$dataArray]];
				$queryKey = array_keys($quotationArrayData)[$dataArray];
				
				if(array_key_exists($quotationArrayData[array_keys($quotationArrayData)[$dataArray]],$headerData))
				{
					$queryParameter = $queryParameter."".$queryKey."='".$headerData[$key][0]."' and ";
				}
			}
			//database selection
			$database = "";
			$constantDatabase = new ConstantClass();
			$databaseName = $constantDatabase->constantDatabase();
			
			DB::beginTransaction();		
			$raw = DB::connection($databaseName)->select("select 
			quotation_bill_id,
			product_array,
			quotation_number,
			total,
			total_discounttype,
			total_discount,
			total_cgst_percentage,
			total_sgst_percentage,
			total_igst_percentage,
			extra_charge,
			tax,
			grand_total,
			remark,
			entry_date,
			client_id,
			company_id,
			branch_id,
			jf_id,
			created_at,
			updated_at	
			from quotation_bill_dtl where ".$queryParameter." deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			
			// get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(count($raw)==0)
			{
				return $exceptionArray['204'];
			}
			else
			{
				$documentResult = array();
				for($quotationData=0;$quotationData<count($raw);$quotationData++)
				{
					DB::beginTransaction();
					$documentResult[$quotationData] = DB::connection($databaseName)->select("select
					document_id,
					quotation_bill_id,
					document_name,
					document_size,
					document_format,
					document_type,
					created_at,
					updated_at
					from quotation_bill_doc_dtl
					where quotation_bill_id='".$raw[$quotationData]->quotation_bill_id."' and 
					deleted_at='0000-00-00 00:00:00'");
					DB::commit();
					if(count($documentResult[$quotationData])==0)
					{
						$documentResult[$quotationData] = array();
						$documentResult[$quotationData][0] = new stdClass();
						$documentResult[$quotationData][0]->document_id = 0;
						$documentResult[$quotationData][0]->quotation_bill_id = 0;
						$documentResult[$quotationData][0]->document_name = '';
						$documentResult[$quotationData][0]->document_size = 0;
						$documentResult[$quotationData][0]->document_format = '';
						$documentResult[$quotationData][0]->document_type ='quotation';
						$documentResult[$quotationData][0]->created_at = '0000-00-00 00:00:00';
						$documentResult[$quotationData][0]->updated_at = '0000-00-00 00:00:00';
					}
				}
				$quotationArrayData = array();
				$quotationArrayData['quotationData'] = json_encode($raw);
				$quotationArrayData['documentData'] = json_encode($documentResult);
				return json_encode($quotationArrayData);
			}
		}
	}
	
	/**
	 * get previous-next quotation-bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getPreviousNextData($headerData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(array_key_exists('previousquotationid',$headerData))
		{
			if($headerData['previousquotationid'][0]==0)
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->select("select 
				quotation_bill_id,
				product_array,
				quotation_number,
				total,
				total_discounttype,
				total_discount,
				total_cgst_percentage,
				total_sgst_percentage,
				total_igst_percentage,
				extra_charge,
				tax,
				grand_total,
				remark,
				entry_date,
				client_id,
				company_id,
				branch_id,
				jf_id,
				created_at,
				updated_at	 
				from quotation_bill_dtl 
				where company_id = '".$headerData['companyid'][0]."' and
				deleted_at='0000-00-00 00:00:00'
				order by quotation_bill_id desc limit 1");
				DB::commit();
				if(count($raw)==0)
				{
					return $exceptionArray['204'];
				}
				else
				{
					$quotationDataResult = $this->getDocumentData($raw);
					return $quotationDataResult;
				}
			}
			else
			{
				$quotationId = $headerData['previousquotationid'][0]-1;

				//quotationId
				$result = $this->getQuotationPreviousNextData($headerData,$quotationId);
				
				if(count($result)==0)
				{
					DB::beginTransaction();
					$previousAscId = DB::connection($databaseName)->select("select 
					quotation_bill_id
					from quotation_bill_dtl 
					where company_id = '".$headerData['companyid'][0]."' and
					deleted_at='0000-00-00 00:00:00'
					order by quotation_bill_id asc limit 1");
					DB::commit();
					
					if($quotationId<$previousAscId[0]->quotation_bill_id)
					{
						return $exceptionArray['204'];
					}
					else
					{
						for($arrayData=$quotationId-1;$arrayData>=$previousAscId[0]->quotation_bill_id;$arrayData--)
						{
							$innerResult = $this->getQuotationPreviousNextData($headerData,$arrayData);
							if(count($innerResult)!=0)
							{
								break;
							}
							if($arrayData==$previousAscId[0]->quotation_bill_id && count($innerResult)==0)
							{
								return $exceptionArray['204'];
							}
							$quotationId++;
						}
						$quotationDataResult = $this->getDocumentData($innerResult);
						return $quotationDataResult;
					}
				}
				else
				{
					$quotationDataResult = $this->getDocumentData($result);
					return $quotationDataResult;
				}
			}
		}
		else if(array_key_exists('nextquotationid',$headerData))
		{
			$quotationId = $headerData['nextquotationid'][0]+1;
			$result = $this->getQuotationPreviousNextData($headerData,$quotationId);
			if(count($result)==0)
			{
				DB::beginTransaction();
				$nextDescId = DB::connection($databaseName)->select("select 
				quotation_bill_id
				from quotation_bill_dtl 
				where company_id = '".$headerData['companyid'][0]."' and
				deleted_at='0000-00-00 00:00:00' 
				order by quotation_bill_id desc limit 1");
				DB::commit();
				if($quotationId>$nextDescId[0]->quotation_bill_id)
				{
					return $exceptionArray['204'];
				}
				else
				{
					for($arrayData=$quotationId+1;$arrayData<=$nextDescId[0]->quotation_bill_id;$arrayData++)
					{
						$innerResult = $this->getQuotationPreviousNextData($headerData,$arrayData);
						if(count($innerResult)!=0)
						{
							break;
						}
						if($arrayData==$nextDescId[0]->quotation_bill_id && count($innerResult)==0)
						{
							return $exceptionArray['204'];
						}
						$quotationId++;
					}
					$quotationDataResult = $this->getDocumentData($innerResult);
					return $quotationDataResult;
				}
			}
			else
			{
				$quotationDataResult = $this->getDocumentData($result);
				return $quotationDataResult;
			}
		}
		else if(array_key_exists('operation',$headerData))
		{
			if(strcmp($headerData['operation'][0],'first')==0)
			{
				DB::beginTransaction();
				$fistQuotationDataResult = DB::connection($databaseName)->select("select 
				quotation_bill_id,
				product_array,
				quotation_number,
				total,
				total_discounttype,
				total_discount,
				total_cgst_percentage,
				total_sgst_percentage,
				total_igst_percentage,
				extra_charge,
				tax,
				grand_total,
				remark,
				entry_date,
				client_id,
				company_id,
				branch_id,
				jf_id,
				created_at,
				updated_at	 
				from quotation_bill_dtl 
				where company_id = '".$headerData['companyid'][0]."' and
				deleted_at='0000-00-00 00:00:00'  order by quotation_bill_id asc limit 1");
				DB::commit();
				
				$quotationDataResult = $this->getDocumentData($fistQuotationDataResult);
				return $quotationDataResult;
			}
			else if(strcmp($headerData['operation'][0],'last')==0)
			{
				DB::beginTransaction();
				$lastQuotationDataResult = DB::connection($databaseName)->select("select 
				quotation_bill_id,
				product_array,
				quotation_number,
				total,
				total_discounttype,
				total_discount,
				total_cgst_percentage,
				total_sgst_percentage,
				total_igst_percentage,
				extra_charge,
				tax,
				grand_total,
				remark,
				entry_date,
				client_id,
				company_id,
				branch_id,
				jf_id,
				created_at,
				updated_at 
				from quotation_bill_dtl 
				where company_id = '".$headerData['companyid'][0]."' and
				deleted_at='0000-00-00 00:00:00' order by quotation_bill_id desc limit 1");
				DB::commit();
				
				$quotationDataResult = $this->getDocumentData($lastQuotationDataResult);
				return $quotationDataResult;
			}
		}
	}
	
	/**
	 * get previous quotation-bill data
	 * @param  header-data
	 * returns the exception-message/sales data
	*/
	public function getQuotationPreviousNextData($headerData,$quotationId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$quotationData = DB::connection($databaseName)->select("select 
		quotation_bill_id,
		product_array,
		quotation_number,
		total,
		total_discounttype,
		total_discount,
		total_cgst_percentage,
		total_sgst_percentage,
		total_igst_percentage,
		extra_charge,
		tax,
		grand_total,
		remark,
		entry_date,
		client_id,
		company_id,
		branch_id,
		jf_id,
		created_at,
		updated_at 
		from quotation_bill_dtl 
		where company_id = '".$headerData['companyid'][0]."' and
		deleted_at='0000-00-00 00:00:00' and
		quotation_bill_id='".$quotationId."'");
		DB::commit();

		return $quotationData;
	}
	
	/**
	 * get document quotation-bill data(internal call)
	 * @param  quotation-bill-data
	 * returns the quotation-data
	*/
	public function getDocumentData($quotationArrayData)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$documentResult = array();
		for($quotationData=0;$quotationData<count($quotationArrayData);$quotationData++)
		{
			DB::beginTransaction();
			$documentResult[$quotationData] = DB::connection($databaseName)->select("select
			document_id,
			quotation_bill_id,
			document_name,
			document_size,
			document_format,
			document_type,
			created_at,
			updated_at
			from quotation_bill_doc_dtl
			where quotation_bill_id='".$quotationArrayData[$quotationData]->quotation_bill_id."' and 
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if(count($documentResult[$quotationData])==0)
			{
				$documentResult[$quotationData] = array();
				$documentResult[$quotationData][0] = new stdClass();
				$documentResult[$quotationData][0]->document_id = 0;
				$documentResult[$quotationData][0]->quotation_bill_id = 0;
				$documentResult[$quotationData][0]->document_name = '';
				$documentResult[$quotationData][0]->document_size = 0;
				$documentResult[$quotationData][0]->document_format = '';
				$documentResult[$quotationData][0]->document_type ='quotation';
				$documentResult[$quotationData][0]->created_at = '0000-00-00 00:00:00';
				$documentResult[$quotationData][0]->updated_at = '0000-00-00 00:00:00';
			}
		}
		$quotationDataArray = array();
		$quotationDataArray['quotationData'] = json_encode($quotationArrayData);
		$quotationDataArray['documentData'] = json_encode($documentResult);
		return json_encode($quotationDataArray);
	}


	/**
	 * get document quotation-bill data(internal call)
	 * @param  quotation-bill-data
	 * returns the quotation-data
	*/
	public function getDocumentDataByClientId($clientId)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$documentResult = array();

		DB::beginTransaction();
		$documentResult = DB::connection($databaseName)->select("select
		document_id,
		quotation_bill_id,
		document_name,
		document_size,
		document_format,
		document_type,
		created_at,
		updated_at
		from quotation_bill_doc_dtl
		where quotation_bill_id in ( select quotation_bill_id from quotation_bill_dtl where client_id = '".$clientId."' and deleted_at='0000-00-00 00:00:00') and 
		deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		if(count($documentResult)==0)
		{
			return $exceptionArray['204'];
		}

		$quotationDataArray = array();
		$quotationDataArray['clientDocumentData'] = $documentResult;
		return json_encode($quotationDataArray);
	}
	
	/**
	 * get specific quotation-bill data
	 * @param  headerdata
	 * returns the exception-message/array data
	*/
	public function getquotationIdData($quotationBillId)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		quotation_bill_id,
		product_array,
		quotation_number,
		total,
		total_discounttype,
		total_discount,
		total_cgst_percentage,
		total_sgst_percentage,
		total_igst_percentage,
		extra_charge,
		tax,
		grand_total,
		remark,
		entry_date,
		client_id,
		company_id,
		branch_id,
		jf_id,
		created_at,
		updated_at	
		from quotation_bill_dtl where quotation_bill_id='".$quotationBillId."' and deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
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
	 * update quotation data
	 * @param  quotation-bill-id and quotation-data array
	 * returns the exception-message/status
	*/
	public function updateQuotationData($quotationArray,$quotationId,$headerData,$documentData,$dataFlag)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$keyValueString = "";
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		for($quotationArrayData=0;$quotationArrayData<count($quotationArray);$quotationArrayData++)
		{
			$keyValueString = $keyValueString.array_keys($quotationArray)[$quotationArrayData]." = '".$quotationArray[array_keys($quotationArray)[$quotationArrayData]]."',";
		}
		if(array_key_exists("issalesorder",$headerData))
		{
			$salesOrderResult = $this->updateSalesOrderData($keyValueString,$mytime,$quotationId,$documentData,$dataFlag);
			return $salesOrderResult;
		}
		else
		{
			// update quotation-data
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			quotation_bill_dtl set
			".$keyValueString."
			updated_at = '".$mytime."'
			where quotation_bill_id = ".$quotationId." and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
			if($raw==1)
			{
				$quotationData = $this->getQuotationIdData($quotationId);
				$jsonDecodedQuotationData = json_decode($quotationData);
				
				//insert quotation data in quotation_bill_archives 
				DB::beginTransaction();
				$quotaionInsertionResult = DB::connection($databaseName)->statement("insert
				into quotation_bill_archives(
				quotation_bill_id,
				product_array,
				quotation_number,
				total,
				total_discounttype,
				total_discount,
				total_cgst_percentage,
				total_sgst_percentage,
				total_igst_percentage,
				extra_charge,
				tax,
				grand_total,
				remark,
				entry_date,
				client_id,
				company_id,
				branch_id,
				jf_id,
				created_at,
				updated_at)
				values(
				'".$jsonDecodedQuotationData[0]->quotation_bill_id."',
				'".$jsonDecodedQuotationData[0]->product_array."',
				'".$jsonDecodedQuotationData[0]->quotation_number."',
				'".$jsonDecodedQuotationData[0]->total."',
				'".$jsonDecodedQuotationData[0]->total_discounttype."',
				'".$jsonDecodedQuotationData[0]->total_discount."',
				'".$jsonDecodedQuotationData[0]->total_cgst_percentage."',
				'".$jsonDecodedQuotationData[0]->total_sgst_percentage."',
				'".$jsonDecodedQuotationData[0]->total_igst_percentage."',
				'".$jsonDecodedQuotationData[0]->extra_charge."',
				'".$jsonDecodedQuotationData[0]->tax."',
				'".$jsonDecodedQuotationData[0]->grand_total."',
				'".$jsonDecodedQuotationData[0]->remark."',
				'".$jsonDecodedQuotationData[0]->entry_date."',
				'".$jsonDecodedQuotationData[0]->client_id."',
				'".$jsonDecodedQuotationData[0]->company_id."',
				'".$jsonDecodedQuotationData[0]->branch_id."',
				'".$jsonDecodedQuotationData[0]->jf_id."',
				'".$jsonDecodedQuotationData[0]->created_at."',
				'".$jsonDecodedQuotationData[0]->updated_at."')");
				DB::commit();
				
				if($quotaionInsertionResult!=1)
				{
					return $exceptionArray['500']; 
				}
				else
				{
					//get latest inserted quotation bill data
					DB::beginTransaction();
					$quotationResult = DB::connection($databaseName)->select("select
					quotation_bill_id,
					product_array,
					quotation_number,
					total,
					total_discounttype,
					total_discount,
					total_cgst_percentage,
					total_sgst_percentage,
					total_igst_percentage,
					extra_charge,
					tax,
					grand_total,
					remark,
					entry_date,
					client_id,
					company_id,
					branch_id,
					jf_id,
					created_at,
					updated_at 
					from quotation_bill_dtl where quotation_bill_id = ".$quotationId." and deleted_at='0000-00-00 00:00:00'"); 
					DB::commit();
					if(count($quotationResult)!=0)
					{
						return json_encode($quotationResult);
					}
					else
					{
						return $exceptionArray['204']; 
					}
				}
			}
		}
	}
	
	/**
	 * update sales-order data
	 * @param  sale-bill-id and sales-data array
	 * returns the exception-message/status
	*/
	public function updateSalesOrderData($keyValueString,$mytime,$quotationId,$documentData,$dataFlag)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($dataFlag==1)
		{
			//data is available => update bill-data
			DB::beginTransaction();
			$raw = DB::connection($databaseName)->statement("update
			sales_bill set
			".$keyValueString."
			updated_at = '".$mytime."'
			where sale_id = ".$quotationId." and
			deleted_at='0000-00-00 00:00:00'");
			DB::commit();
		}
		//get last updated data from sales-bill
		DB::beginTransaction();
		$saleBillData = DB::connection($databaseName)->select("SELECT 
		sale_id,
		product_array,
		payment_mode,
		bank_name,
		invoice_number,
		job_card_number,
		check_number,
		total,
		total_discounttype,
		total_discount,
		total_cgst_percentage,
		total_sgst_percentage,
		total_igst_percentage,
		extra_charge,
		tax,
		grand_total,
		advance,
		balance,
		po_number,
		remark,
		entry_date,
		sales_type,
		client_id,
		company_id,
		branch_id,
		jf_id,
		created_at,
		updated_at
		FROM sales_bill where deleted_at='0000-00-00 00:00:00' and sale_id='".$quotationId."'");
		DB::commit();
		if(count($documentData)!=0)
		{
			$documentCount = count($documentData);
			//insert document data
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				DB::beginTransaction();
				$raw = DB::connection($databaseName)->statement("insert into sales_bill_doc_dtl(
				sale_id,
				document_name,
				document_format,
				document_size,
				created_at)
				values('".$quotationId."','".$documentData[$documentArray][0]."','".$documentData[$documentArray][2]."','".$documentData[$documentArray][1]."','".$mytime."')");
				DB::commit();
				// add documents in client database
				DB::beginTransaction();
				$clientDocumentResult = DB::connection($databaseName)->statement("insert into client_doc_dtl(
				sale_id,
				document_name,
				document_format,
				document_size,
				client_id,
				created_at) 
				values('".$quotationId."','".$documentData[$documentArray][0]."','".$documentData[$documentArray][2]."','".$documentData[$documentArray][1]."','".$saleBillData[0]->client_id."','".$mytime."')");
				DB::commit();
			}
		}
		if(count($saleBillData)!=0)
		{
			return json_encode($saleBillData);
		}
	}
}
