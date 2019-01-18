<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MediumPurchasePriceTrait
{
	/**
     * @var mediumPurchasePrice
     */
    private $mediumPurchasePrice;
	/**
	 * @param float $mediumPurchasePrice
	 */
	public function setMediumPurchasePrice($mediumPurchasePrice)
	{
		$this->mediumPurchasePrice = $mediumPurchasePrice;
	}
	/**
	 * @return mediumPurchasePrice
	 */
	public function getMediumPurchasePrice()
	{
		return $this->mediumPurchasePrice;
	}
}