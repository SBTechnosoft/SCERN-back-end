<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait HigherMeasurementUnitIdTrait
{
	/**
     * @var higherMeasurementIdUnit
     */
    private $higherMeasurementIdUnit;
	/**
	 * @param Integer $higherMeasurementIdUnit
	 */
	public function setHigherMeasurementUnitId($higherMeasurementIdUnit)
	{
		$this->higherMeasurementIdUnit = $higherMeasurementIdUnit;
	}
	/**
	 * @return higherMeasurementIdUnit
	 */
	public function getHigherMeasurementUnitId()
	{
		return $this->higherMeasurementIdUnit;
	}
}