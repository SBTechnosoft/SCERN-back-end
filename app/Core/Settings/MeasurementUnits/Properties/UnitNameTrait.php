<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait UnitNameTrait
{
	/**
     * @var unitName
     */
    private $unitName;
	/**
	 * @param string $unitName
	 */
	public function setUnitName($unitName)
	{
		$this->unitName = $unitName;
	}
	/**
	 * @return unitName
	 */
	public function getUnitName()
	{
		return $this->unitName;
	}
}