<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DiscountPropertyTrait
{
	/**
     * @var discount
     */
    private $discount;
	/**
	 * @param float $discount
	 */
	public function setDiscount($discount)
	{
		$this->discount = $discount;
	}
	/**
	 * @return discount
	 */
	public function getDiscount()
	{
		return $this->discount;
	}
}