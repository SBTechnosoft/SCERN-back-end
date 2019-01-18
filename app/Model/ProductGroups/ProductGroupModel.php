<?php
namespace ERP\Model\ProductGroups;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupModel extends Model
{
	protected $table = 'product_group_mst';
	
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
		
		$getProductGrpData = array();
		$getProductGrpKey = array();
		$getProductGrpData = func_get_arg(0);
		$getProductGrpKey = func_get_arg(1);
		$productGrpData="";
		$keyName = "";
		for($data=0;$data<count($getProductGrpData);$data++)
		{
			if($data == (count($getProductGrpData)-1))
			{
				$productGrpData = $productGrpData."'".$getProductGrpData[$data]."'";
				$keyName =$keyName.$getProductGrpKey[$data];
			}
			else
			{
				$productGrpData = $productGrpData."'".$getProductGrpData[$data]."',";
				$keyName =$keyName.$getProductGrpKey[$data].",";
			}
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("insert into product_group_mst(".$keyName.",created_at) 
		values(".$productGrpData.",'".$mytime."')");
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
			
		$getProductGrpData = array();
		$getProductGrpKey = array();
		$getProductGrpData = func_get_arg(0);
		$getProductGrpKey = func_get_arg(1);
		$getErrorArray = func_get_arg(2);
		$productGrpDetail = "";
		$errorCount = count($getErrorArray);
		if(count($getProductGrpData)!=0)
		{
			for($dataArray=0;$dataArray<count($getProductGrpData);$dataArray++)
			{
				
				$productGrpData="";
				$keyName = "";
				for($data=0;$data<count($getProductGrpData[$dataArray]);$data++)
				{
					$flag=1;
					if($data==3)
					{
						$flag=0;
						//replace group-name with parent-group-id
						$convertedString = preg_replace('/[^A-Za-z0-9]/', '',$getProductGrpData[$dataArray][$data]);
						if($convertedString!="")
						{
							//database selection
							DB::beginTransaction();
							$groupIdResult = DB::connection($databaseName)->select("SELECT 
							product_group_id 
							from product_group_mst 
							where REGEXP_REPLACE(product_group_name,'[^a-zA-Z0-9]','')='".$convertedString."' and 
							deleted_at='0000-00-00 00:00:00'");
							DB::commit();
							if(count($groupIdResult)==0)
							{
								//error array
								$getErrorArray[$errorCount] = array();
								$getErrorArray[$errorCount]['productGroupName'] = $getProductCatData[$dataArray][0];
								$getErrorArray[$errorCount]['productGroupDescription'] = $getProductCatData[$dataArray][1];
								$getErrorArray[$errorCount]['isDisplay'] = $getProductCatData[$dataArray][3];
								$getErrorArray[$errorCount]['productParentGroupId'] = $getProductCatData[$dataArray][4];
								$getErrorArray[$errorCount]['remark'] = "plz enter proper group-name in parent-group-name";
								$errorCount++;
							}
							else
							{
								$flag=1;
								$getProductGrpData[$dataArray][$data] = $groupIdResult[0]->product_group_id;
							}
						}
						else
						{
							$flag=1;
							$getProductGrpData[$dataArray][$data]="";
						}
					}
					if($flag==1)
					{
						if($data == (count($getProductGrpData[$dataArray])-1))
						{
							$productGrpData = $productGrpData."'".$getProductGrpData[$dataArray][$data]."'";
							$keyName =$keyName.$getProductGrpKey[$dataArray][$data];
						}
						else
						{
							$productGrpData = $productGrpData."'".$getProductGrpData[$dataArray][$data]."',";
							$keyName =$keyName.$getProductGrpKey[$dataArray][$data].",";
						}
					}
				}
				//database insertion
				DB::beginTransaction();
				$groupInsertionResult = DB::connection($databaseName)->statement("insert into product_group_mst(".$keyName.",created_at) 
				values(".$productGrpData.",'".$mytime."')");
				DB::commit();
				if($groupInsertionResult!=1)
				{
					return $exceptionArray['500'];
				}
			}
			if($groupInsertionResult==1)
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
	 * @param state_abb,state_nameand is_display
	 * returns the status
	*/
	public function updateData($productGrpData,$key,$productGrpId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$mytime = Carbon\Carbon::now();
		$keyValueString="";
		for($data=0;$data<count($productGrpData);$data++)
		{
			$keyValueString=$keyValueString.$key[$data]."='".$productGrpData[$data]."',";
		}
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->statement("update product_group_mst 
		set ".$keyValueString."updated_at='".$mytime."'
		where product_group_id = '".$productGrpId."' and deleted_at='0000-00-00 00:00:00'");
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
		product_group_id,
		product_group_name,
		product_group_description,
		is_display,
		product_group_parent_id,
		created_at,
		updated_at
		from product_group_mst where deleted_at='0000-00-00 00:00:00'");
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
	public function getAllBulkData($productGroupIds)
	{	
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		$implodeString = implode(", ",$productGroupIds);

		DB::beginTransaction();		
		$raw = DB::connection($databaseName)->select("select 
		product_group_id,
		product_group_name,
		product_group_description,
		is_display,
		product_group_parent_id,
		created_at,
		updated_at
		from product_group_mst where deleted_at='0000-00-00 00:00:00' and product_group_id in (".$implodeString.")");
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
	public function getData($productGrpId)
	{
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select 
		product_group_id,
		product_group_name,
		product_group_description,
		is_display,
		product_group_parent_id,
		created_at,
		updated_at
		from product_group_mst where product_group_id = '".$productGrpId."' and deleted_at='0000-00-00 00:00:00'");
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
	 * get data as per given product-Group-Name
	 * @param $productGroupName
	 * returns the error-message/groupId
	*/
	public function getGroupId($productGroupName)
	{
		$flag=0;
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$groupResult = DB::connection($databaseName)->select("SELECT 
		product_group_id,
		product_group_name
		from product_group_mst 
		where deleted_at='0000-00-00 00:00:00'");
		DB::commit();
		
		for($dataArray=0;$dataArray<count($groupResult);$dataArray++)
		{
			$convertedGrpString = strtoupper($groupResult[$dataArray]->product_group_name);
			//replace string of db group-name
			$convertedDbGrpString = preg_replace('/[^A-Za-z0-9]/', '',$convertedGrpString);
			if(strcmp($convertedDbGrpString,$productGroupName)==0)
			{
				$flag=1;
				$groupId = $groupResult[$dataArray]->product_group_id;
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
			return $groupId;
		}
	}
	
	//delete
	public function deleteData($productGrpId)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$mytime = Carbon\Carbon::now();
		$raw = DB::connection($databaseName)->statement("update product_group_mst 
		set deleted_at='".$mytime."'
		where product_group_id = '".$productGrpId."'");
		DB::commit();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($raw==1)
		{
			$productGrp = DB::connection($databaseName)->statement("update product_mst 
			set deleted_at='".$mytime."'
			where product_group_id = '".$productGrpId."'");
			if($productGrp==1)
			{
				$groupId = $this->groupDelete($productGrpId);
				while(strcmp($groupId,'stop')!=0)
				{
					$groupId = $this->groupDelete($groupId);
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
	
	public function groupDelete($groupId)
	{
		$mytime = Carbon\Carbon::now();
		//database selection
		$database = "";
		$constantDatabase = new ConstantClass();
		$databaseName = $constantDatabase->constantDatabase();
		
		DB::beginTransaction();
		$raw = DB::connection($databaseName)->select("select product_group_id 
		from product_group_mst 
		where product_group_parent_id = '".$groupId."' and
		deleted_at = '0000-00-00 00:00:00'");
		DB::commit();
		
		if(count($raw)==0)
		{
			return "stop";
		}
		else
		{
			DB::beginTransaction();
			$productCatRaw = DB::connection($databaseName)->statement("update product_group_mst 
			set deleted_at='".$mytime."'
			where product_group_parent_id='".$groupId."'");
			DB::commit();
			
			DB::beginTransaction();
			$productGrpRaw = DB::connection($databaseName)->statement("update product_mst 
			set deleted_at='".$mytime."'
			where product_group_id='".$raw[0]->product_group_id."'");
			DB::commit();
			return $raw[0]->product_group_id;
		}
	}
}
