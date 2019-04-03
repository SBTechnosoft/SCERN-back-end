<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Core\Accounting\Ledgers\Services\LedgerService;
use Carbon;
use ERP\Core\Companies\Services\CompanyService;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeTrnAllData extends LedgerService
{
	public function getEncodedAllData($status,$ledgerId)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		
		$companyService = new CompanyService();
		$ledger = new Ledger();
		$encodeDataClass = new EncodeTrnAllData();
		$ledgerArray = array();
		$companyArray = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			if(array_key_exists($ledgerId[0]->ledger_id.'_id',$decodedJson[$decodedData]))
			{
				$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
				$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
				$id[$decodedData] = $decodedJson[$decodedData][$ledgerId[0]->ledger_id.'_id'];
				$amount[$decodedData] = $decodedJson[$decodedData]['amount'];
				$amountType[$decodedData] = $decodedJson[$decodedData]['amount_type'];
				$entryDate[$decodedData] = $decodedJson[$decodedData]['entry_date'];
				$jfId[$decodedData] = $decodedJson[$decodedData]['jf_id'];
				$ledgersId[$decodedData] = $decodedJson[$decodedData]['ledger_id'];
				$openingBalance[$decodedData] = $decodedJson[$decodedData]['openingBalance'];
				$openingBalanceType[$decodedData] = $decodedJson[$decodedData]['openingBalanceType'];
				$currentBalance[$decodedData] = $decodedJson[$decodedData]['currentBalance'];
				$currentBalanceType[$decodedData] = $decodedJson[$decodedData]['currentBalanceType'];
				
				// get the ledger detail from database
				if (!isset($ledgerArray[$ledgersId[$decodedData]])) {
					$ledgerStatus[$decodedData] = $encodeDataClass->getLedgerData($ledgersId[$decodedData]);
					$ledgerArray[$ledgersId[$decodedData]] = json_decode($ledgerStatus[$decodedData],true);
				}
				$ledgerDecodedJson[$decodedData] = $ledgerArray[$ledgersId[$decodedData]];
				
				if (!isset($companyArray[$ledgerDecodedJson[$decodedData]['company']['companyId']])) {
					$companyData[$decodedData] = $companyService->getCompanyData($ledgerDecodedJson[$decodedData]['company']['companyId']);
					$companyArray[$ledgerDecodedJson[$decodedData]['company']['companyId']] = json_decode($companyData[$decodedData]);
				}
				
				$companyDecodedData[$decodedData] = $companyArray[$ledgerDecodedJson[$decodedData]['company']['companyId']];
				
				//convert amount(round) into their company's selected decimal points
				$amount[$decodedData] = round($amount[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints);
				
				// date format conversion
				$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
				$ledger->setCreated_at($convertedCreatedDate[$decodedData]);
				$getCreatedDate[$decodedData] = $ledger->getCreated_at();
				
				if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
				{
					$getUpdatedDate[$decodedData]="00-00-0000";
				}
				else
				{	
					$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
					$ledger->setUpdated_at($convertedUpdatedDate[$decodedData]);
					$getUpdatedDate[$decodedData] = $ledger->getUpdated_at();
				}
				if(strcmp($entryDate[$decodedData],'0000-00-00 00:00:00')==0)
				{
					$getEntryDate[$decodedData]="00-00-0000";
				}
				else
				{
					$convertedEntryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
					$ledger->setEntryDate($convertedEntryDate[$decodedData]);
					$getEntryDate[$decodedData] = $ledger->getEntryDate();
				}
			}
			else
			{
				
				$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
				$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
				$id[$decodedData] = $decodedJson[$decodedData][$ledgerId[1]->ledger_id.'_id'];
				$amount[$decodedData] = $decodedJson[$decodedData]['amount'];
				$amountType[$decodedData] = $decodedJson[$decodedData]['amount_type'];
				$entryDate[$decodedData] = $decodedJson[$decodedData]['entry_date'];
				$jfId[$decodedData] = $decodedJson[$decodedData]['jf_id'];
				$ledgersId[$decodedData] = $decodedJson[$decodedData]['ledger_id'];
				$openingBalance[$decodedData] = $decodedJson[$decodedData]['openingBalance'];
				$openingBalanceType[$decodedData] = $decodedJson[$decodedData]['openingBalanceType'];
				$currentBalance[$decodedData] = $decodedJson[$decodedData]['currentBalance'];
				$currentBalanceType[$decodedData] = $decodedJson[$decodedData]['currentBalanceType'];
				
				// get the ledger detail from database
				if (!isset($ledgerArray[$ledgersId[$decodedData]])) {
					$ledgerStatus[$decodedData] = $encodeDataClass->getLedgerData($ledgersId[$decodedData]);
					$ledgerArray[$ledgersId[$decodedData]] = json_decode($ledgerStatus[$decodedData],true);
				}
				$ledgerDecodedJson[$decodedData] = $ledgerArray[$ledgersId[$decodedData]];
				
				if (!isset($companyArray[$ledgerDecodedJson[$decodedData]['company']['companyId']])) {
					$companyData[$decodedData] = $companyService->getCompanyData($ledgerDecodedJson[$decodedData]['company']['companyId']);
					$companyArray[$ledgerDecodedJson[$decodedData]['company']['companyId']] = json_decode($companyData[$decodedData]);
				}
				$companyDecodedData[$decodedData] = $companyArray[$ledgerDecodedJson[$decodedData]['company']['companyId']];
				//convert amount(number_format) into their company's selected decimal points
				$amount[$decodedData] = number_format($amount[$decodedData],$companyDecodedData[$decodedData]->noOfDecimalPoints,'.','');
				
				// date format conversion
				$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
				$ledger->setCreated_at($convertedCreatedDate[$decodedData]);
				$getCreatedDate[$decodedData] = $ledger->getCreated_at();
				
				if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
				{
					$getUpdatedDate[$decodedData]="00-00-0000";
				}
				else
				{	
					$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
					$ledger->setUpdated_at($convertedUpdatedDate[$decodedData]);
					$getUpdatedDate[$decodedData] = $ledger->getUpdated_at();
				}
				if(strcmp($entryDate[$decodedData],'0000-00-00 00:00:00')==0)
				{
					$getEntryDate[$decodedData]="00-00-0000";
				}
				else
				{
					$convertedEntryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
					$ledger->setEntryDate($convertedEntryDate[$decodedData]);
					$getEntryDate[$decodedData] = $ledger->getEntryDate();
				}
			}
			$data[$decodedData]= array(
				'Id'=>$id[$decodedData],
				'amount' => $amount[$decodedData],
				'amountType' => $amountType[$decodedData],
				'entryDate' => $getEntryDate[$decodedData],
				'jfId' => $jfId[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData],
				'openingBalance' => $openingBalance[$decodedData],
				'openingBalanceType' => $openingBalanceType[$decodedData],
				'currentBalance' => $currentBalance[$decodedData],
				'currentBalanceType' => $currentBalanceType[$decodedData],
				'ledger' => array(
					'ledgerId' => $ledgerDecodedJson[$decodedData]['ledgerId'],
					'ledgerName' => $ledgerDecodedJson[$decodedData]['ledgerName'],
					'alias' => $ledgerDecodedJson[$decodedData]['alias'],
					'inventoryAffected' => $ledgerDecodedJson[$decodedData]['inventoryAffected'],
					'address1' => $ledgerDecodedJson[$decodedData]['address1'],
					'address2' => $ledgerDecodedJson[$decodedData]['address2'],
					'isDealer' => $ledgerDecodedJson[$decodedData]['isDealer'],
					'contactNo' => $ledgerDecodedJson[$decodedData]['contactNo'],
					'emailId' => $ledgerDecodedJson[$decodedData]['emailId'],
					'invoiceNumber' => $ledgerDecodedJson[$decodedData]['invoiceNumber'],
					'outstandingLimit' => $ledgerDecodedJson[$decodedData]['outstandingLimit'],
					'outstandingLimitType' => $ledgerDecodedJson[$decodedData]['outstandingLimitType'],
					'pan' => $ledgerDecodedJson[$decodedData]['pan'],
					'tin' => $ledgerDecodedJson[$decodedData]['tin'],
					'cgst' => $ledgerDecodedJson[$decodedData]['cgst'],
					'sgst' => $ledgerDecodedJson[$decodedData]['sgst'],
					'bankId' => $ledgerDecodedJson[$decodedData]['bankId'],
					'bankDtlId' => $ledgerDecodedJson[$decodedData]['bankDtlId'],
					'micrCode' => $ledgerDecodedJson[$decodedData]['micrCode'],
					'createdAt' => $ledgerDecodedJson[$decodedData]['createdAt'],
					'updatedAt' => $ledgerDecodedJson[$decodedData]['updatedAt'],
					'openingBalance' => $ledgerDecodedJson[$decodedData]['openingBalance'],
					'openingBalanceType' => $ledgerDecodedJson[$decodedData]['openingBalanceType'],
					'currentBalance' => $ledgerDecodedJson[$decodedData]['currentBalance'],
					'currentBalanceType' => $ledgerDecodedJson[$decodedData]['currentBalanceType'],
					'ledgerGroupId' => $ledgerDecodedJson[$decodedData]['ledgerGroup']['ledgerGroupId'],
					'stateAbb' => $ledgerDecodedJson[$decodedData]['state']['stateAbb'],
					'cityId' => $ledgerDecodedJson[$decodedData]['city']['cityId'],
					'companyId' => $ledgerDecodedJson[$decodedData]['company']['companyId']
				)
			);
			
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}