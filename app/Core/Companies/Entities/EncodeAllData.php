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
class EncodeAllData extends StateService
{
	//date conversion and merge with json data and returns json array
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate = array();
		$convertedUpdatedDate = array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$documentArray = array();
		
		//get constant document-url from document
		$documentUrl =  new ConstantClass();
		$documentArray = $documentUrl->constantVariable();
		$encodeDataClass = new EncodeAllData();
		$company = new Company();
		$cityDetail = new CityDetail();

		$stateArray = array();
		$cityArray = array();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$companyName[$decodedData] = $decodedJson[$decodedData]['company_name'];
			$companyDisplayName[$decodedData] = $decodedJson[$decodedData]['company_display_name'];
			$websiteName[$decodedData] = $decodedJson[$decodedData]['website_name'];
			$address1[$decodedData] = $decodedJson[$decodedData]['address1'];
			$address2[$decodedData] = $decodedJson[$decodedData]['address2'];
			$emailId[$decodedData] = $decodedJson[$decodedData]['email_id'];
			$customerCare[$decodedData] = $decodedJson[$decodedData]['customer_care'];
			$pincode[$decodedData] = $decodedJson[$decodedData]['pincode'];
			$pan[$decodedData] = $decodedJson[$decodedData]['pan'];
			$tin[$decodedData] = $decodedJson[$decodedData]['tin'];
			$vat_no[$decodedData] = $decodedJson[$decodedData]['vat_no'];
			$cgst[$decodedData] = $decodedJson[$decodedData]['cgst'];
			$sgst[$decodedData] = $decodedJson[$decodedData]['sgst'];
			$cess[$decodedData] = $decodedJson[$decodedData]['cess'];
			$printType[$decodedData] = $decodedJson[$decodedData]['print_type'];
			$serviceTaxNo[$decodedData] = $decodedJson[$decodedData]['service_tax_no'];
			$basicCurrencySymbol[$decodedData] = $decodedJson[$decodedData]['basic_currency_symbol'];
			$formalName[$decodedData] = $decodedJson[$decodedData]['formal_name'];
			$noOfDecimalPoints[$decodedData] = $decodedJson[$decodedData]['no_of_decimal_points'];
			$currencySymbol[$decodedData] = $decodedJson[$decodedData]['currency_symbol'];
			$documentName[$decodedData] = $decodedJson[$decodedData]['document_name'];
			$documentUrl = $documentArray['documentUrl'];
			$documentSize[$decodedData] = $decodedJson[$decodedData]['document_size'];
			$documentFormat[$decodedData] = $decodedJson[$decodedData]['document_format'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			$isDefault[$decodedData] = $decodedJson[$decodedData]['is_default'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			
			//get the state details from database
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
			
			//convert amount(number_format) into their company's selected decimal points
			$cess[$decodedData] = number_format($cess[$decodedData],$noOfDecimalPoints[$decodedData],'.','');
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$company->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $company->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$company->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $company->getUpdated_at();
			}
			$data[$decodedData]= array(
				'companyId'=>$companyId[$decodedData],
				'companyName' => $companyName[$decodedData],
				'companyDisplayName' => $companyDisplayName[$decodedData],
				'websiteName' => $websiteName[$decodedData],
				'address1' => $address1[$decodedData],
				'address2' => $address2[$decodedData],
				'emailId' => $emailId[$decodedData],
				'customerCare' => $customerCare[$decodedData],
				'pincode'=> $pincode[$decodedData],
				'pan' => $pan[$decodedData],
				'tin' => $tin[$decodedData],
				'vatNo' =>$vat_no[$decodedData],
				'cgst' =>$cgst[$decodedData],
				'sgst' =>$sgst[$decodedData],
				'cess' =>$cess[$decodedData],
				'serviceTaxNo' => $serviceTaxNo[$decodedData],
				'basicCurrencySymbol' => $basicCurrencySymbol[$decodedData],
				'formalName' => $formalName[$decodedData],
				'noOfDecimalPoints' => $noOfDecimalPoints[$decodedData],
				'currencySymbol' => $currencySymbol[$decodedData],
				'printType' => $printType[$decodedData],
				'logo' => array(
					'documentName'=> $documentName[$decodedData],
					'documentUrl' => $documentUrl,
					'documentSize' => $documentSize[$decodedData],
					'documentFormat' => $documentFormat[$decodedData]
				),
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
				'city'=> array(
					'cityId' => $cityId[$decodedData],
					'cityName' => $getCityDetail[$decodedData]['cityName'],
					'isDisplay' => $getCityDetail[$decodedData]['isDisplay'],
					'createdAt' => $getCityDetail[$decodedData]['createdAt'],
					'updatedAt' => $getCityDetail[$decodedData]['updatedAt'],
					'stateAbb' => $getCityDetail[$decodedData]['state']['stateAbb']
				)
			);
		}
		
		return $documentArray['prefixConstant'].json_encode($data);
	}
}