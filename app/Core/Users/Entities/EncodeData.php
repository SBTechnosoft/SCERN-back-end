<?php
namespace ERP\Core\Users\Entities;

use ERP\Core\Users\Entities\User;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\BranchDetail;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends StateService 
{
	//date conversion and merge with json data and returns json array
    public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
			
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$userId= $decodedJson[0]['user_id'];
		$userName= $decodedJson[0]['user_name'];
		$userType= $decodedJson[0]['user_type'];
		$contactNo= $decodedJson[0]['contact_no'];
		$emailId= $decodedJson[0]['email_id'];
		$address= $decodedJson[0]['address'];
		$password= $decodedJson[0]['password'];
		$pincode= $decodedJson[0]['pincode'];
		$cityId= $decodedJson[0]['city_id'];
		$stateAbb= $decodedJson[0]['state_abb'];
		$companyId= $decodedJson[0]['company_id'];
		$branchId= $decodedJson[0]['branch_id'];
		$permissionArray= json_decode($decodedJson[0]['permission_array']);
		$defaultCompanyId= $decodedJson[0]['default_company_id'];
		
		//password decoding
		$decodedPassword = base64_decode($password);
			
		//get the state_name from database
		$encodeStateDataClass = new EncodeData();
		$stateStatus = $encodeStateDataClass->getStateData($stateAbb);
		$stateDecodedJson = json_decode($stateStatus,true);
		
		//get the city_name from database
		$cityDetail = new CityDetail();
		$getCityDetail = $cityDetail->getCityDetail($cityId);
		
		//get the company detail from database
		$companyDetail  = new CompanyDetail();
		$getCompanyDetails = $companyDetail->getCompanyDetails($companyId);
		
		//get the branch detail from database
		$branchDetail  = new BranchDetail();
		$getBranchDetails = $branchDetail->getBranchDetails($branchId);
		
		//date format conversion
		$user = new User();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$user->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $user->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$user->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $user->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['userId'] = $userId;
		$data['userName'] = $userName;
		$data['userType'] = $userType;
		$data['contactNo'] = $contactNo;
		$data['emailId'] = $emailId;
		$data['address'] = $address;	
		$data['password'] = $decodedPassword;	
		$data['pincode'] = $pincode;	
		$data['permissionArray'] = $permissionArray;	
		$data['defaultCompanyId'] = $defaultCompanyId;	
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
		$data['company'] = array(
			'companyId' => $getCompanyDetails['companyId'],	
			'companyName' => $getCompanyDetails['companyName'],
			'companyDisplayName' => $getCompanyDetails['companyDisplayName'],
			'address1' => $getCompanyDetails['address1'],	
			'address2' => $getCompanyDetails['address2'],	
			'pincode' => $getCompanyDetails['pincode'],
			'pan' => $getCompanyDetails['pan'],	
			'tin' => $getCompanyDetails['tin'],	
			'vatNo' => $getCompanyDetails['vatNo'],	
			'serviceTaxNo' => $getCompanyDetails['serviceTaxNo'],	
			'basicCurrencySymbol' => $getCompanyDetails['basicCurrencySymbol'],
			'formalName' => $getCompanyDetails['formalName'],	
			'noOfDecimalPoints' => $getCompanyDetails['noOfDecimalPoints'],
			'currencySymbol' => $getCompanyDetails['currencySymbol'],
			'logo' => array(
				'documentName' => $getCompanyDetails['logo']['documentName'],	
				'documentUrl' => $getCompanyDetails['logo']['documentUrl'],	
				'documentSize' => $getCompanyDetails['logo']['documentSize'],	
				'documentFormat' => $getCompanyDetails['logo']['documentFormat']
			),
			'isDisplay' => $getCompanyDetails['isDisplay'],	
			'isDefault' => $getCompanyDetails['isDefault'],	
			'createdAt' => $getCompanyDetails['createdAt'],	
			'updatedAt' => $getCompanyDetails['updatedAt'],
			'stateAbb' => $getCompanyDetails['state']['stateAbb'],	
			'cityId' => $getCompanyDetails['city']['cityId']
		);
		$data['branch'] = array(
			'branchId' => $getBranchDetails['branchId'],
			'branchName' => $getBranchDetails['branchName'],	
			'address1' => $getBranchDetails['address1'],	
			'address2' => $getBranchDetails['address2'],	
			'pincode' => $getBranchDetails['pincode'],	
			'isDisplay' => $getBranchDetails['isDisplay'],	
			'isDefault' => $getBranchDetails['isDefault'],	
			'createdAt' => $getBranchDetails['createdAt'],	
			'updatedAt' => $getBranchDetails['updatedAt'],	
			'stateAbb' => $getBranchDetails['state']['stateAbb'],	
			'cityId' => $getBranchDetails['city']['cityId'],	
			'companyId' => $getBranchDetails['company']['companyId']
		);	
		$encodeData = json_encode($data);
		return $encodeData;
	}
}