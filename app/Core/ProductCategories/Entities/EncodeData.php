<?php
namespace ERP\Core\ProductCategories\Entities;

use ERP\Core\ProductCategories\Entities\ProductCategory;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData
{
	
    public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
			
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$isDisplay= $decodedJson[0]['is_display'];
		$productCatId= $decodedJson[0]['product_category_id'];
		$productCatName= $decodedJson[0]['product_category_name'];
		$productCatDesc= $decodedJson[0]['product_category_description'];
		$productParentCatId= $decodedJson[0]['product_parent_category_id'];
		
		//date format conversion['created_at','updated_at']
		$productCategory = new ProductCategory();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$productCategory->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $productCategory->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{	
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$productCategory->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $productCategory->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['productCategoryName'] = $productCatName;
		$data['productCategoryId'] = $productCatId;
		$data['productCategoryDescription'] = $productCatDesc;
		$data['isDisplay'] = $isDisplay;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['productParentCategoryId'] = $productParentCatId;	
		
		$encodeData = json_encode($data);
		return $encodeData;
	}
}