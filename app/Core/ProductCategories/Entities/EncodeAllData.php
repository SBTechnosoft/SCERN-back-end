<?php
namespace ERP\Core\ProductCategories\Entities;

use ERP\Core\ProductCategories\Entities\State;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData
{
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
			
		$decodedJson = json_decode($status,true);
		$productCategory = new ProductCategory();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$productCatId[$decodedData] = $decodedJson[$decodedData]['product_category_id'];
			$productCatName[$decodedData] = $decodedJson[$decodedData]['product_category_name'];
			$productCatDesc[$decodedData] = $decodedJson[$decodedData]['product_category_description'];
			$productParentCatId[$decodedData] = $decodedJson[$decodedData]['product_parent_category_id'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$productCategory->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $productCategory->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$productCategory->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $productCategory->getUpdated_at();
			}
			$data[$decodedData]= array(
				'productCategoryName' => $productCatName[$decodedData],
				'productCategoryId' =>$productCatId[$decodedData],
				'productCategoryDescription' =>$productCatDesc[$decodedData],
				'isDisplay' => $isDisplay[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' =>$getUpdatedDate[$decodedData],
				'productParentCategoryId' =>$productParentCatId[$decodedData]
			);
		}
		return json_encode($data);
	}
}