<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PricePropertyTrait
{
	/**
     * @var price
     */
    private $price;
	/**
	 * @param float $price
	 */
	public function setPrice($price)
	{
		$this->price = $price;
	}
	/**
	 * @return price
	 */
	public function getPrice()
	{
		return $this->price;
	}
}