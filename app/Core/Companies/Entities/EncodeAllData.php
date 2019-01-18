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
		
		$company = new Company();
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
		}
		
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'companyId'=>$companyId[$jsonData],
				'companyName' => $companyName[$jsonData],
				'companyDisplayName' => $companyDisplayName[$jsonData],
				'websiteName' => $websiteName[$jsonData],
				'address1' => $address1[$jsonData],
				'address2' => $address2[$jsonData],
				'emailId' => $emailId[$jsonData],
				'customerCare' => $customerCare[$jsonData],
				'pincode'=> $pincode[$jsonData],
				'pan' => $pan[$jsonData],
				'tin' => $tin[$jsonData],
				'vatNo' =>$vat_no[$jsonData],
				'cgst' =>$cgst[$jsonData],
				'sgst' =>$sgst[$jsonData],
				'cess' =>$cess[$jsonData],
				'serviceTaxNo' => $serviceTaxNo[$jsonData],
				'basicCurrencySymbol' => $basicCurrencySymbol[$jsonData],
				'formalName' => $formalName[$jsonData],
				'noOfDecimalPoints' => $noOfDecimalPoints[$jsonData],
				'currencySymbol' => $currencySymbol[$jsonData],
				'printType' => $printType[$jsonData],
				'logo' => array(
					'documentName'=> $documentName[$jsonData],
					'documentUrl' => $documentUrl,
					'documentSize' => $documentSize[$jsonData],
					'documentFormat' => $documentFormat[$jsonData]
				),
				'isDisplay' => $isDisplay[$jsonData],
				'isDefault' => $isDefault[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				
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
				)
			);
		}
		return $documentArray['prefixConstant'].json_encode($data);
	}
}