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
			$encodeDataClass = new EncodeAllData();
			$ledgerStatus[$decodedData] = $encodeDataClass->getLedgerData($ledgerId[$decodedData]);
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
			$companyDetail = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			
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
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'journalId'=>$journalId[$jsonData],
				'jfId'=>$jfId[$jsonData],
				'amount'=>$amount[$jsonData],
				'amountType'=>$amountType[$jsonData],
				'entryDate'=>$getEntryDate[$jsonData],
				'createdAt'=>$getCreatedDate[$jsonData],
				'updatedAt'=>$getUpdatedDate[$jsonData],
				
				'ledger' => array(	
					'ledgerId'=>$ledgerId[$jsonData],
					'ledgerName' => $ledgerName[$jsonData],
					'alias' => $alias[$jsonData],
					'inventoryAffected' => $inventoryAffected[$jsonData],
					'address1' => $address1[$jsonData],
					'address2' => $address2[$jsonData],
					'contactNo' => $contactNo[$jsonData],
					'emailId' => $emailId[$jsonData],
					'pan'=> $panNo[$jsonData],
					'tin'=> $tinNo[$jsonData],
					'cgst'=> $cgst[$jsonData],
					'sgst'=> $sgst[$jsonData],
					'createdAt' => $ledgerCreatedAt[$jsonData],
					'updatedAt' => $ledgerUpdatedAt[$jsonData],
					'ledgerGroupId' => $ledgerGroupId[$jsonData],
					'stateAbb' => $stateAbb[$jsonData],
					'cityId' => $cityId[$jsonData],
					'companyId' => $ledgerCompanyId[$jsonData]
				),
				'company' => array(	
					'companyId' => $getCompanyDetails[$jsonData]['companyId'],
					'companyName' => $getCompanyDetails[$jsonData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$jsonData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$jsonData]['address1'],	
					'address2'=> $getCompanyDetails[$jsonData]['address2'],	
					'pincode' => $getCompanyDetails[$jsonData]['pincode'],	
					'pan' => $getCompanyDetails[$jsonData]['pan'],	
					'tin'=> $getCompanyDetails[$jsonData]['tin'],	
					'vatNo' => $getCompanyDetails[$jsonData]['vatNo'],	
					'serviceTaxNo' => $getCompanyDetails[$jsonData]['serviceTaxNo'],	
					'basicCurrencySymbol' => $getCompanyDetails[$jsonData]['basicCurrencySymbol'],	
					'formalName' => $getCompanyDetails[$jsonData]['formalName'],	
					'noOfDecimalPoints' => $getCompanyDetails[$jsonData]['noOfDecimalPoints'],	
					'currencySymbol' => $getCompanyDetails[$jsonData]['currencySymbol'],	
					'logo'=> array(
						'documentName' => $getCompanyDetails[$jsonData]['logo']['documentName'],	
						'documentUrl' => $getCompanyDetails[$jsonData]['logo']['documentUrl'],	
						'documentSize' =>$getCompanyDetails[$jsonData]['logo']['documentSize'],	
						'documentFormat' => $getCompanyDetails[$jsonData]['logo']['documentFormat']
					),
					'isDisplay' => $getCompanyDetails[$jsonData]['isDisplay'],	
					'isDefault' => $getCompanyDetails[$jsonData]['isDefault'],	
					'createdAt' => $getCompanyDetails[$jsonData]['createdAt'],	
					'updatedAt' => $getCompanyDetails[$jsonData]['updatedAt'],	
					'stateAbb' => $getCompanyDetails[$jsonData]['state']['stateAbb'],	
					'cityId' => $getCompanyDetails[$jsonData]['city']['cityId']	
				)		
			);
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}