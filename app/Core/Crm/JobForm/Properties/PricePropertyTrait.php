<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $price
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