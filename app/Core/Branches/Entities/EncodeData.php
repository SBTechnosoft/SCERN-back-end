<?php
namespace ERP\Core\Branches\Entities;

use ERP\Core\Branches\Entities\Branch;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\CityDetail;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends StateService
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$branchId= $decodedJson[0]['branch_id'];
		$branchName= $decodedJson[0]['branch_name'];
		$address1= $decodedJson[0]['address1'];
		$address2= $decodedJson[0]['address2'];
		$pincode = $decodedJson[0]['pincode'];
		$isDisplay= $decodedJson[0]['is_display'];
		$isDefault= $decodedJson[0]['is_default'];
		$stateAbb= $decodedJson[0]['state_abb'];
		$cityId= $decodedJson[0]['city_id'];
		$companyId= $decodedJson[0]['company_id'];
		
		// get the state details from database
		$encodeStateDataClass = new EncodeData();
		$stateStatus = $encodeStateDataClass->getStateData($stateAbb);
		$stateDecodedJson = json_decode($stateStatus,true);
		
		// get the city details from database
		$cityDetail = new CityDetail();
		$getCityDetail = $cityDetail->getCityDetail($cityId);
		
		// get the company details from database
		$companyDetail = new CompanyDetail();
		$companyDetails = $companyDetail->getCompanyDetails($companyId);
		
		// date format conversion
		$branch = new Branch();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$branch->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $branch->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$branch->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $branch->getUpdated_at();
		}
		// set all data into json array
		$data = array();
		$data['branchId'] = $branchId;
		$data['branchName'] = $branchName;
		$data['address1'] = $address1;
		$data['address2'] = $address2;
		$data['pincode'] = $pincode;
		$data['isDisplay'] = $isDisplay;
		$data['isDefault'] = $isDefault;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		
		$data['company']= array(
			'companyId' => $companyDetails['companyId'],
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
			'formalName' => $companyDetails['formalName'],
			'noOfDecimalPoints' => $companyDetails['noOfDecimalPoints'],
			'currencySymbol' => $companyDetails['currencySymbol'],	
			'logo' => array(
				'documentName' => $companyDetails['logo']['documentName'],	
				'documentUrl' => $companyDetails['logo']['documentUrl'],	
				'documentSize' => $companyDetails['logo']['documentSize'],
				'documentFormat' => $companyDetails['logo']['documentFormat']	
			),
			'isDisplay' => $companyDetails['isDisplay'],	
			'isDefault' => $companyDetails['isDefault'],	
			'createdAt' => $companyDetails['createdAt'],	
			'updatedAt' => $companyDetails['updatedAt'],	
		);
		$data['state']= array(
			'stateAbb' => $stateDecodedJson['stateAbb'],
			'stateName' => $stateDecodedJson['stateName'],
			'isDisplay' => $stateDecodedJson['isDisplay'],	
			'createdAt' => $stateDecodedJson['createdAt'],	
			'updatedAt' => $stateDecodedJson['updatedAt']	
		);
		$data['city']= array(
			'cityId' => $getCityDetail['cityId'],
			'cityName' => $getCityDetail['cityName'],	
			'isDisplay' => $getCityDetail['isDisplay'],	
			'createdAt' => $getCityDetail['createdAt'],	
			'updatedAt' => $getCityDetail['updatedAt'],	
			'stateAbb'=> $getCityDetail['state']['stateAbb']
		);
		$encodeData = json_encode($data);
		return $encodeData;
	}
}