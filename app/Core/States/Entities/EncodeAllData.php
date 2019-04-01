<?php
namespace ERP\Core\States\Entities;

use ERP\Core\States\Entities\State;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData
{
	//date conversion and merge with json data and returns json array
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$state = new State();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$stateName[$decodedData] = $decodedJson[$decodedData]['state_name'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			$stateCode[$decodedData] = $decodedJson[$decodedData]['state_code'];
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$state->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $state->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$state->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $state->getUpdated_at();
			}
			$data[$decodedData]= array(
				'stateName' => $stateName[$decodedData],
				'stateAbb' =>$stateAbb[$decodedData],
				'isDisplay' => $isDisplay[$decodedData],
				'stateCode' => $stateCode[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' =>$getUpdatedDate[$decodedData]
				
			);
		}
		return json_encode($data);
	}
}