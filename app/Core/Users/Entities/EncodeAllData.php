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
class EncodeAllData extends StateService
{
	//date conversion and merge with json data and returns json array
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$user = new User();
		$data = array();

		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$userName[$decodedData] = $decodedJson[$decodedData]['user_name'];
			$userType[$decodedData] = $decodedJson[$decodedData]['user_type'];
			$userId[$decodedData] = $decodedJson[$decodedData]['user_id'];
			$emailId[$decodedData] = $decodedJson[$decodedData]['email_id'];
			$password[$decodedData] = $decodedJson[$decodedData]['password'];
			$contactNo[$decodedData] = $decodedJson[$decodedData]['contact_no'];
			$address[$decodedData] = $decodedJson[$decodedData]['address'];
			$pincode[$decodedData] = $decodedJson[$decodedData]['pincode'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$branchId[$decodedData] = $decodedJson[$decodedData]['branch_id'];
			$permissionArray[$decodedData] = json_decode($decodedJson[$decodedData]['permission_array']);
			$defaultCompanyId[$decodedData] = $decodedJson[$decodedData]['default_company_id'];
			
			//password decoding
			$decodedPassword[$decodedData] = base64_decode($password[$decodedData]);
			
			//get the state details from database
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
			
			//get the company detail from database
			$companyDetail  = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			
			//get the branch detail from database
			$branchDetail  = new BranchDetail();
			$getBranchDetails[$decodedData] = $branchDetail->getBranchDetails($branchId[$decodedData]);
			
			// date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$user->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $user->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$user->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $user->getUpdated_at();
			}
			
			$data[$decodedData]= array(
				'userName' => $userName[$decodedData],
				'userType' => $userType[$decodedData],
				'userId' =>$userId[$decodedData],
				'emailId' => $emailId[$decodedData],
				'password' => $decodedPassword[$decodedData],
				'contactNo' =>$contactNo[$decodedData],
				'address' =>$address[$decodedData],
				'pincode' =>$pincode[$decodedData],
				'permissionArray' =>$permissionArray[$decodedData],
				'defaultCompanyId' =>$defaultCompanyId[$decodedData],
				'createdAt' =>$getCreatedDate[$decodedData],
				'updatedAt' =>$getUpdatedDate[$decodedData],
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
					'logo' => array(
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
				),
				
				'branch' => array(
					'branchId' => $getBranchDetails[$decodedData]['branchId'],	
					'branchName'=> $getBranchDetails[$decodedData]['branchName'],	
					'address1' => $getBranchDetails[$decodedData]['address1'],	
					'address2' => $getBranchDetails[$decodedData]['address2'],	
					'pincode' => $getBranchDetails[$decodedData]['pincode'],	
					'isDisplay' => $getBranchDetails[$decodedData]['isDisplay'],	
					'isDefault' => $getBranchDetails[$decodedData]['isDefault'],	
					'createdAt' => $getBranchDetails[$decodedData]['createdAt'],	
					'updatedAt' => $getBranchDetails[$decodedData]['updatedAt'],	
					'stateAbb' => $getBranchDetails[$decodedData]['state']['stateAbb'],	
					'cityId' => $getBranchDetails[$decodedData]['city']['cityId'],	
					'companyId' => $getBranchDetails[$decodedData]['company']['companyId']	
				)
			);	

		}
		
		$encodedData = json_encode($data);
		return $encodedData;
		// return $data;
	}
}