<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MediumLowerPurchasePriceTrait
{
	/**
     * @var mediumLowerPurchasePrice
     */
    private $mediumLowerPurchasePrice;
	/**
	 * @param float $mediumLowerPurchasePrice
	 */
	public function setMediumLowerPurchasePrice($mediumLowerPurchasePrice)
	{
		$this->mediumLowerPurchasePrice = $mediumLowerPurchasePrice;
	}
	/**
	 * @return mediumLowerPurchasePrice
	 */
	public function getMediumLowerPurchasePrice()
	{
		return $this->mediumLowerPurchasePrice;
	}
}