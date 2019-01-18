<?php
namespace ERP\Core\Settings\MeasurementUnits\Entities;

use ERP\Core\Settings\MeasurementUnits\Entities\Measurement;
use Carbon;
/**
 *
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class EncodeAllData
{
	public function getEncodedAllData($status)
	{
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$measurement = new Measurement();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$measurement->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $measurement->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$measurement->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $measurement->getUpdated_at();
			}
			$data[$decodedData]= array(
				'measurementUnitId'=> $decodedJson[$decodedData]['measurement_unit_id'],
				'unitName' => $decodedJson[$decodedData]['unit_name'],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData]
			);
		}
		return json_encode($data);
	}
}