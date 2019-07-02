<?php
namespace ERP\Core\Branches\Entities;

use ERP\Core\Branches\Entities\Branch;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
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
		$branch = new Branch();
		$data = array();
		$encodeDataClass = new EncodeAllData();
		$cityDetail = new CityDetail();
		$companyDetail = new CompanyDetail();

		$stateArray = array();
		$cityArray = array();
		$companyDetailArray = array();

		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$branchId[$decodedData] = $decodedJson[$decodedData]['branch_id'];
			$branchName[$decodedData] = $decodedJson[$decodedData]['branch_name'];
			$address1[$decodedData] = $decodedJson[$decodedData]['address1'];
			$address2[$decodedData] = $decodedJson[$decodedData]['address2'];
			$pincode[$decodedData] = $decodedJson[$decodedData]['pincode'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			$isDefault[$decodedData] = $decodedJson[$decodedData]['is_default'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			
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
				$cityArray[$stateAbb[$decodedData]] = $cityDetail->getCityDetail($cityId[$decodedData]);
			}
			$getCityDetail[$decodedData] = $cityArray[$stateAbb[$decodedData]];
			 
			//get the company details from database
			if (!isset($companyDetailArray[$companyId[$decodedData]])) {
				$companyDetailArray[$companyId[$decodedData]] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			}
			$getCompanyDetails[$decodedData] = $companyDetailArray[$companyId[$decodedData]];
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$branch->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $branch->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$branch->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $branch->getUpdated_at();
			}
			$data[$decodedData]= array(
				'branchId'=>$branchId[$decodedData],
				'branchName' => $branchName[$decodedData],
				'address1' => $address1[$decodedData],
				'address2' => $address2[$decodedData],
				'pincode'=> $pincode[$decodedData],
				'isDisplay' => $isDisplay[$decodedData],
				'isDefault' => $isDefault[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData],
				
				'state' => array(
					'stateAbb' => $stateAbb[$decodedData],
					'stateName' => $stateName[$decodedData],
					'isDisplay' => $stateIsDisplay[$decodedData],
					'createdAt' => $stateCreatedAt[$decodedData],
					'updatedAt' => $stateUpdatedAt[$decodedData]
				),
				
				'city' => array(
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
				)		
			);
		}
		return json_encode($data);
	}
}