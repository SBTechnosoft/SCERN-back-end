<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait HighestMeasurementUnitIdTrait
{
	/**
     * @var highestMeasurementIdUnit
     */
    private $highestMeasurementIdUnit;
	/**
	 * @param Integer $highestMeasurementIdUnit
	 */
	public function setHighestMeasurementUnitId($highestMeasurementIdUnit)
	{
		$this->highestMeasurementIdUnit = $highestMeasurementIdUnit;
	}
	/**
	 * @return highestMeasurementIdUnit
	 */
	public function getHighestMeasurementUnitId()
	{
		return $this->highestMeasurementIdUnit;
	}
}