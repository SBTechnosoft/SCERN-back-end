<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait HeightStatusTrait
{
	/**
     * @var heightStatus
     */
    private $heightStatus;
	/**
	 * @param int $heightStatus
	 */
	public function setHeightStatus($heightStatus)
	{
		$this->heightStatus = $heightStatus;
	}
	/**
	 * @return heightStatus
	 */
	public function getHeightStatus()
	{
		return $this->heightStatus;
	}
}