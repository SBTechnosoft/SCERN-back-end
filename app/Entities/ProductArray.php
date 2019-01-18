<?php
namespace ERP\Entities;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductArray 
{
    public function productDataArray()
	{
		$productArray = array();
		$productArray[0] = "productname";
		$productArray[1] = "productcategoryid";
		$productArray[2] = "productgroupid";
		$productArray[3] = "color";
		$productArray[4] = "size";
		return $productArray;
	}
	
	public function productValueArray()
	{
		$productArray = array();
		$productArray[0] = "product_name";
		$productArray[1] = "product_category_id";
		$productArray[2] = "product_group_id";
		$productArray[3] = "color";
		$productArray[4] = "size";
		return $productArray;
	}
}
