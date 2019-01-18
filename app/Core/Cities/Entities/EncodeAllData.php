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
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$cityName[$decodedData] = $decodedJson[$decodedData]['city_name'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];	
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];	
			
			//get the state details from database
			$encodeDataClass = new EncodeAllData();
			$stateStatus[$decodedData] = $encodeDataClass->getStateData($stateAbb[$decodedData]);
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
		}
		
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'cityName' => $cityName[$jsonData],
				'isDisplay' => $isDisplay[$jsonData],
				'cityId' => $cityId[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' =>$getUpdatedDate[$jsonData],
				
				'state' => array(
					'stateAbb' =>$stateAbb[$jsonData],
					'stateName' => $stateName[$jsonData],
					'isDisplay' => $stateIsDisplay[$jsonData],
					'createdAt' => $stateCreatedAt[$jsonData],
					'updatedAt' => $stateUpdatedAt[$jsonData]
				)
			);
		}
		return json_encode($data);
	}
}