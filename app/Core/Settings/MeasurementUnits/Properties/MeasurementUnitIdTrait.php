<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MeasurementUnitIdTrait
{
	/**
     * @var measurementUnitId
     */
    private $measurementUnitId;
	/**
	 * @param int $measurementUnitId
	 */
	public function setMeasurementUnitId($measurementUnitId)
	{
		$this->measurementUnitId = $measurementUnitId;
	}
	/**
	 * @return measurementUnitId
	 */
	public function getMeasurementUnitId()
	{
		return $this->measurementUnitId;
	}
}