<?php
namespace ERP\Core\Settings\Professions\Entities;

use ERP\Core\Settings\Professions\Entities\Profession;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends Profession
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt= $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$professionId= $decodedJson[0]['profession_id'];
		$professionName= $decodedJson[0]['profession_name'];
		$description= $decodedJson[0]['description'];
		$professionParentId= $decodedJson[0]['profession_parent_id'];
		
		//date format conversion
		$profession = new EncodeData();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$profession->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $profession->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$profession->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $profession->getUpdated_at();
		}
		if($professionParentId==0)
		{
			$professionParentId='';
		}
		//set all data into json array
		$data = array();
		$data['professionId'] = $professionId;
		$data['professionName'] = $professionName;
		$data['description'] = $description;
		$data['professionParentId'] = $professionParentId;
		$data['createdAt'] = $getCreatedDate;	
		$data['updatedAt'] = $getUpdatedDate;	
		$encodeData = json_encode($data);
		return $encodeData;
	}
}