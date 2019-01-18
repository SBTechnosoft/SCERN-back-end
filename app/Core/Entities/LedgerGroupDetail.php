<?php
namespace ERP\Core\Entities;

use ERP\Core\Accounting\LedgerGroups\Services\LedgerGroupService;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerGroupDetail extends LedgerGroupService 
{
	public function getLedgerGrpDetails($ledgerGrpId)
	{
		//get the ledger grp data from database
		$encodeLedgerGrpDataClass = new LedgerGroupDetail();
		$ledgerGrpStatus = $encodeLedgerGrpDataClass->getLedgerGrpData($ledgerGrpId);
		$ledgerGrpDecodedJson = json_decode($ledgerGrpStatus,true);
		return $ledgerGrpDecodedJson;
	}
    
}