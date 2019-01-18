<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait MediumLowerUnitQtyTrait
{
	/**
     * @var mediumLowerUnitQty
     */
    private $mediumLowerUnitQty;
	/**
	 * @param Decimal $mediumLowerUnitQty
	 */
	public function setMediumLowerUnitQty($mediumLowerUnitQty)
	{
		$this->mediumLowerUnitQty = $mediumLowerUnitQty;
	}
	/**
	 * @return mediumLowerUnitQty
	 */
	public function getMediumLowerUnitQty()
	{
		return $this->mediumLowerUnitQty;
	}
}