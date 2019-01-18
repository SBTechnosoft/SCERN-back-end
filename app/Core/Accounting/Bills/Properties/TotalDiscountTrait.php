<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TotalDiscountTrait
{
	/**
     * @var totalDiscount
     */
    private $totalDiscount;
	/**
	 * @param float $totalDiscount
	 */
	public function setTotalDiscount($totalDiscount)
	{
		$this->totalDiscount = $totalDiscount;
	}
	/**
	 * @return totalDiscount
	 */
	public function getTotalDiscount()
	{
		return $this->totalDiscount;
	}
}