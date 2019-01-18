<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait HigherUnitQtyTrait
{
	/**
     * @var higherUnitQty
     */
    private $higherUnitQty;
	/**
	 * @param Decimal $higherUnitQty
	 */
	public function setHigherUnitQty($higherUnitQty)
	{
		$this->higherUnitQty = $higherUnitQty;
	}
	/**
	 * @return higherUnitQty
	 */
	public function getHigherUnitQty()
	{
		return $this->higherUnitQty;
	}
}