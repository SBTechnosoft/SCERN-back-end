<?php
namespace ERP\Core\ProductGroups\Entities;

use ERP\Core\ProductGroups\Entities\State;
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
		$productGroup = new ProductGroup();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$productGrpId[$decodedData] = $decodedJson[$decodedData]['product_group_id'];
			$productGrpName[$decodedData] = $decodedJson[$decodedData]['product_group_name'];
			$productGrpDesc[$decodedData] = $decodedJson[$decodedData]['product_group_description'];
			$productGrpParentId[$decodedData] = $decodedJson[$decodedData]['product_group_parent_id'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$productGroup->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $productGroup->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$productGroup->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $productGroup->getUpdated_at();
			}
			$data[$decodedData]= array(
				'productGroupName' => $productGrpName[$decodedData],
				'productGroupId' =>$productGrpId[$decodedData],
				'productGroupDescription' =>$productGrpDesc[$decodedData],
				'isDisplay' => $isDisplay[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' =>$getUpdatedDate[$decodedData],
				'productGroupParentId' =>$productGrpParentId[$decodedData]
			);
		}
		
		return json_encode($data);
	}
}