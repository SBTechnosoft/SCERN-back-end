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
		$encodeBranchData = new EncodeAllBranchData();
		$bankBranchArray = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$bankId[$decodedData] = $decodedJson[$decodedData]['bank_id'];
			$branchName[$decodedData] = $decodedJson[$decodedData]['branch_name'];
			$bankDtlId[$decodedData] = $decodedJson[$decodedData]['bank_dtl_id'];
			$ifscCode[$decodedData] = $decodedJson[$decodedData]['ifsc_code'];
			$isDefault[$decodedData] = $decodedJson[$decodedData]['is_default'];
			
			if (!isset($bankBranchArray[$bankId[$decodedData]])) {
				$bankData[$decodedData] = $encodeBranchData->getBankData($bankId[$decodedData]);
				$bankBranchArray[$bankId[$decodedData]] = json_decode($bankData[$decodedData],true);
			}
			$bankDecodedJson[$decodedData] = $bankBranchArray[$bankId[$decodedData]];
			$data[$decodedData]= array(
				'bankDtlId' => $bankDtlId[$decodedData],
				'branchName' =>$branchName[$decodedData],
				'ifscCode' =>$ifscCode[$decodedData],
				'isDefault' =>$isDefault[$decodedData],
				'bank' => $bankDecodedJson[$decodedData]
			);
		}
		return json_encode($data);
	}
}