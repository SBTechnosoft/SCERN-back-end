<?php
namespace ERP\Core\Accounting\TrialBalance\Entities;

use ERP\Core\Accounting\Ledgers\Services\LedgerService;
use ERP\Core\Companies\Services\CompanyService;
use ERP\Core\Accounting\TrialBalance\Entities\TrialBalance;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeTrialBalanceData extends LedgerService
{
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedLedgerData = array();
		$decodedJson = json_decode($status,true);
		$companyService = new CompanyService();
		$trialBalance = new TrialBalance();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$ledgerId[$decodedData] = $decodedJson[$decodedData]['ledger_id'];
			$amount[$decodedData] = $decodedJson[$decodedData]['amount'];
			$amountType[$decodedData] = $decodedJson[$decodedData]['amount_type'];
			$trialBalanceId[$decodedData] = $decodedJson[$decodedData]['trial_balance_id'];
			
			$trialBalanceData = new EncodeTrialBalanceData();
			$ledgerData[$decodedData]  = $trialBalanceData->getLedgerData($ledgerId[$decodedData]);
			$decodedLedgerData[$decodedData] = json_decode($ledgerData[$decodedData]);
			
			// convert amount(round) into their company's selected decimal points
			$companyData[$decodedData] = $companyService->getCompanyData($decodedLedgerData[$decodedData]->company->companyId);
			$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);
				
			$amount[$decodedData] = number_format($amount[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints,'.','');
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$trialBalance->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $trialBalance->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$trialBalance->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $trialBalance->getUpdated_at();
			}
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'trialBalanceId'=>$trialBalanceId[$jsonData],
				'amount'=>$amount[$jsonData],
				'amountType' => $amountType[$jsonData],
				'createdAt'=>$getCreatedDate[$jsonData],
				'updatedAt'=>$getUpdatedDate[$jsonData],
				'ledger' => array(	
					'ledgerId' => $decodedLedgerData[$jsonData]->ledgerId,
					'ledgerName' => $decodedLedgerData[$jsonData]->ledgerName,
					'alias' => $decodedLedgerData[$jsonData]->alias,
					'inventoryAffected' => $decodedLedgerData[$jsonData]->inventoryAffected,
					'address1' => $decodedLedgerData[$jsonData]->address1,
					'address2' => $decodedLedgerData[$jsonData]->address2,
					'contactNo' => $decodedLedgerData[$jsonData]->contactNo,
					'emailId' => $decodedLedgerData[$jsonData]->emailId,
					'pan' => $decodedLedgerData[$jsonData]->pan,
					'tin' => $decodedLedgerData[$jsonData]->tin,
					'cgst' => $decodedLedgerData[$jsonData]->cgst,
					'sgst' => $decodedLedgerData[$jsonData]->sgst,
					'createdAt' => $decodedLedgerData[$jsonData]->createdAt,
					'updatedAt' => $decodedLedgerData[$jsonData]->updatedAt,
					'openingBalance' => $decodedLedgerData[$jsonData]->openingBalance,
					'openingBalanceType' => $decodedLedgerData[$jsonData]->openingBalanceType,
					'currentBalance' => $decodedLedgerData[$jsonData]->currentBalance,
					'currentBalanceType' => $decodedLedgerData[$jsonData]->currentBalanceType,
					'ledgerGroupId' => $decodedLedgerData[$jsonData]->ledgerGroup->ledgerGroupId,
					'stateAbb' => $decodedLedgerData[$jsonData]->state->stateAbb,
					'cityId' => $decodedLedgerData[$jsonData]->city->cityId,
					'companyId' => $decodedLedgerData[$jsonData]->company->companyId
				)		
			);	
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}