<?php
namespace ERP\Core\Cities\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CityIdPropertyTrait
{
	/**
     * @var cityId
     */
    private $cityId;
	/**
	 * @param int $cityId
	 */
	public function setCityId($cityId)
	{
		$this->cityId = $cityId;
	}
	/**
	 * @return cityId
	 */
	public function getCityId()
	{
		return $this->cityId;
	}
}