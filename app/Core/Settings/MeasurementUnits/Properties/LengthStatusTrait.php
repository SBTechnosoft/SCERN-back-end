<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait LengthStatusTrait
{
	/**
     * @var lengthStatus
     */
    private $lengthStatus;
	/**
	 * @param int $lengthStatus
	 */
	public function setLengthStatus($lengthStatus)
	{
		$this->lengthStatus = $lengthStatus;
	}
	/**
	 * @return lengthStatus
	 */
	public function getLengthStatus()
	{
		return $this->lengthStatus;
	}
}