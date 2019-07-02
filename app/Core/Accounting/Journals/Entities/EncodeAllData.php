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
class EncodeAllData extends LedgerService
{
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		
		$journal = new Journal();
		$companyDetail = new CompanyDetail();
		$encodeDataClass = new EncodeAllData();
		$ledgerArray = array();
		$companyArray = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$journalId[$decodedData] = $decodedJson[$decodedData]['journal_id'];
			$jfId[$decodedData] = $decodedJson[$decodedData]['jf_id'];
			$amount[$decodedData] = $decodedJson[$decodedData]['amount'];
			$amountType[$decodedData] = $decodedJson[$decodedData]['amount_type'];
			$entryDate[$decodedData] = $decodedJson[$decodedData]['entry_date'];
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$ledgerId[$decodedData] = $decodedJson[$decodedData]['ledger_id'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			
			//get the Ledger detail from database
			if (!isset($ledgerArray[$ledgerId[$decodedData]])) {
				$ledgerArray[$ledgerId[$decodedData]] = $encodeDataClass->getLedgerData($ledgerId[$decodedData]);
			}
			$ledgerStatus[$decodedData] = $ledgerArray[$ledgerId[$decodedData]];

			$ledgerDecodedJson[$decodedData] = json_decode($ledgerStatus[$decodedData],true);
			$ledgerId[$decodedData]= $ledgerDecodedJson[$decodedData]['ledgerId'];
			$ledgerName[$decodedData]= $ledgerDecodedJson[$decodedData]['ledgerName'];
			$alias[$decodedData]= $ledgerDecodedJson[$decodedData]['alias'];
			$inventoryAffected[$decodedData]= $ledgerDecodedJson[$decodedData]['inventoryAffected'];
			$address1[$decodedData]= $ledgerDecodedJson[$decodedData]['address1'];
			$address2[$decodedData]= $ledgerDecodedJson[$decodedData]['address2'];
			$contactNo[$decodedData]= $ledgerDecodedJson[$decodedData]['contactNo'];
			$emailId[$decodedData]= $ledgerDecodedJson[$decodedData]['emailId'];
			$panNo[$decodedData]= $ledgerDecodedJson[$decodedData]['pan'];
			$tinNo[$decodedData]= $ledgerDecodedJson[$decodedData]['tin'];
			$cgst[$decodedData]= $ledgerDecodedJson[$decodedData]['cgst'];
			$sgst[$decodedData]= $ledgerDecodedJson[$decodedData]['sgst'];
			$ledgerCreatedAt[$decodedData]= $ledgerDecodedJson[$decodedData]['createdAt'];
			$ledgerUpdatedAt[$decodedData]= $ledgerDecodedJson[$decodedData]['updatedAt'];
			$ledgerGroupId[$decodedData]= $ledgerDecodedJson[$decodedData]['ledgerGroup']['ledgerGroupId'];
			$stateAbb[$decodedData]= $ledgerDecodedJson[$decodedData]['state']['stateAbb'];
			$cityId[$decodedData]= $ledgerDecodedJson[$decodedData]['city']['cityId'];
			$ledgerCompanyId[$decodedData]= $ledgerDecodedJson[$decodedData]['company']['companyId'];
			
			//get the company details from database
			if (!isset($companyArray[$companyId[$decodedData]])) {
				$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			}
			
			//convert amount(number_format) into their company's selected decimal points
			$amount[$decodedData] = number_format($amount[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$journal->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $journal->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$journal->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $journal->getUpdated_at();
			}
			if(strcmp($entryDate[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getEntryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedEntryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
				$journal->setEntryDate($convertedEntryDate[$decodedData]);
				$getEntryDate[$decodedData] = $journal->getEntryDate();
			}
			$data[$decodedData]= array(
				'journalId'=>$journalId[$decodedData],
				'jfId'=>$jfId[$decodedData],
				'amount'=>$amount[$decodedData],
				'amountType'=>$amountType[$decodedData],
				'entryDate'=>$getEntryDate[$decodedData],
				'createdAt'=>$getCreatedDate[$decodedData],
				'updatedAt'=>$getUpdatedDate[$decodedData],
				
				'ledger' => array(	
					'ledgerId'=>$ledgerId[$decodedData],
					'ledgerName' => $ledgerName[$decodedData],
					'alias' => $alias[$decodedData],
					'inventoryAffected' => $inventoryAffected[$decodedData],
					'address1' => $address1[$decodedData],
					'address2' => $address2[$decodedData],
					'contactNo' => $contactNo[$decodedData],
					'emailId' => $emailId[$decodedData],
					'pan'=> $panNo[$decodedData],
					'tin'=> $tinNo[$decodedData],
					'cgst'=> $cgst[$decodedData],
					'sgst'=> $sgst[$decodedData],
					'createdAt' => $ledgerCreatedAt[$decodedData],
					'updatedAt' => $ledgerUpdatedAt[$decodedData],
					'ledgerGroupId' => $ledgerGroupId[$decodedData],
					'stateAbb' => $stateAbb[$decodedData],
					'cityId' => $cityId[$decodedData],
					'companyId' => $ledgerCompanyId[$decodedData]
				),
				'company' => array(	
					'companyId' => $getCompanyDetails[$decodedData]['companyId'],
					'companyName' => $getCompanyDetails[$decodedData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$decodedData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$decodedData]['address1'],	
					'address2'=> $getCompanyDetails[$decodedData]['address2'],	
					'pincode' => $getCompanyDetails[$decodedData]['pincode'],	
					'pan' => $getCompanyDetails[$decodedData]['pan'],	
					'tin'=> $getCompanyDetails[$decodedData]['tin'],	
					'vatNo' => $getCompanyDetails[$decodedData]['vatNo'],	
					'serviceTaxNo' => $getCompanyDetails[$decodedData]['serviceTaxNo'],	
					'basicCurrencySymbol' => $getCompanyDetails[$decodedData]['basicCurrencySymbol'],	
					'formalName' => $getCompanyDetails[$decodedData]['formalName'],	
					'noOfDecimalPoints' => $getCompanyDetails[$decodedData]['noOfDecimalPoints'],	
					'currencySymbol' => $getCompanyDetails[$decodedData]['currencySymbol'],	
					'logo'=> array(
						'documentName' => $getCompanyDetails[$decodedData]['logo']['documentName'],	
						'documentUrl' => $getCompanyDetails[$decodedData]['logo']['documentUrl'],	
						'documentSize' =>$getCompanyDetails[$decodedData]['logo']['documentSize'],	
						'documentFormat' => $getCompanyDetails[$decodedData]['logo']['documentFormat']
					),
					'isDisplay' => $getCompanyDetails[$decodedData]['isDisplay'],	
					'isDefault' => $getCompanyDetails[$decodedData]['isDefault'],	
					'createdAt' => $getCompanyDetails[$decodedData]['createdAt'],	
					'updatedAt' => $getCompanyDetails[$decodedData]['updatedAt'],	
					'stateAbb' => $getCompanyDetails[$decodedData]['state']['stateAbb'],	
					'cityId' => $getCompanyDetails[$decodedData]['city']['cityId']	
				)		
			);
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}