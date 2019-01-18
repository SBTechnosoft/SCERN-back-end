<?php
namespace ERP\Core\Entities;

use ERP\Core\Cities\Services\CityService;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityDetail extends CityService 
{
	public function getCityDetail($cityId)
	{
		//get the city Details from database
		$encodeCityDataClass = new CityDetail();
		$cityStatus = $encodeCityDataClass->getCityData($cityId);
		$cityDecodedJson = json_decode($cityStatus,true);
		return $cityDecodedJson;
	}
}