<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait DevideFactorTrait
{
	/**
     * @var devideFactor
     */
    private $devideFactor;
	/**
	 * @param int $devideFactor
	 */
	public function setDevideFactor($devideFactor)
	{
		$this->devideFactor = $devideFactor;
	}
	/**
	 * @return devideFactor
	 */
	public function getDevideFactor()
	{
		return $this->devideFactor;
	}
}