<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DiscountValuePropertyTrait
{
	/**
     * @var discountValue
     */
    private $discountValue;
	/**
	 * @param float $discountValue
	 */
	public function setDiscountValue($discountValue)
	{
		$this->discountValue = $discountValue;
	}
	/**
	 * @return discountValue
	 */
	public function getDiscountValue()
	{
		return $this->discountValue;
	}
}