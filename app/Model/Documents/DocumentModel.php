<?php
namespace ERP\Model\Documents;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Http\Requests;
use Illuminate\Http\Request;
use ERP\Core\Products\Services\ProductService;
// use ERP\Core\Documents\Entities\UserArray;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class DocumentModel extends Model
{
	/**
	 * insert data 
	 * @param  array
	 * returns the status
	*/
	public function deleteDocumentData($headerData,$documentId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		$mytime = Carbon\Carbon::now();
		$tableName='';
		if(strcmp('sale-bill',$headerData['type'][0])==0)
		{
			$tableName = "sales_bill_doc_dtl";
			DB::beginTransaction();
			$clientDocumentName = DB::connection($databaseName)->select("select document_name 
			from client_doc_dtl where document_id='".$documentId."'");
			DB::commit();
			if(count($clientDocumentName)!=0)
			{
				DB::beginTransaction();
				$updateBillDocument = DB::connection($databaseName)->statement("update ".$tableName." 
				set deleted_at='".$mytime."'
				where document_name='".$clientDocumentName[0]->document_name."'");
				DB::commit();
			}
			DB::beginTransaction();
			$updateClientDocument = DB::connection($databaseName)->statement("update client_doc_dtl 
			set deleted_at='".$mytime."'
			where document_id='".$documentId."'");
			DB::commit();
				
				
		}
		elseif (strcmp('purchase-bill',$headerData['type'][0])==0)
		{
			$tableName = "purchase_doc_dtl";
			DB::beginTransaction();
			$updateBillDocument = DB::connection($databaseName)->statement("update ".$tableName." 
			set deleted_at='".$mytime."'
			where document_id='".$documentId."'");
			DB::commit();
		}
		elseif (strcmp('product',$headerData['type'][0])==0) 
		{
			$tableName = "product_doc_dtl";
			$productId = 0;

			DB::beginTransaction();
			$rawProduct = DB::connection($databaseName)->select("select product_id from ".$tableName."
			where document_id='".$documentId."'");
			DB::commit();

			if (isset($rawProduct[0]->product_id))
			{
				$productId = $rawProduct[0]->product_id;
			}
			
			DB::beginTransaction();
			$updateBillDocument = DB::connection($databaseName)->statement("update ".$tableName." 
			set deleted_at='".$mytime."'
			where document_id='".$documentId."'");
			DB::commit();

			if ($productId != 0 && $productId != ""){
				$productServiceData = new ProductService();
				$productServiceData = $productServiceData->fireWebIntegrationPush($productId,$headerData);
			}
		}

		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($updateBillDocument==1)
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['500'];
		}
	}

	/**
	 * get bill Document data
	 * @param  sale_ids
	 * returns the exception-message/sales data
	*/
	public function getSaleIdsDocuments($saleIds)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();

		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$document_type = 'bill';

		if(count($saleIds) > 0)
		{
			DB::beginTransaction();
			$companyRaw = DB::connection($databaseName)->select("select 
			print_type
			from company_mst 
			where company_id in (select company_id from sales_bill where sale_id = '$saleIds[0]')");
			DB::commit();

			if (is_array($companyRaw)){
				if(count($companyRaw) > 0){
					if ($companyRaw[0]->print_type == 'preprint'){
						$document_type = 'preprint-bill';
					}
				}
			}
		}
		else{
			return $exceptionArray['204'];
		}
		
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		document_id,
		sale_id,
		document_name,
		document_size,
		document_format,
		document_type,
		created_at,
		updated_at
		from sales_bill_doc_dtl 
		where document_id in (select MAX(document_id) from sales_bill_doc_dtl where sale_id in (".implode(',',$saleIds).") and document_format = 'pdf' and document_type = '$document_type' and deleted_at='0000-00-00 00:00:00' GROUP BY sale_id)");
		DB::commit();
		
		if(count($raw)==0)
		{
			return $exceptionArray['404']; 
		}
		else
		{
			return $raw;
		}
	}
}
