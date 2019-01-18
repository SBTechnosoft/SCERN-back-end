<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductIdPropertyTrait
{
	/**
     * @var productId
     */
    private $productId;
	/**
	 * @param int $productId
	 */
	public function setProductId($productId)
	{
		$this->productId = $productId;
	}
	/**
	 * @return productId
	 */
	public function getProductId()
	{
		return $this->productId;
	}
}