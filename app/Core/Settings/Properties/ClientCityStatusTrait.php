<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientCityStatusTrait
{
	/**
     * @var city
     */
    private $city;
	/**
	 * @param string $city
	 */
	public function setClientCityStatus($city)
	{
		$this->city = $city;
	}
	/**
	 * @return city
	 */
	public function getClientCityStatus()
	{
		return $this->city;
	}
}