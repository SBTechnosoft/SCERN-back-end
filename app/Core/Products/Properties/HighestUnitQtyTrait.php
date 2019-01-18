<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait HighestUnitQtyTrait
{
	/**
     * @var highestUnitQty
     */
    private $highestUnitQty;
	/**
	 * @param Decimal $highestUnitQty
	 */
	public function setHighestUnitQty($highestUnitQty)
	{
		$this->highestUnitQty = $highestUnitQty;
	}
	/**
	 * @return highestUnitQty
	 */
	public function getHighestUnitQty()
	{
		return $this->highestUnitQty;
	}
}