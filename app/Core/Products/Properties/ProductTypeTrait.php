<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductTypeTrait
{
	/**
     * @var productType
     */
    private $productType;
	/**
	 * @param float $productType
	 */
	public function setProductType($productType)
	{
		$this->productType = $productType;
	}
	/**
	 * @return productType
	 */
	public function getProductType()
	{
		return $this->productType;
	}
}