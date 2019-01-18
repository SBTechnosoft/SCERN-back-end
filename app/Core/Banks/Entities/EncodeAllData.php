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
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$bankId[$decodedData] = $decodedJson[$decodedData]['bank_id'];
			$bankName[$decodedData] = $decodedJson[$decodedData]['bank_name'];
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'bankId' => $bankId[$jsonData],
				'bankName' =>$bankName[$jsonData]
			);	
		}
		return json_encode($data);
	}
}