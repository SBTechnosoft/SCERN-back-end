<?php
namespace ERP\Core\Cities\Entities;

use ERP\Core\Cities\Entities\City;
use ERP\Core\States\Services\StateService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends StateService
{
	//date conversion and merge with json data and returns json array
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$cityName= $decodedJson[0]['city_name'];
		$cityId = $decodedJson[0]['city_id'];
		$isDisplay= $decodedJson[0]['is_display'];
		$stateAbb= $decodedJson[0]['state_abb'];
		
		//get the state_name from database
		$encodeDataClass = new EncodeData();
		$stateStatus = $encodeDataClass->getStateData($stateAbb);
		$stateDecodedJson = json_decode($stateStatus,true);
		
		//date format conversion
		$city = new City();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$city->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $city->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$city->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $city->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['cityName'] = $cityName;
		$data['isDisplay'] = $isDisplay;
		$data['cityId'] = $cityId;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;
		
		$data['state'] = array(
			'stateAbb' => $stateDecodedJson['stateAbb'],
			'stateName' => $stateDecodedJson['stateName'],	
			'isDisplay' => $stateDecodedJson['isDisplay'],	
			'createdAt' => $stateDecodedJson['createdAt'],	
			'updatedAt' => $stateDecodedJson['updatedAt']
		);
		$encodeData = json_encode($data);
		return $encodeData;
	}
}