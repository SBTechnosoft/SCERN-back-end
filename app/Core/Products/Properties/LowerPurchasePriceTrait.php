<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait LowerPurchasePriceTrait
{
	/**
     * @var lowerPurchasePrice
     */
    private $lowerPurchasePrice;
	/**
	 * @param float $lowerPurchasePrice
	 */
	public function setLowerPurchasePrice($lowerPurchasePrice)
	{
		$this->lowerPurchasePrice = $lowerPurchasePrice;
	}
	/**
	 * @return lowerPurchasePrice
	 */
	public function getLowerPurchasePrice()
	{
		return $this->lowerPurchasePrice;
	}
}