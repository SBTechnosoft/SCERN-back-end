<?php
namespace ERP\Core\ProductCategories\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductCatIdPropertyTrait
{
	/**
     * @var productParentCatId
     */
    private $productCatId;
	/**
	 * @param int $productParentCatId
	 */
	public function setProductCategoryId($productCatId)
	{
		$this->productCatId = $productCatId;
	}
	/**
	 * @return productParentCatId
	 */
	public function getProductCategoryId()
	{
		return $this->productCatId;
	}
}