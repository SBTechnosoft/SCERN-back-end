<?php
namespace ERP\Core\Settings\MeasurementUnits\Entities;

use ERP\Core\Settings\MeasurementUnits\Entities\Measurement;
use Carbon;
/**
 *
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class EncodeData
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt= $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		
		//date format conversion
		$measurement = new Measurement();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$measurement->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $measurement->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$measurement->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $measurement->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['measurementUnitId'] = $decodedJson[0]['measurement_unit_id'];
		$data['unitName'] = $decodedJson[0]['unit_name'];
		$data['lengthStatus'] = $decodedJson[0]['length_status'];
		$data['widthStatus'] = $decodedJson[0]['width_status'];
		$data['heightStatus'] = $decodedJson[0]['height_status'];
		$data['devideFactor'] = $decodedJson[0]['devide_factor'] <= 0 ? '1' : $decodedJson[0]['devide_factor'];
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;
		$encodeData = json_encode($data);
		return $encodeData;
	}
}