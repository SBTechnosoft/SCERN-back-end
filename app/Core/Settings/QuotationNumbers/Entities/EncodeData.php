<?php
namespace ERP\Core\Settings\QuotationNumbers\Entities;

use ERP\Core\Settings\QuotationNumbers\Entities\QuotationNumber;
use ERP\Core\Companies\Services\CompanyService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends CompanyService 
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt = $decodedJson[0]['updated_at'];
		$quotationId= $decodedJson[0]['quotation_id'];
		$quotationLabel= $decodedJson[0]['quotation_label'];
		$quotationType= $decodedJson[0]['quotation_type'];
		$startAt= $decodedJson[0]['start_at'];
		$endAt = $decodedJson[0]['end_at'];
		$companyId= $decodedJson[0]['company_id'];
		
		//get the company details from database
		$encodeCompanyDataClass = new EncodeData();
		$companyStatus = $encodeCompanyDataClass->getCompanyData($companyId);
		$companyDecodedJson = json_decode($companyStatus,true);
		
		//date format conversion
		$quotation = new QuotationNumber();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$quotation->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $quotation->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$quotation->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $quotation->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['quotationId'] = $quotationId;
		$data['quotationLabel'] = $quotationLabel;
		$data['quotationType'] = $quotationType;
		$data['startAt'] = $startAt;
		$data['endAt'] = $endAt;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;
		
		$data['company']= array(
			'companyId' => $companyDecodedJson['companyId'],	
			'companyName' => $companyDecodedJson['companyName'],
			'companyDisplayName' => $companyDecodedJson['companyDisplayName'],	
			'address1' => $companyDecodedJson['address1'],	
			'address2' => $companyDecodedJson['address2'],
			'pincode'=> $companyDecodedJson['pincode'],	
			'pan' => $companyDecodedJson['pan'],	
			'tin' => $companyDecodedJson['tin'],	
			'vatNo' => $companyDecodedJson['vatNo'],
			'serviceTaxNo' => $companyDecodedJson['serviceTaxNo'],	
			'basicCurrencySymbol'=> $companyDecodedJson['basicCurrencySymbol'],
			'formalName'=> $companyDecodedJson['formalName'],
			'noOfDecimalPoints' => $companyDecodedJson['noOfDecimalPoints'],	
			'currencySymbol' => $companyDecodedJson['currencySymbol'],
			'logo' => array(
				'documentName'=> $companyDecodedJson['logo']['documentName'],
				'documentUrl' => $companyDecodedJson['logo']['documentUrl'],
				'documentSize' => $companyDecodedJson['logo']['documentSize'],	
				'documentFormat' => $companyDecodedJson['logo']['documentFormat']
			),
			'isDisplay' => $companyDecodedJson['isDisplay'],	
			'isDefault' => $companyDecodedJson['isDefault'],	
			'createdAt' => $companyDecodedJson['createdAt'],	
			'updatedAt' => $companyDecodedJson['updatedAt'],
			'stateAbb' => $companyDecodedJson['state']['stateAbb'],
			'cityId' => $companyDecodedJson['city']['cityId']
		);
		$encodeData = json_encode($data);
		return $encodeData;
	}
}