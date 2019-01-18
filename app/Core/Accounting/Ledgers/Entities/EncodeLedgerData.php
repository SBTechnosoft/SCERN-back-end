<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\LedgerGroupDetail;
use ERP\Core\Entities\CityDetail;
use ERP\Core\Entities\CompanyDetail;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeLedgerData extends StateService 
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson['created_at'];
		$updatedAt= $decodedJson['updated_at'];
		$ledgerId= $decodedJson['ledger_id'];
		$ledgerName= $decodedJson['ledger_name'];
		$alias= $decodedJson['alias'];
		$inventoryAffected= $decodedJson['inventory_affected'];
		$address1= $decodedJson['address1'];
		$address2= $decodedJson['address2'];
		$isDealer= $decodedJson['is_dealer'];
		$contactNo= $decodedJson['contact_no'];
		$emailId= $decodedJson['email_id'];
		$invoiceNumber= $decodedJson['invoice_number'];
		$outstandingLimit= $decodedJson['outstanding_limit'];
		$outstandingLimitType= $decodedJson['outstanding_limit_type'];
		$panNo = $decodedJson['pan'];
		$tinNo = $decodedJson['tin'];
		$cgst= $decodedJson['cgst'];
		$sgst= $decodedJson['sgst'];
		$bankId= $decodedJson['bank_id'];
		$bankDtlId= $decodedJson['bank_dtl_id'];
		$micrCode= $decodedJson['micr_code'];
		$stateAbb= $decodedJson['state_abb'];
		$cityId= $decodedJson['city_id'];
		$ledgerGrpId= $decodedJson['ledger_group_id'];
		$companyId= $decodedJson['company_id'];
		
		//get the state details from database
		$encodeStateDataClass = new EncodeData();
		$stateStatus = $encodeStateDataClass->getStateData($stateAbb);
		$stateDecodedJson = json_decode($stateStatus,true);
		
		//get the city details from database
		$cityDetail = new CityDetail();
		$getCityDetail = $cityDetail->getCityDetail($cityId);
		
		//get the ledger-group details from database
		$ledgerGrpDetail = new LedgerGroupDetail();
		$getLedgerGrpDetail = $ledgerGrpDetail->getLedgerGrpDetails($ledgerGrpId);
		
		//get the company details from database
		$companyDetail = new CompanyDetail();
		$companyDetails = $companyDetail->getCompanyDetails($companyId);
		
		//date format conversion
		$ledger = new Ledger();
		
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$ledger->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $ledger->getCreated_at();
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$ledger->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $ledger->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['ledgerId'] = $ledgerId;
		$data['ledgerName'] = $ledgerName;
		$data['alias'] = $alias;
		$data['inventoryAffected'] = $inventoryAffected;
		$data['address1'] = $address1;
		$data['address2'] = $address2;
		$data['isDealer'] = $isDealer;
		$data['contactNo'] = $contactNo;
		$data['emailId'] = $emailId;
		$data['invoiceNumber'] = $invoiceNumber;
		$data['outstandingLimit'] = $outstandingLimit;
		$data['outstandingLimitType'] = $outstandingLimitType;
		$data['pan'] = $panNo;
		$data['tin'] = $tinNo;
		$data['cgst'] = $cgst;
		$data['sgst'] = $sgst;
		$data['bankId'] = $bankId;
		$data['bankDtlId'] = $bankDtlId;
		$data['micrCode'] = $micrCode;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['ledgerGroup']= array(
			'ledgerGroupId' => $ledgerGrpId,	
			'ledgerGroupName' => $getLedgerGrpDetail['ledgerGroupName'],
			'alias' => $getLedgerGrpDetail['alias'],
			'underWhat' => $getLedgerGrpDetail['underWhat'],
			'natureOfGroup' => $getLedgerGrpDetail['natureOfGroup'],
			'affectedGroupProfit' => $getLedgerGrpDetail['affectedGroupProfit']
		);
		$data['state'] = array(
			'stateAbb' => $stateAbb,
			'stateName' => $stateDecodedJson['stateName'],
			'isDisplay' => $stateDecodedJson['isDisplay'],	
			'createdAt' => $stateDecodedJson['createdAt'],	
			'updatedAt' => $stateDecodedJson['updatedAt']	
		);
		$data['city'] = array(
			'cityId' => $cityId,
			'cityName' => $getCityDetail['cityName'],	
			'isDisplay'=> $getCityDetail['isDisplay'],	
			'createdAt' => $getCityDetail['createdAt'],	
			'updatedAt' => $getCityDetail['updatedAt'],	
			'stateAbb'=> $getCityDetail['state']['stateAbb']
		);
		$data['company']= array(
			'companyId' => $companyId,
			'companyName' => $companyDetails['companyName'],	
			'companyDisplayName' => $companyDetails['companyDisplayName'],	
			'address1' => $companyDetails['address1'],	
			'address2' => $companyDetails['address2'],	
			'emailId' => $companyDetails['emailId'],	
			'customerCare' => $companyDetails['customerCare'],	
			'pincode' => $companyDetails['pincode'],
			'pan' => $companyDetails['pan'],	
			'tin' => $companyDetails['tin'],
			'vatNo' =>$companyDetails['vatNo'],
			'serviceTaxNo' => $companyDetails['serviceTaxNo'],
			'basicCurrencySymbol' => $companyDetails['basicCurrencySymbol'],
			'formalName' => $companyDetails['formalName'],
			'noOfDecimalPoints' => $companyDetails['currencySymbol'],	
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