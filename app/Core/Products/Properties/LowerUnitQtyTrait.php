<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait LowerUnitQtyTrait
{
	/**
     * @var lowerUnitQty
     */
    private $lowerUnitQty;
	/**
	 * @param Decimal $lowerUnitQty
	 */
	public function setLowerUnitQty($lowerUnitQty)
	{
		$this->lowerUnitQty = $lowerUnitQty;
	}
	/**
	 * @return lowerUnitQty
	 */
	public function getLowerUnitQty()
	{
		return $this->lowerUnitQty;
	}
}