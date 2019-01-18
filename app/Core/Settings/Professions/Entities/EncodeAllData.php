<?php
namespace ERP\Core\Settings\Professions\Entities;

use ERP\Core\Settings\Professions\Entities\Profession;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData
{
	public function getEncodedAllData($status)
	{
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$profession = new Profession();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$professionId[$decodedData] = $decodedJson[$decodedData]['profession_id'];
			$professionName[$decodedData] = $decodedJson[$decodedData]['profession_name'];
			$description[$decodedData] = $decodedJson[$decodedData]['description'];
			$professionParentId[$decodedData] = $decodedJson[$decodedData]['profession_parent_id'];
			if($professionParentId[$decodedData]==0)
			{
				$professionParentId[$decodedData]='';
			}
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$profession->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $profession->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$profession->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $profession->getUpdated_at();
			}
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'professionId'=>$professionId[$jsonData],
				'professionName' => $professionName[$jsonData],
				'description' => $description[$jsonData],
				'professionParentId' => $professionParentId[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData]
			);
		}
		return json_encode($data);
	}
}