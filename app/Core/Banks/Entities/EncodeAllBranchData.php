<?php
namespace ERP\Core\Banks\Entities;

use Carbon;
use ERP\Core\Banks\Services\BankService;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllBranchData extends BankService
{
	//date conversion and merge with json data and returns json array
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$encodeBranchData = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$bankId[$decodedData] = $decodedJson[$decodedData]['bank_id'];
			$branchName[$decodedData] = $decodedJson[$decodedData]['branch_name'];
			$bankDtlId[$decodedData] = $decodedJson[$decodedData]['bank_dtl_id'];
			$ifscCode[$decodedData] = $decodedJson[$decodedData]['ifsc_code'];
			$isDefault[$decodedData] = $decodedJson[$decodedData]['is_default'];
			
			$encodeBranchData[$decodedData] = new EncodeAllBranchData();
			$bankData[$decodedData] = $encodeBranchData[$decodedData]->getBankData($bankId[$decodedData]);
			$bankDecodedJson[$decodedData] = json_decode($bankData[$decodedData],true);
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'bankDtlId' => $bankDtlId[$jsonData],
				'branchName' =>$branchName[$jsonData],
				'ifscCode' =>$ifscCode[$jsonData],
				'isDefault' =>$isDefault[$jsonData],
				'bank' => $bankDecodedJson[$jsonData]
			);	
		}
		return json_encode($data);
	}
}