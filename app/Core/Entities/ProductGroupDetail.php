<?php
namespace ERP\Core\Entities;

use ERP\Core\ProductGroups\Services\ProductGroupService;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupDetail extends ProductGroupService 
{
	public function getProductGrpDetails($productGrpId)
	{
		//get the city_name from database
		$encodeProductGrpDataClass = new ProductGroupDetail();
		$productGrpStatus = $encodeProductGrpDataClass->getProductGrpData($productGrpId);
		$productGrpDecodedJson = json_decode($productGrpStatus,true);
		
		// $companyName= $companyDecodedJson['company_name'];
		return $productGrpDecodedJson;
	}
    
}