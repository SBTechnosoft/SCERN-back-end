<?php
namespace ERP\Core\Cities\Entities;

use ERP\Core\Cities\Entities\City;
use ERP\Core\States\Services\StateService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends StateService
{
	//date conversion and merge with json data and returns json array
    public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$city = new City();
		$data = array();
		$stateArray = array();
		$encodeDataClass = new EncodeAllData();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$cityName[$decodedData] = $decodedJson[$decodedData]['city_name'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];	
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];	
			
			//get the state details from database
			
			if (!isset($stateArray[$stateAbb[$decodedData]])) {
				$stateArray[$stateAbb[$decodedData]] = $encodeDataClass->getStateData($stateAbb[$decodedData]);
			}
			$stateStatus[$decodedData] = $stateArray[$stateAbb[$decodedData]];
			
			$stateDecodedJson[$decodedData] = json_decode($stateStatus[$decodedData],true);
			$stateName[$decodedData]= $stateDecodedJson[$decodedData]['stateName'];
			$stateIsDisplay[$decodedData]= $stateDecodedJson[$decodedData]['isDisplay'];
			$stateCreatedAt[$decodedData]= $stateDecodedJson[$decodedData]['createdAt'];
			$stateUpdatedAt[$decodedData]= $stateDecodedJson[$decodedData]['updatedAt'];
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$city->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $city->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$city->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $city->getUpdated_at();
			}
			$data[$decodedData]= array(
				'cityName' => $cityName[$decodedData],
				'isDisplay' => $isDisplay[$decodedData],
				'cityId' => $cityId[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' =>$getUpdatedDate[$decodedData],
				
				'state' => array(
					'stateAbb' =>$stateAbb[$decodedData],
					'stateName' => $stateName[$decodedData],
					'isDisplay' => $stateIsDisplay[$decodedData],
					'createdAt' => $stateCreatedAt[$decodedData],
					'updatedAt' => $stateUpdatedAt[$decodedData]
				)
			);
		}
		return json_encode($data);
	}
}