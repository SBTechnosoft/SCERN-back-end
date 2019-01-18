<?php
namespace ERP\Core\Companies\Entities;

use ERP\Core\Companies\Entities\Company;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends StateService 
{
	//date conversion and merge with json data and returns json array
    public function getEncodedData($status)
	{
		$documentArray = array();
		
		//get constant document-url from document
		$documentUrl =  new ConstantClass();
		$documentArray = $documentUrl->constantVariable();
		
		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$companyId= $decodedJson[0]['company_id'];
		$companyName= $decodedJson[0]['company_name'];
		$companyDisplayName = $decodedJson[0]['company_display_name'];
		$websiteName = $decodedJson[0]['website_name'];
		$address1= $decodedJson[0]['address1'];
		$address2= $decodedJson[0]['address2'];
		$emailId= $decodedJson[0]['email_id'];
		$customerCare= $decodedJson[0]['customer_care'];
		$pincode = $decodedJson[0]['pincode'];
		$pan= $decodedJson[0]['pan'];
		$tin= $decodedJson[0]['tin'];
		$vat_no= $decodedJson[0]['vat_no'];
		$cgst= $decodedJson[0]['cgst'];
		$sgst= $decodedJson[0]['sgst'];
		$cess= $decodedJson[0]['cess'];
		$serviceTaxNo= $decodedJson[0]['service_tax_no'];
		$basicCurrencySymbol = $decodedJson[0]['basic_currency_symbol'];
		$formalName= $decodedJson[0]['formal_name'];
		$noOfDecimalPoints= $decodedJson[0]['no_of_decimal_points'];
		$currencySymbol= $decodedJson[0]['currency_symbol'];
		$documentName= $decodedJson[0]['document_name'];
		$documentUrl= $documentArray['documentUrl'];
		$documentSize= $decodedJson[0]['document_size'];
		$documentFormat= $decodedJson[0]['document_format'];
		$isDisplay= $decodedJson[0]['is_display'];
		$isDefault= $decodedJson[0]['is_default'];
		$stateAbb= $decodedJson[0]['state_abb'];
		$cityId= $decodedJson[0]['city_id'];
		$printType= $decodedJson[0]['print_type'];
	
		//get the state_name from database
		$encodeStateDataClass = new EncodeData();
		$stateStatus = $encodeStateDataClass->getStateData($stateAbb);
		$stateDecodedJson = json_decode($stateStatus,true);
		
		//get the city_name from database
		$cityDetail = new CityDetail();
		$getCityDetail = $cityDetail->getCityDetail($cityId);
		
		//convert amount(number_format) into their company's selected decimal points
		$cess = number_format($cess,$noOfDecimalPoints,'.','');
			
		//date format conversion
		$company = new Company();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$company->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $company->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{	
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$company->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $company->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['companyId'] = $companyId;
		$data['companyName'] = $companyName;
		$data['companyDisplayName'] = $companyDisplayName;
		$data['websiteName'] = $websiteName;
		$data['address1'] = $address1;
		$data['address2'] = $address2;
		$data['emailId'] = $emailId;
		$data['customerCare'] = $customerCare;
		$data['pincode'] = $pincode;
		$data['pan'] = $pan;
		$data['tin'] = $tin;
		$data['vatNo'] = $vat_no;
		$data['sgst'] = $sgst;
		$data['cgst'] = $cgst;
		$data['cess'] = $cess;
		$data['serviceTaxNo'] = $serviceTaxNo;
		$data['basicCurrencySymbol'] = $basicCurrencySymbol;
		$data['formalName'] = $formalName;
		$data['noOfDecimalPoints'] = $noOfDecimalPoints;
		$data['currencySymbol'] = $currencySymbol;
		$data['printType'] = $printType;
		$data['logo'] = array(
			'documentName' => $documentName,
			'documentUrl' => $documentUrl,
			'documentSize' => $documentSize,
			'documentFormat' => $documentFormat
		);
		$data['isDisplay'] = $isDisplay;
		$data['isDefault'] = $isDefault;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		
		$data['state'] = array(
			'stateAbb' => $stateDecodedJson['stateAbb'],
			'stateName' => $stateDecodedJson['stateName'],	
			'isDisplay' => $stateDecodedJson['isDisplay'],	
			'createdAt' => $stateDecodedJson['createdAt'],	
			'updatedAt' => $stateDecodedJson['updatedAt']
		);
		
		$data['city'] = array(
			'cityId' => $getCityDetail['cityId'],
			'cityName' => $getCityDetail['cityName'],
			'isDisplay' => $getCityDetail['isDisplay'],	
			'createdAt' => $getCityDetail['createdAt'],	
			'updatedAt' => $getCityDetail['updatedAt'],	
			'stateAbb' => $getCityDetail['state']['stateAbb']
		);
		$encodeData = json_encode($data);
		return $encodeData;
	}
}