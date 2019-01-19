<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait ProductVariantStatusTrait
{
	/**
     * @var productVariant
     */
    private $productVariant;
	/**
	 * @param string $productVariant
	 */
	public function setProductVariantStatus($productVariant)
	{
		$this->productVariant = $productVariant;
	}
	/**
	 * @return productVariant
	 */
	public function getProductVariantStatus()
	{
		return $this->productVariant;
	}
}