<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait LowestUnitQtyTrait
{
	/**
     * @var lowestUnitQty
     */
    private $lowestUnitQty;
	/**
	 * @param Decimal $lowestUnitQty
	 */
	public function setLowestUnitQty($lowestUnitQty)
	{
		$this->lowestUnitQty = $lowestUnitQty;
	}
	/**
	 * @return lowestUnitQty
	 */
	public function getLowestUnitQty()
	{
		return $this->lowestUnitQty;
	}
}