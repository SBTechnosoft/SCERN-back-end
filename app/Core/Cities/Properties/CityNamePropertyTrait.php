<?php
namespace ERP\Core\Cities\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CityNamePropertyTrait
{
	/**
     * @var cityName
     */
    private $cityName;
	/**
	 * @param int $cityName
	 */
	public function setCityName($cityName)
	{
		$this->cityName = $cityName;
	}
	/**
	 * @return cityName
	 */
	public function getCityName()
	{
		return $this->cityName;
	}
}