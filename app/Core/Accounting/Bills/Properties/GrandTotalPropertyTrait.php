<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait GrandTotalPropertyTrait
{
	/**
     * @var grandTotal
     */
    private $grandTotal;
	/**
	 * @param float $grandTotal
	 */
	public function setGrandTotal($grandTotal)
	{
		$this->grandTotal = $grandTotal;
	}
	/**
	 * @return grandTotal
	 */
	public function getGrandTotal()
	{
		return $this->grandTotal;
	}
}