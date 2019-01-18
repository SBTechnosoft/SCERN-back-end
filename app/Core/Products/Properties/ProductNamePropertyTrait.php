<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductNamePropertyTrait
{
	/**
     * @var productName
     */
    private $productName;
	/**
	 * @param int $productName
	 */
	public function setProductName($productName)
	{
		$this->productName = $productName;
	}
	/**
	 * @return productName
	 */
	public function getProductName()
	{
		return $this->productName;
	}
}