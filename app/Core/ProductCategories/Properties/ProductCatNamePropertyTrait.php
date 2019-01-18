<?php
namespace ERP\Core\ProductCategories\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductCatNamePropertyTrait
{
	/**
     * @var productCatName
     */
    private $productCatName;
	/**
	 * @param int $productParentCatName
	 */
	public function setProductCategoryName($productCatName)
	{
		$this->productCatName = $productCatName;
	}
	/**
	 * @return productCatName
	 */
	public function getProductCategoryName()
	{
		return $this->productCatName;
	}
}