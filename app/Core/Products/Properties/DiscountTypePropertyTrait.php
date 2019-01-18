<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait DiscountTypePropertyTrait
{
	/**
     * @var discountType
     */
    private $discountType;
	/**
	 * @param float $discountType
	 */
	public function setDiscountType($discountType)
	{
		$this->discountType = $discountType;
	}
	/**
	 * @return discountType
	 */
	public function getDiscountType()
	{
		return $this->discountType;
	}
}