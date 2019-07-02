<?php
namespace ERP\Core\Settings\MeasurementUnits\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait ModuloFactorTrait
{
	/**
     * @var moduloFactor
     */
    private $moduloFactor;
	/**
	 * @param int $moduloFactor
	 */
	public function setModuloFactor($moduloFactor)
	{
		$this->moduloFactor = $moduloFactor;
	}
	/**
	 * @return moduloFactor
	 */
	public function getModuloFactor()
	{
		return $this->moduloFactor;
	}
}