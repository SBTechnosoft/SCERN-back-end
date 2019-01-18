<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PurchasePricePropertyTrait
{
	/**
     * @var purchasePrice
     */
    private $purchasePrice;
	/**
	 * @param float $purchasePrice
	 */
	public function setPurchasePrice($purchasePrice)
	{
		$this->purchasePrice = $purchasePrice;
	}
	/**
	 * @return purchasePrice
	 */
	public function getPurchasePrice()
	{
		return $this->purchasePrice;
	}
}