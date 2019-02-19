<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait WidthStatusTrait
{
	/**
     * @var widthStatus
     */
    private $widthStatus;
	/**
	 * @param int $widthStatus
	 */
	public function setWidthStatus($widthStatus)
	{
		$this->widthStatus = $widthStatus;
	}
	/**
	 * @return widthStatus
	 */
	public function getWidthStatus()
	{
		return $this->widthStatus;
	}
}