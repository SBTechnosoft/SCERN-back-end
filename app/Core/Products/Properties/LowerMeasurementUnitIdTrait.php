<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait LowerMeasurementUnitIdTrait
{
	/**
     * @var lowerMeasurementIdUnit
     */
    private $lowerMeasurementIdUnit;
	/**
	 * @param Integer $lowerMeasurementIdUnit
	 */
	public function setLowerMeasurementUnitId($lowerMeasurementIdUnit)
	{
		$this->lowerMeasurementIdUnit = $lowerMeasurementIdUnit;
	}
	/**
	 * @return lowerMeasurementIdUnit
	 */
	public function getLowerMeasurementUnitId()
	{
		return $this->lowerMeasurementIdUnit;
	}
}