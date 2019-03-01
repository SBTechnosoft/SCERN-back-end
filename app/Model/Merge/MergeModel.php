<?php
namespace ERP\Model\Merge;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use stdClass;

/**
 * @author reema Patel<reema.p@siliconbrain.in>
 */
class MergeModel extends Model
{
	protected $table = 'product_mst';
	/**
	 * get data 
	 * @param  array
	 * returns the data
	*/
	public function getProductTrnData($productId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$productTrn = DB::connection($databaseName)->select("select 
			product_trn_id,
			transaction_type,
			invoice_number,
			bill_number,
			company_id,
			jf_id,
			product_id
			from product_trn 
			where deleted_at='0000-00-00 00:00:00'
			and product_id = '".$productId."'");
		if (count($productTrn)) {
			return json_encode($productTrn);
		}else{
			return $exceptionArray['404'];
		}
	}
	/**
	 * get data 
	 * @param  array
	 * returns the data
	*/
	public function getSalesBillDataByJfId($jfId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$billData = DB::connection($databaseName)->select("select 
		sale_id,
		product_array,
		invoice_number,
		sales_type,
		company_id,
		branch_id,
		jf_id,
		created_at,
		updated_at 
		from sales_bill 
		where jf_id='".$jfId."' and
		deleted_at='0000-00-00 00:00:00'");
		if (count($billData)) {
			return json_encode($billData);
		}else{
			return $exceptionArray['404'];
		}
	}
	/**
	 * get data 
	 * @param  array
	 * @return the data
	*/
	public function getPurchaseBillDataByJfId($jfId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$purchaseIdDataResult = DB::connection($databaseName)->select("select 
			purchase_id,
			vendor_id,
			product_array,
			bill_number,
			bill_type,
			company_id,
			jf_id,
			created_at,
			updated_at 
			from purchase_bill 
			where jf_id='$jfId' and
			deleted_at='0000-00-00 00:00:00'");
			if (count($purchaseIdDataResult)) {
				return json_encode($purchaseIdDataResult);
			}else{
				return $exceptionArray['404'];
			}
	}
	/**
	 * get data 
	 * @param  array
	 * @return the data
	*/
	public function getSalesReturnDataByJfId($jfId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$purchaseIdDataResult = DB::connection($databaseName)->select("select 
			sale_return_id,
			client_id,
			product_array,
			company_id,
			jf_id,
			created_at,
			updated_at 
			from sales_return 
			where jf_id='$jfId' and
			deleted_at='0000-00-00 00:00:00'");
			if (count($purchaseIdDataResult)) {
				return json_encode($purchaseIdDataResult);
			}else{
				return $exceptionArray['404'];
			}
	}
	/**
	 * update data 
	 * @param  data and id
	 * returns the status
	*/
	public function updateSalesBillBySaleId($updateData,$saleId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$separator = '';
		foreach ($updateData as $key => $value) {
			$queryString .= $separator.$key."='".$value."'";
			$separator = ',';
		}
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`sales_bill` SET $queryString
		where sale_id='$saleId' and 
		deleted_at='0000-00-00 00:00:00'
		");
		if ($raw==1) {
			$raw1 = DB::connection($databaseName)->statement("UPDATE 
				`sales_bill_trn` SET $queryString
			where sale_id='$saleId' and 
			deleted_at='0000-00-00 00:00:00'
			");
			if ($raw1==1) {
				return $exceptionArray['200'];
			}else{
				return $exceptionArray['500'];
			}
		}else{
			return $exceptionArray['500'];
		}
	}
	/**
	 * update data 
	 * @param  data and id
	 * returns the status
	*/
	public function updatePurchaseBillByPurchaseId($updateData,$purchaseId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$separator = '';
		foreach ($updateData as $key => $value) {
			$queryString .= $separator.$key."='".$value."'";
			$separator = ',';
		}
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`purchase_bill` SET $queryString
		where purchase_id='$purchaseId' and 
		deleted_at='0000-00-00 00:00:00'
		");
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
	/**
	 * update data 
	 * @param  data and id
	 * returns the status
	*/
	public function updateSalesReturnById($updateData,$returnId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$separator = '';
		foreach ($updateData as $key => $value) {
			$queryString .= $separator.$key."='".$value."'";
			$separator = ',';
		}
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`sales_return` SET $queryString
		where sale_return_id='$returnId' and 
		deleted_at='0000-00-00 00:00:00'
		");
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
	/**
	 * update data 
	 * @param  data and id
	 * returns the status
	*/
	public function updateProductTrnByProductId($updateData,$productId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$separator = '';
		foreach ($updateData as $key => $value) {
			$queryString .= $separator.$key."='".$value."'";
			$separator = ',';
		}
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`product_trn` SET $queryString
		where product_id='$productId' and 
		deleted_at='0000-00-00 00:00:00'
		");
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
	/**
	 * update data 
	 * @param  data and id
	 * returns the status
	*/
	public function updateItemizeTrnByProductId($updateData,$productId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$separator = '';
		foreach ($updateData as $key => $value) {
			$queryString .= $separator.$key."='".$value."'";
			$separator = ',';
		}
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`itemize_trn_dtl` SET $queryString
		where product_id='$productId'
		");
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
	/**
	 * update data 
	 * @param  data and id
	 * returns the status
	*/
	public function updateItemwiseCommissionByProductId($updateData,$productId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$separator = '';
		foreach ($updateData as $key => $value) {
			$queryString .= $separator.$key."='".$value."'";
			$separator = ',';
		}
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`product_commission_dtl` SET $queryString
		where product_id='$productId' and 
		deleted_at='0000-00-00 00:00:00'
		");
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
	/**
	 * soft delete data 
	 * @param  data and id
	 * returns the status
	*/
	public function deleteProductTrnById($trnId)
	{
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		//get exception message
		$mytime = Carbon\Carbon::now();
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		$queryString = '';
		$raw = DB::connection($databaseName)->statement("UPDATE 
			`product_trn` SET deleted_at='$mytime'
		where product_trn_id='$trnId' and 
		deleted_at='0000-00-00 00:00:00'
		");
		if ($raw==1) {
			return $exceptionArray['200'];
		}else{
			return $exceptionArray['500'];
		}
	}
}