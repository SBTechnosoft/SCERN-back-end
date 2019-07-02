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
		$decodedJson = json_decode($status,true);
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$data[$decodedData]= array(
				'ledgerGroupId' => $decodedJson[$decodedData]['ledger_group_id'],
				'ledgerGroupName' =>$decodedJson[$decodedData]['ledger_group_name'],
				'alias' => $decodedJson[$decodedData]['alias'],
				'underWhat' => $decodedJson[$decodedData]['under_what'],
				'natureOfGroup' =>$decodedJson[$decodedData]['nature_of_group'],
				'affectedGroupProfit' =>$decodedJson[$decodedData]['affected_group_profit']
			);
		}
		return json_encode($data);
	}
}