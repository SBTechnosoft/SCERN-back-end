<?php
namespace ERP\Core\Accounting\LedgerGroups\Entities;

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
			$ledgerGrpId[$decodedData] = $decodedJson[$decodedData]['ledger_group_id'];
			$ledgerGrpName[$decodedData] = $decodedJson[$decodedData]['ledger_group_name'];
			$alias[$decodedData] = $decodedJson[$decodedData]['alias'];
			$underWhat[$decodedData] = $decodedJson[$decodedData]['under_what'];
			$natureOfGrp[$decodedData] = $decodedJson[$decodedData]['nature_of_group'];
			$affectedGrpProfit[$decodedData] = $decodedJson[$decodedData]['affected_group_profit'];
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'ledgerGroupId' => $ledgerGrpId[$jsonData],
				'ledgerGroupName' =>$ledgerGrpName[$jsonData],
				'alias' => $alias[$jsonData],
				'underWhat' => $underWhat[$jsonData],
				'natureOfGroup' =>$natureOfGrp[$jsonData],
				'affectedGroupProfit' =>$affectedGrpProfit[$jsonData]
			);	
		}
		return json_encode($data);
	}
}