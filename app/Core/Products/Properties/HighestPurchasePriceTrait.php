<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait HighestPurchasePriceTrait
{
	/**
     * @var highestPurchasePrice
     */
    private $highestPurchasePrice;
	/**
	 * @param float $highestPurchasePrice
	 */
	public function setHighestPurchasePrice($highestPurchasePrice)
	{
		$this->highestPurchasePrice = $highestPurchasePrice;
	}
	/**
	 * @return highestPurchasePrice
	 */
	public function getHighestPurchasePrice()
	{
		return $this->highestPurchasePrice;
	}
}