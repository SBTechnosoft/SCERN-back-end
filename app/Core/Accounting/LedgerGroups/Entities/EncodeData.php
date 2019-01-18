<?php
namespace ERP\Core\Accounting\LedgerGroups\Entities;

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
			
		$ledgerGrpId = $decodedJson[0]['ledger_group_id'];
		$ledgerGrpName= $decodedJson[0]['ledger_group_name'];
		$alias= $decodedJson[0]['alias'];
		$underWhat= $decodedJson[0]['under_what'];
		$natureOfGrp= $decodedJson[0]['nature_of_group'];
		$affectedGrpProfit= $decodedJson[0]['affected_group_profit'];
		
		//set all data into json array
		$data = array();
		$data['ledgerGroupId'] = $ledgerGrpId;
		$data['ledgerGroupName'] = $ledgerGrpName;
		$data['alias'] = $alias;
		$data['underWhat'] = $underWhat;
		$data['natureOfGroup'] = $natureOfGrp;	
		$data['affectedGroupProfit'] = $affectedGrpProfit;	
		
		$encodeData = json_encode($data);
		return $encodeData;
	}
}