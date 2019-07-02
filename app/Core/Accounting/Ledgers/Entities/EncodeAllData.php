<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
use ERP\Core\Entities\LedgerGroupDetail;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Accounting\Ledgers\Entities\LedgerArray;
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
		$encodeDataClass = new EncodeAllData();
		$cityDetail = new CityDetail();
		$ledgerGrpDetail = new LedgerGroupDetail();
		$companyDetail = new CompanyDetail();
		$data = array();

		$stateArray = array();
		$cityArray = array();
		$ledgerGrpArray = array();
		$companyDetailsArray = array();

		$ledgerArray = new LedgerArray();
		$defaultLedgers = $ledgerArray->ledgerArrays();

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
			if (!isset($stateArray[$stateAbb[$decodedData]])) {
				$stateArray[$stateAbb[$decodedData]] = $encodeDataClass->getStateData($stateAbb[$decodedData]);
			}
			$stateStatus[$decodedData] = $stateArray[$stateAbb[$decodedData]];
			$stateDecodedJson[$decodedData] = json_decode($stateStatus[$decodedData],true);
			$stateName[$decodedData]= $stateDecodedJson[$decodedData]['stateName'];
			$stateIsDisplay[$decodedData]= $stateDecodedJson[$decodedData]['isDisplay'];
			$stateCreatedAt[$decodedData]= $stateDecodedJson[$decodedData]['createdAt'];
			$stateUpdatedAt[$decodedData]= $stateDecodedJson[$decodedData]['updatedAt'];
			
			//get the city details from database
			if (!isset($cityArray[$cityId[$decodedData]])) {
				$cityArray[$cityId[$decodedData]] = $cityDetail->getCityDetail($cityId[$decodedData]);
			}
			$getCityDetail[$decodedData] = $cityArray[$cityId[$decodedData]];
			
			//get the ledger-group details from database
			if (!isset($ledgerGrpArray[$ledgerGrpId[$decodedData]])) {
				$ledgerGrpArray[$ledgerGrpId[$decodedData]] = $ledgerGrpDetail->getLedgerGrpDetails($ledgerGrpId[$decodedData]);
			}
			$getLedgerGrpDetails[$decodedData] = $ledgerGrpArray[$ledgerGrpId[$decodedData]];
			
			//get the company details from database
			if (!isset($companyDetailsArray[$companyId[$decodedData]])) {
				$companyDetailsArray[$companyId[$decodedData]] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			}
			$getCompanyDetails[$decodedData] = $companyDetailsArray[$companyId[$decodedData]];
			
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
			$data[$decodedData]= array(
				'ledgerId'=>$ledgerId[$decodedData],
				'ledgerName' => $ledgerName[$decodedData],
				'alias' => $alias[$decodedData],
				'inventoryAffected' => $inventoryAffected[$decodedData],
				'address1' => $address1[$decodedData],
				'address2' => $address2[$decodedData],
				'isDealer' => $isDealer[$decodedData],
				'contactNo' => $contactNo[$decodedData],
				'emailId' => $emailId[$decodedData],
				'invoiceNumber' => $invoiceNumber[$decodedData],
				'outstandingLimit' => $outstandingLimit[$decodedData],
				'outstandingLimitType' => $outstandingLimitType[$decodedData],
				'pan'=> $panNo[$decodedData],
				'tin'=> $tinNo[$decodedData],
				'cgst'=> $cgst[$decodedData],
				'sgst'=> $sgst[$decodedData],
				'bankId'=> $bankId[$decodedData],
				'bankDtlId'=> $bankDtlId[$decodedData],
				'micrCode'=> $micrCode[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData],
				'openingBalance' => $openingBalance[$decodedData],
				'openingBalanceType' => $openingBalanceType[$decodedData],
				'currentBalance' => $currentBalance[$decodedData],
				'currentBalanceType' => $currentBalanceType[$decodedData],
				
				'state' => array(
					'stateAbb' => $stateAbb[$decodedData],
					'stateName' => $stateName[$decodedData],
					'isDisplay' => $stateIsDisplay[$decodedData],
					'createdAt' => $stateCreatedAt[$decodedData],
					'updatedAt' => $stateUpdatedAt[$decodedData]
				),
				
				'city'=> array(
					'cityId' => $cityId[$decodedData],
					'cityName' => $getCityDetail[$decodedData]['cityName'],
					'isDisplay' => $getCityDetail[$decodedData]['isDisplay'],
					'createdAt' => $getCityDetail[$decodedData]['createdAt'],
					'updatedAt' => $getCityDetail[$decodedData]['updatedAt'],
					'stateAbb' => $getCityDetail[$decodedData]['state']['stateAbb']
				),
				
				'ledgerGroup'=> array(
					'ledgerGroupId' => $getLedgerGrpDetails[$decodedData]['ledgerGroupId'],	
					'ledgerGroupName' => $getLedgerGrpDetails[$decodedData]['ledgerGroupName'],	
					'underWhat' => $getLedgerGrpDetails[$decodedData]['underWhat'],
					'alias' => $getLedgerGrpDetails[$decodedData]['alias'],
					'natureOfGroup' => $getLedgerGrpDetails[$decodedData]['natureOfGroup'],
					'affectedGroupProfit' => $getLedgerGrpDetails[$decodedData]['affectedGroupProfit']
				),
				
				'company' => array(	
					'companyId' => $getCompanyDetails[$decodedData]['companyId'],
					'companyName' => $getCompanyDetails[$decodedData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$decodedData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$decodedData]['address1'],	
					'address2'=> $getCompanyDetails[$decodedData]['address2'],	
					'emailId'=> $getCompanyDetails[$decodedData]['emailId'],	
					'customerCare'=> $getCompanyDetails[$decodedData]['customerCare'],	
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
			$data[$decodedData]['isDefault'] = in_array($ledgerName[$decodedData], $defaultLedgers);
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}