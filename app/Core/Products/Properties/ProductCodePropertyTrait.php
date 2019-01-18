<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductCodePropertyTrait
{
	/**
     * @var productCode
     */
    private $productCode;
	/**
	 * @param float $productCode
	 */
	public function setProductCode($productCode)
	{
		$this->productCode = $productCode;
	}
	/**
	 * @return productCode
	 */
	public function getProductCode()	
	{
		return $this->productCode;
	}
}