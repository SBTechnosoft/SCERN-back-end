<?php
namespace ERP\Core\States\Entities;

use ERP\Core\States\Entities\State;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData
{
	//date conversion and merge with json data and returns json array
    public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
			
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$isDisplay= $decodedJson[0]['is_display'];
		$stateAbb= $decodedJson[0]['state_abb'];
		$stateName= $decodedJson[0]['state_name'];
		$stateCode= $decodedJson[0]['state_code'];
		
		//date format conversion
		$state = new State();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$state->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $state->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$state->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $state->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['stateName'] = $stateName;
		$data['stateAbb'] = $stateAbb;
		$data['isDisplay'] = $isDisplay;
		$data['stateCode'] = $stateCode;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		
		$encodeData = json_encode($data);
		return $encodeData;
	}
}