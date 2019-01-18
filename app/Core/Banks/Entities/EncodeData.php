<?php
namespace ERP\Core\Banks\Entities;

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
			
		$bankId = $decodedJson[0]['bank_id'];
		$bankName= $decodedJson[0]['bank_name'];
		
		//set all data into json array
		$data = array();
		$data['bankId'] = $bankId;
		$data['bankName'] = $bankName;
		$encodeData = json_encode($data);
		return $encodeData;
	}
}