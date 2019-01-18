<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait MediumLowerMeasurementUnitIdTrait
{
	/**
     * @var mediumLowerMeasurementIdUnit
     */
    private $mediumLowerMeasurementIdUnit;
	/**
	 * @param Integer $mediumLowerMeasurementIdUnit
	 */
	public function setMediumLowerMeasurementUnitId($mediumLowerMeasurementIdUnit)
	{
		$this->mediumLowerMeasurementIdUnit = $mediumLowerMeasurementIdUnit;
	}
	/**
	 * @return mediumLowerMeasurementIdUnit
	 */
	public function getMediumLowerMeasurementUnitId()
	{
		return $this->mediumLowerMeasurementIdUnit;
	}
}