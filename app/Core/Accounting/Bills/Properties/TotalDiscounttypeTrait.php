<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TotalDiscounttypeTrait
{
	/**
     * @var totalDiscountType
     */
    private $totalDiscountType;
	/**
	 * @param float $totalDiscountType
	 */
	public function setTotalDiscounttype($totalDiscountType)
	{
		$this->totalDiscountType = $totalDiscountType;
	}
	/**
	 * @return totalDiscountType
	 */
	public function getTotalDiscounttype()
	{
		return $this->totalDiscountType;
	}
}