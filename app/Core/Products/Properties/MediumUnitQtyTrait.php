<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait MediumUnitQtyTrait
{
	/**
     * @var mediumUnitQty
     */
    private $mediumUnitQty;
	/**
	 * @param Decimal $mediumUnitQty
	 */
	public function setMediumUnitQty($mediumUnitQty)
	{
		$this->mediumUnitQty = $mediumUnitQty;
	}
	/**
	 * @return mediumUnitQty
	 */
	public function getMediumUnitQty()
	{
		return $this->mediumUnitQty;
	}
}