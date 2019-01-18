<?php
namespace ERP\Core\ProductCategories\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductParentCatIdPropertyTrait
{
	/**
     * @var productParentCatId
     */
    private $productParentCatId;
	/**
	 * @param int $productParentCatId
	 */
	public function setProductParentCategoryId($productParentCatId)
	{
		$this->productParentCatId = $productParentCatId;
	}
	/**
	 * @return productParentCatId
	 */
	public function getProductParentCategoryId()
	{
		return $this->productParentCatId;
	}
}