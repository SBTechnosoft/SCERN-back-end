<?php
namespace ERP\Core\Accounting\Journals\Entities;

use ERP\Core\Accounting\Journals\Entities\Journal;
use ERP\Core\Accounting\Ledgers\Services\LedgerService;
use ERP\Core\Entities\CompanyDetail;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends LedgerService 
{
	public function getEncodedData()
	{
		$decodedJson = func_get_arg(0);
		// print_r($decodedJson);
		$createdAt = $decodedJson->created_at;
		$updatedAt= $decodedJson->updated_at;
		$journalId= $decodedJson->journal_id;
		$jfId= $decodedJson->jf_id;
		$amount= $decodedJson->amount;
		$amountType= $decodedJson->amount_type;
		$entryDate= $decodedJson->entry_date;
		$ledgerId= $decodedJson->ledger_id;
		$companyId= $decodedJson->company_id;
		
		//get the company details from database
		$companyDetail = new CompanyDetail();
		$companyDetails = $companyDetail->getCompanyDetails($companyId);
		
		//get the ledger details from database
		$encodeData = new EncodeData();
		$ledgerDetails = $encodeData->getLedgerData($ledgerId);
		$encodedLedgerData = json_decode($ledgerDetails);
		
		//date format conversion
		$journal = new Journal();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$journal->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $journal->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$journal->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $journal->getUpdated_at();
		}
		if(strcmp($entryDate,'0000-00-00 00:00:00')==0)
		{
			$getEntryDate = "00-00-0000";
		}
		else
		{
			$convertedEntryDate= Carbon\Carbon::createFromFormat('Y-m-d', $entryDate)->format('d-m-Y');
			$journal->setEntryDate($convertedEntryDate);
			$getEntryDate = $journal->getEntryDate();
		}
		//convert amount(number_format) into their company's selected decimal points
		$amount = number_format($amount,$companyDetails['noOfDecimalPoints'],'.','');
		
		//set all data into json array
		$data = array();
		$data['journalId'] = $journalId;
		$data['jfId'] = $jfId;
		$data['amount'] = $amount;
		$data['amountType'] = $amountType;
		$data['entryDate'] = $getEntryDate;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['ledger']= array(
			'ledgerId' => $ledgerId,
			'ledgerName' => $encodedLedgerData->ledgerName,
			'alias' => $encodedLedgerData->alias,
			'inventoryAffected' => $encodedLedgerData->inventoryAffected,
			'address1' => $encodedLedgerData->address1,
			'address2' => $encodedLedgerData->address2,
			'contactNo' => $encodedLedgerData->contactNo,
			'emailId' => $encodedLedgerData->emailId,
			'pan' => $encodedLedgerData->pan,
			'tin' => $encodedLedgerData->tin,
			'cgst' => $encodedLedgerData->cgst,
			'sgst' => $encodedLedgerData->sgst,
			'createdAt' => $encodedLedgerData->createdAt,
			'updatedAt' => $encodedLedgerData->updatedAt,
			'ledgerGroupId' => $encodedLedgerData->ledgerGroup->ledgerGroupId,
			'stateAbb' => $encodedLedgerData->state->stateAbb,
			'cityId' => $encodedLedgerData->city->cityId,
			'companyId' => $encodedLedgerData->company->companyId
		);
		$data['company']= array(
			'companyId' => $companyId,
			'companyName' => $companyDetails['companyName'],	
			'companyDisplayName' => $companyDetails['companyDisplayName'],	
			'address1' => $companyDetails['address1'],	
			'address2' => $companyDetails['address2'],	
			'pincode' => $companyDetails['pincode'],
			'pan' => $companyDetails['pan'],	
			'tin' => $companyDetails['tin'],
			'vatNo' =>$companyDetails['vatNo'],
			'serviceTaxNo' => $companyDetails['serviceTaxNo'],
			'basicCurrencySymbol' => $companyDetails['basicCurrencySymbol'],
			'noOfDecimalPoints' => $companyDetails['noOfDecimalPoints'],
			'formalName' => $companyDetails['formalName'],
			'currencySymbol' => $companyDetails['currencySymbol'],	
			'logo'=> array(
				'documentName' => $companyDetails['logo']['documentName'],	
				'documentUrl' => $companyDetails['logo']['documentUrl'],	
				'documentSize' => $companyDetails['logo']['documentSize'],
				'documentFormat' => $companyDetails['logo']['documentFormat']
			),
			'isDisplay' => $companyDetails['isDisplay'],	
			'isDefault' => $companyDetails['isDefault'],	
			'createdAt' => $companyDetails['createdAt'],	
			'updatedAt' => $companyDetails['updatedAt'],	
			'stateAbb' => $companyDetails['state']['stateAbb'],	
			'cityId' => $companyDetails['city']['cityId']
		);
		$encodeData = json_encode($data);
		return $encodeData;
	}
}