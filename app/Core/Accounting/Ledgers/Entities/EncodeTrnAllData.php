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
				$encodeDataClass = new EncodeTrnAllData();
				$ledgerStatus[$decodedData] = $encodeDataClass->getLedgerData($ledgersId[$decodedData]);
				$ledgerDecodedJson[$decodedData] = json_decode($ledgerStatus[$decodedData],true);
				
				$companyData[$decodedData] = $companyService->getCompanyData($ledgerDecodedJson[$decodedData]['company']['companyId']);
				$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);
				
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
				$encodeDataClass = new EncodeTrnAllData();
				$ledgerStatus[$decodedData] = $encodeDataClass->getLedgerData($ledgersId[$decodedData]);
				$ledgerDecodedJson[$decodedData] = json_decode($ledgerStatus[$decodedData],true);
				
				$companyData[$decodedData] = $companyService->getCompanyData($ledgerDecodedJson[$decodedData]['company']['companyId']);
				$companyDecodedData[$decodedData] = json_decode($companyData[$decodedData]);
				
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
			
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'Id'=>$id[$jsonData],
				'amount' => $amount[$jsonData],
				'amountType' => $amountType[$jsonData],
				'entryDate' => $getEntryDate[$jsonData],
				'jfId' => $jfId[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				'openingBalance' => $openingBalance[$jsonData],
				'openingBalanceType' => $openingBalanceType[$jsonData],
				'currentBalance' => $currentBalance[$jsonData],
				'currentBalanceType' => $currentBalanceType[$jsonData],
				'ledger' => array(
					'ledgerId' => $ledgerDecodedJson[$jsonData]['ledgerId'],
					'ledgerName' => $ledgerDecodedJson[$jsonData]['ledgerName'],
					'alias' => $ledgerDecodedJson[$jsonData]['alias'],
					'inventoryAffected' => $ledgerDecodedJson[$jsonData]['inventoryAffected'],
					'address1' => $ledgerDecodedJson[$jsonData]['address1'],
					'address2' => $ledgerDecodedJson[$jsonData]['address2'],
					'isDealer' => $ledgerDecodedJson[$jsonData]['isDealer'],
					'contactNo' => $ledgerDecodedJson[$jsonData]['contactNo'],
					'emailId' => $ledgerDecodedJson[$jsonData]['emailId'],
					'invoiceNumber' => $ledgerDecodedJson[$jsonData]['invoiceNumber'],
					'outstandingLimit' => $ledgerDecodedJson[$jsonData]['outstandingLimit'],
					'outstandingLimitType' => $ledgerDecodedJson[$jsonData]['outstandingLimitType'],
					'pan' => $ledgerDecodedJson[$jsonData]['pan'],
					'tin' => $ledgerDecodedJson[$jsonData]['tin'],
					'cgst' => $ledgerDecodedJson[$jsonData]['cgst'],
					'sgst' => $ledgerDecodedJson[$jsonData]['sgst'],
					'bankId' => $ledgerDecodedJson[$jsonData]['bankId'],
					'bankDtlId' => $ledgerDecodedJson[$jsonData]['bankDtlId'],
					'micrCode' => $ledgerDecodedJson[$jsonData]['micrCode'],
					'createdAt' => $ledgerDecodedJson[$jsonData]['createdAt'],
					'updatedAt' => $ledgerDecodedJson[$jsonData]['updatedAt'],
					'openingBalance' => $ledgerDecodedJson[$jsonData]['openingBalance'],
					'openingBalanceType' => $ledgerDecodedJson[$jsonData]['openingBalanceType'],
					'currentBalance' => $ledgerDecodedJson[$jsonData]['currentBalance'],
					'currentBalanceType' => $ledgerDecodedJson[$jsonData]['currentBalanceType'],
					'ledgerGroupId' => $ledgerDecodedJson[$jsonData]['ledgerGroup']['ledgerGroupId'],
					'stateAbb' => $ledgerDecodedJson[$jsonData]['state']['stateAbb'],
					'cityId' => $ledgerDecodedJson[$jsonData]['city']['cityId'],
					'companyId' => $ledgerDecodedJson[$jsonData]['company']['companyId']
				)
			);
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}