<?php
namespace ERP\Model\ProductCategories;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategoryModel extends Model
{
	protected $table = 'product_category_mst';
	
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
		
		$getProductCatData = array();
		$getProductCatKey = array();
		$getProductCatData = func_get_arg(0);
		$getProductCatKey = func_get_arg(1);
		$productCatData="";
		$keyName = "";
		for($data=0;$data<count($getProductCatData);$data++)
		{
			if($data == (count($getProductCatData)-1))
			{
				$productCatData = $productCatData."'".$getProductCatData[$data]."'";
				$keyName =$keyName.$getProductCatKey[$data];
			}
			else
			{
				$productCatData = $productCatData."'".$getProductCatData[$data]."',";
				$keyName =$keyName.$getProductCatKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into product_category_mst(".$keyName.",created_at) 
		values(".$productCatData.",'".$mytime."')");
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
	 * insert batch of data 
	 * @param  array
	 * returns the status
	*/
	public function insertBatchData()
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
			
		$getProductCatData = array();
		$getProductCatKey = array();
		$getProductCatData = func_get_arg(0);
		$getProductCatKey = func_get_arg(1);
		$getErrorArray = func_get_arg(2);
		$productCatDetail = "";
		
		$errorCount = count($getErrorArray);
		if(count($getProductCatData)!=0)
		{
			for($dataArray=0;$dataArray<count($getProductCatData);$dataArray++)
			{
				$productCatData="";
				$keyName = "";
				for($data=0;$data<count($getProductCatData[$dataArray]);$data++)
				{
					$flag=1;
					if($data==3)
					{
						$flag=0;
						//replace group-name with parent-group-id
						$convertedString = preg_replace('/[^A-Za-z0-9]/', '',$getProductCatData[$dataArray][$data]);
						if($convertedString!="")
						{
							//database selection
							DB::beginTransaction();
							$categoryIdResult = DB::connection($databaseName)->select("SELECT 
							product_category_id 
							from product_category_mst 
							where REGEXP_REPLACE(product_category_name,'[^a-zA-Z0-9]','')='".$convertedString."' and 
							deleted_at='0000-00-00 00:00:00'");
							DB::commit();
							if(count($categoryIdResult)==0)
							{
								$getErrorArray[$errorCount] = array();
								//through error
								$getErrorArray[$errorCount]['productCategoryName'] = $getProductCatData[$dataArray][0];
								$getErrorArray[$errorCount]['productCategoryDescription'] = $getProductCatData[$dataArray][1];
								$getErrorArray[$errorCount]['isDisplay'] = $getProductCatData[$dataArray][2];
								$getErrorArray[$errorCount]['productParentCategoryId'] = $getProductCatData[$dataArray][3];
								$getErrorArray[$errorCount]['remark'] = "plz enter proper category-name in parent-category-name";
								$errorCount++;
							}
							else
							{
								$flag=1;
								$getProductCatData[$dataArray][$data] = $categoryIdResult[0]->product_category_id;
							}
						}
						else
						{
							$flag=1;
							$getProductCatData[$dataArray][$data]="";
						}
					}
					if($flag==1)
					{
						if($data == (count($getProductCatData[$dataArray])-1))
						{
							$productCatData = $productCatData."'".$getProductCatData[$dataArray][$data]."'";
							$keyName =$keyName.$getProductCatKey[$dataArray][$data];
						}
						else
						{
							$productCatData = $productCatData."'".$getProductCatData[$dataArray][$data]."',";
							$keyName =$keyName.$getProductCatKey[$dataArray][$data].",";
						}
					}
				}
				//database insertion
				DB::beginTransaction();
				$categoryInsertionResult = DB::connection($databaseName)->statement("insert into product_category_mst(".$keyName.",created_at) 
				values(".$productCatData.",'".$mytime."')");
				DB::commit();
				if($categoryInsertionResult!=1)
				{
					return $exceptionArray['500'];
				}
			}
			if($categoryInsertionResult==1)
			{
				if(count($getErrorArray)==0)
				{
					return $exceptionArray['200'];
				}
				else
				{
					return json_encode($getErrorArray);
				}
			}
		}
		else
		{
			if(count($getErrorArray)==0)
			{
				return $exceptionArray['500'];
			}
			else
			{
				return json_encode($getErrorArray);
			}
		}
	}
	/**
	 * update data 
	 * @param productCatData,$key of productCatData,productCatId
	 * returns the status
	*/
	public function updateData($productCatData,$key,$productCatId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($productCatData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$productCatData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update product_category_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where product_category_id ='".$productCatId."' and deleted_at='0000-00-00 00:00:00'");
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
		product_category_id,
		product_category_name,
		product_category_description,
		is_display,
		product_parent_category_id,
		created_at,
		updated_at
		from product_category_mst where deleted_at='0000-00-00 00:00:00'");
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
	 * get All data 
	 * returns the status
	*/
	public function getAllBulkData($productCategoryIds)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$implodeString = implode(", ",$productCategoryIds);
		
		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		product_category_id,
		product_category_name,
		product_category_description,
		is_display,
		product_parent_category_id,
		created_at,
		updated_at
		from product_category_mst where deleted_at='0000-00-00 00:00:00' and product_category_id in (".$implodeString.")");
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
	 * get data as per given product_Cat_Id
	 * @param $productCategoryId
	 * returns the status
	*/
	public function getData($productCategoryId)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		product_category_id,
		product_category_name,
		product_category_description,
		is_display,
		product_parent_category_id,
		created_at,
		updated_at
		from product_category_mst where product_category_id = '".$productCategoryId."' and deleted_at='0000-00-00 00:00:00'");
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
	 * get data as per given product-Category-Name
	 * @param $productCategoryName
	 * returns the error-message/categoryId
	*/
	public function getCategoryId($convertedCategoryName)
	{
		$flag=0;
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$categoryResult = DB::connection($databaseName)->select("SELECT 
		product_category_id,
		product_category_name 
		from product_category_mst 
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		for($dataArray=0;$dataArray<count($categoryResult);$dataArray++)
		{
			$convertedCatString = strtoupper($categoryResult[$dataArray]->product_category_name);
			//replace string of db category-name
			$convertedDbCatString = preg_replace('/[^A-Za-z0-9]/', '',$convertedCatString);
			if(strcmp($convertedDbCatString,$convertedCategoryName)==0)
			{
				$flag=1;
				$categoryId = $categoryResult[$dataArray]->product_category_id;
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
			return $categoryId;
		}
	}
	
	//delete
	public function deleteData($productCatId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$raw = DB::connection($databaseName)->statement("update product_category_mst 
		set deleted_at='".$mytime."'
		where product_category_id = '".$productCatId."'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			$product = DB::connection($databaseName)->statement("update product_mst 
			set deleted_at='".$mytime."'
			where product_category_id = '".$productCatId."'");
			if($product==1)
			{
				$categoryId = $this->categoryDelete($productCatId);
				while(strcmp($categoryId,'stop')!=0)
				{
					$categoryId = $this->categoryDelete($categoryId);
				}
				return $exceptionArray['200'];
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
	
	public function categoryDelete($categoryId)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select product_category_id 
		from product_category_mst 
		where product_parent_category_id = '".$categoryId."' and
		deleted_at = '0000-00-00 00:00:00'");
		DB::commit();
		
		if(count($raw)==0)
		{
			return "stop";
		}
		else
		{
			DB::beginTransaction();
			$productCatRaw = DB::connection($databaseName)->statement("update product_category_mst 
			set deleted_at='".$mytime."'
			where product_parent_category_id='".$categoryId."'");
			DB::commit();
			
			DB::beginTransaction();
			$productCatRaww = DB::connection($databaseName)->statement("update product_mst 
			set deleted_at='".$mytime."'
			where product_category_id='".$raw[0]->product_category_id."'");
			DB::commit();
			return $raw[0]->product_category_id;
		}
	}
}
