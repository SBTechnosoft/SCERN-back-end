<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait HigherPurchasePriceTrait
{
	/**
     * @var higherPurchasePrice
     */
    private $higherPurchasePrice;
	/**
	 * @param float $higherPurchasePrice
	 */
	public function setHigherPurchasePrice($higherPurchasePrice)
	{
		$this->higherPurchasePrice = $higherPurchasePrice;
	}
	/**
	 * @return higherPurchasePrice
	 */
	public function getHigherPurchasePrice()
	{
		return $this->higherPurchasePrice;
	}
}