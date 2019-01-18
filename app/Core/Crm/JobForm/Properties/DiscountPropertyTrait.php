<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $discount
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