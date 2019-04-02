<?php
namespace ERP\Core\Banks\Entities;

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
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$data[$decodedData]= array(
				'bankId' => $decodedJson[$decodedData]['bank_id'],
				'bankName' =>$decodedJson[$decodedData]['bank_name']
			);
		}
		return json_encode($data);
	}
}