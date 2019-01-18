<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait QuantityWisePricingTrait
{
	/**
     * @var quantityWisePricing
     */
    private $quantityWisePricing;
	/**
	 * @param json $quantityWisePricing
	 */
	public function setQuantityWisePricing($quantityWisePricing)
	{
		$this->quantityWisePricing = $quantityWisePricing;
	}
	/**
	 * @return quantityWisePricing
	 */
	public function getQuantityWisePricing()
	{
		return $this->quantityWisePricing;
	}
}