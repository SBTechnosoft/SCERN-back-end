<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
use ERP\Core\Entities\LedgerGroupDetail;
use ERP\Core\Entities\CompanyDetail;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends StateService
{
	public function getEncodedAllData($status)
	{
		
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$ledger = new Ledger();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$ledgerId[$decodedData] = $decodedJson[$decodedData]['ledger_id'];
			$ledgerName[$decodedData] = $decodedJson[$decodedData]['ledger_name'];
			$alias[$decodedData] = $decodedJson[$decodedData]['alias'];
			$inventoryAffected[$decodedData] = $decodedJson[$decodedData]['inventory_affected'];
			$address1[$decodedData] = $decodedJson[$decodedData]['address1'];
			$address2[$decodedData] = $decodedJson[$decodedData]['address2'];
			$isDealer[$decodedData] = $decodedJson[$decodedData]['is_dealer'];
			$contactNo[$decodedData] = $decodedJson[$decodedData]['contact_no'];
			$emailId[$decodedData] = $decodedJson[$decodedData]['email_id'];
			$invoiceNumber[$decodedData] = $decodedJson[$decodedData]['invoice_number'];
			$outstandingLimit[$decodedData] = $decodedJson[$decodedData]['outstanding_limit'];
			$outstandingLimitType[$decodedData] = $decodedJson[$decodedData]['outstanding_limit_type'];
			$panNo[$decodedData] = $decodedJson[$decodedData]['pan'];
			$tinNo[$decodedData] = $decodedJson[$decodedData]['tin'];
			$cgst[$decodedData] = $decodedJson[$decodedData]['cgst'];
			$sgst[$decodedData] = $decodedJson[$decodedData]['sgst'];
			$bankId[$decodedData] = $decodedJson[$decodedData]['bank_id'];
			$bankDtlId[$decodedData] = $decodedJson[$decodedData]['bank_dtl_id'];
			$micrCode[$decodedData] = $decodedJson[$decodedData]['micr_code'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$ledgerGrpId[$decodedData] = $decodedJson[$decodedData]['ledger_group_id'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$openingBalance[$decodedData] = $decodedJson[$decodedData]['openingBalance'];
			$openingBalanceType[$decodedData] = $decodedJson[$decodedData]['openingBalanceType'];
			$currentBalance[$decodedData] = $decodedJson[$decodedData]['currentBalance'];
			$currentBalanceType[$decodedData] = $decodedJson[$decodedData]['currentBalanceType'];
			
			//get the state detail from database
			$encodeDataClass = new EncodeAllData();
			$stateStatus[$decodedData] = $encodeDataClass->getStateData($stateAbb[$decodedData]);
			$stateDecodedJson[$decodedData] = json_decode($stateStatus[$decodedData],true);
			$stateName[$decodedData]= $stateDecodedJson[$decodedData]['stateName'];
			$stateIsDisplay[$decodedData]= $stateDecodedJson[$decodedData]['isDisplay'];
			$stateCreatedAt[$decodedData]= $stateDecodedJson[$decodedData]['createdAt'];
			$stateUpdatedAt[$decodedData]= $stateDecodedJson[$decodedData]['updatedAt'];
			
			//get the city details from database
			$cityDetail = new CityDetail();
			$getCityDetail[$decodedData] = $cityDetail->getCityDetail($cityId[$decodedData]);
			
			//get the ledger-group details from database
			$ledgerGrpDetail = new LedgerGroupDetail();
			$getLedgerGrpDetails[$decodedData] = $ledgerGrpDetail->getLedgerGrpDetails($ledgerGrpId[$decodedData]);
			
			//get the company details from database
			$companyDetail = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			
			//convert amount(number_format) into their company's selected decimal points
			$openingBalance[$decodedData] = number_format($openingBalance[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$currentBalance[$decodedData] = number_format($currentBalance[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$outstandingLimit[$decodedData] = number_format($outstandingLimit[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
				
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$ledger->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $ledger->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$ledger->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $ledger->getUpdated_at();
			}
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'ledgerId'=>$ledgerId[$jsonData],
				'ledgerName' => $ledgerName[$jsonData],
				'alias' => $alias[$jsonData],
				'inventoryAffected' => $inventoryAffected[$jsonData],
				'address1' => $address1[$jsonData],
				'address2' => $address2[$jsonData],
				'isDealer' => $isDealer[$jsonData],
				'contactNo' => $contactNo[$jsonData],
				'emailId' => $emailId[$jsonData],
				'invoiceNumber' => $invoiceNumber[$jsonData],
				'outstandingLimit' => $outstandingLimit[$jsonData],
				'outstandingLimitType' => $outstandingLimitType[$jsonData],
				'pan'=> $panNo[$jsonData],
				'tin'=> $tinNo[$jsonData],
				'cgst'=> $cgst[$jsonData],
				'sgst'=> $sgst[$jsonData],
				'bankId'=> $bankId[$jsonData],
				'bankDtlId'=> $bankDtlId[$jsonData],
				'micrCode'=> $micrCode[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				'openingBalance' => $openingBalance[$jsonData],
				'openingBalanceType' => $openingBalanceType[$jsonData],
				'currentBalance' => $currentBalance[$jsonData],
				'currentBalanceType' => $currentBalanceType[$jsonData],
				
				'state' => array(
					'stateAbb' => $stateAbb[$jsonData],
					'stateName' => $stateName[$jsonData],
					'isDisplay' => $stateIsDisplay[$jsonData],
					'createdAt' => $stateCreatedAt[$jsonData],
					'updatedAt' => $stateUpdatedAt[$jsonData]
				),
				
				'city'=> array(
					'cityId' => $cityId[$jsonData],
					'cityName' => $getCityDetail[$jsonData]['cityName'],
					'isDisplay' => $getCityDetail[$jsonData]['isDisplay'],
					'createdAt' => $getCityDetail[$jsonData]['createdAt'],
					'updatedAt' => $getCityDetail[$jsonData]['updatedAt'],
					'stateAbb' => $getCityDetail[$jsonData]['state']['stateAbb']
				),
				
				'ledgerGroup'=> array(
					'ledgerGroupId' => $getLedgerGrpDetails[$jsonData]['ledgerGroupId'],	
					'ledgerGroupName' => $getLedgerGrpDetails[$jsonData]['ledgerGroupName'],	
					'underWhat' => $getLedgerGrpDetails[$jsonData]['underWhat'],
					'alias' => $getLedgerGrpDetails[$jsonData]['alias'],
					'natureOfGroup' => $getLedgerGrpDetails[$jsonData]['natureOfGroup'],
					'affectedGroupProfit' => $getLedgerGrpDetails[$jsonData]['affectedGroupProfit']
				),
				
				'company' => array(	
					'companyId' => $getCompanyDetails[$jsonData]['companyId'],
					'companyName' => $getCompanyDetails[$jsonData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$jsonData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$jsonData]['address1'],	
					'address2'=> $getCompanyDetails[$jsonData]['address2'],	
					'emailId'=> $getCompanyDetails[$jsonData]['emailId'],	
					'customerCare'=> $getCompanyDetails[$jsonData]['customerCare'],	
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