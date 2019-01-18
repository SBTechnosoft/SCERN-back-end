<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $discountType
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