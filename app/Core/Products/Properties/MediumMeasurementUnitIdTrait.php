<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MediumMeasurementUnitIdTrait
{
	/**
     * @var mediumMeasurementIdUnit
     */
    private $mediumMeasurementIdUnit;
	/**
	 * @param Integer $mediumMeasurementIdUnit
	 */
	public function setMediumMeasurementUnitId($mediumMeasurementIdUnit)
	{
		$this->mediumMeasurementIdUnit = $mediumMeasurementIdUnit;
	}
	/**
	 * @return mediumMeasurementIdUnit
	 */
	public function getMediumMeasurementUnitId()
	{
		return $this->mediumMeasurementIdUnit;
	}
}