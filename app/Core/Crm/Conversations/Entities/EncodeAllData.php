<?php
namespace ERP\Core\Crm\JobForm\Entities;

use ERP\Core\Crm\JobForm\Entities\JobForm;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Clients\Services\ClientService;
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
		$getEntryDate = array();
		$jobForm = new JobForm();
		$stateArray = array();
		$cityArray = array();
		$companyArray = array();
		$clientArray = array();
		$encodeDataClass = new EncodeAllData();
		$cityDetail = new CityDetail();
		$companyDetail = new CompanyDetail();
		$clientService = new ClientService();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$entryDate[$decodedData] = $decodedJson[$decodedData]['entry_date'];
			$deliveryDate[$decodedData] = $decodedJson[$decodedData]['delivery_date'];
			$jobCardId[$decodedData] = $decodedJson[$decodedData]['job_card_id'];
			$clientName[$decodedData] = $decodedJson[$decodedData]['client_name'];
			$address[$decodedData] = $decodedJson[$decodedData]['address'];
			$contactNo[$decodedData] = $decodedJson[$decodedData]['contact_no'];
			$emailId[$decodedData] = $decodedJson[$decodedData]['email_id'];
			$jobCardNo[$decodedData] = $decodedJson[$decodedData]['job_card_no'];
			$labourCharge[$decodedData] = $decodedJson[$decodedData]['labour_charge'];
			$serviceType[$decodedData] = $decodedJson[$decodedData]['service_type'];
			$advance[$decodedData] = $decodedJson[$decodedData]['advance'];
			$total[$decodedData] = $decodedJson[$decodedData]['total'];
			$tax[$decodedData] = $decodedJson[$decodedData]['tax'];
			$paymentMode[$decodedData] = $decodedJson[$decodedData]['payment_mode'];
			$bankName[$decodedData] = $decodedJson[$decodedData]['bank_name'];
			$chequeNo[$decodedData] = $decodedJson[$decodedData]['cheque_no'];
			$productArray[$decodedData] = $decodedJson[$decodedData]['product_array'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$clientId[$decodedData] = $decodedJson[$decodedData]['client_id'];
			
			//get the state detail from database
			if (!isset($stateArray[$stateAbb[$decodedData]])) {
				$stateStatus[$decodedData] = $encodeDataClass->getStateData($stateAbb[$decodedData]);
				$stateArray[$stateAbb[$decodedData]] = json_decode($stateStatus[$decodedData],true);
			}
			$stateDecodedJson[$decodedData] = $stateArray[$stateAbb[$decodedData]];
			$stateName[$decodedData]= $stateDecodedJson[$decodedData]['stateName'];
			$stateIsDisplay[$decodedData]= $stateDecodedJson[$decodedData]['isDisplay'];
			$stateCreatedAt[$decodedData]= $stateDecodedJson[$decodedData]['createdAt'];
			$stateUpdatedAt[$decodedData]= $stateDecodedJson[$decodedData]['updatedAt'];
			
			//get the city details from database
			if (!isset($cityArray[$cityId[$decodedData]])) {
				$cityArray[$cityId[$decodedData]] = $cityDetail->getCityDetail($cityId[$decodedData])
			}
			$getCityDetail[$decodedData] = $cityArray[$cityId[$decodedData]];
			
			//get the company details from database
			if (!isset($companyArray[$companyId[$decodedData]])) {
				$companyArray[$companyId[$decodedData]] = $companyDetail->getCompanyDetails($companyId[$decodedData])
			}
			$getCompanyDetails[$decodedData] = $companyArray[$companyId[$decodedData]];
			
			//get the client detail from database
			if (!isset($clientArray[$clientId[$decodedData]])) {
				$clientArray[$clientId[$decodedData]] = $clientService->getClientData($clientId[$decodedData]);
			}
			$getClientDetails[$decodedData] = $clientArray[$clientId[$decodedData]];
			
			//convert amount(number_format) into their company's selected decimal points
			$advance[$decodedData] = number_format($advance[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$total[$decodedData] = number_format($total[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$tax[$decodedData] = number_format($tax[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$labourCharge[$decodedData] = number_format($labourCharge[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$jobForm->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $jobForm->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$jobForm->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $jobForm->getUpdated_at();
			}
			if(strcmp($entryDate[$decodedData],'0000-00-00')==0)
			{
				$getEntryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getEntryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
			}
			if(strcmp($deliveryDate[$decodedData],'0000-00-00 ')==0)
			{
				$getDeliveryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getDeliveryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $deliveryDate[$decodedData])->format('d-m-Y');
			}
			$clientData = json_decode($getClientDetails[$decodedData]);
			$data[$decodedData]= array(
				'jobCardNo'=>$jobCardNo[$decodedData],
				'clientName' => $clientName[$decodedData],
				'address' => $address[$decodedData],
				'jobCardId' => $jobCardId[$decodedData],
				'contactNo' => $contactNo[$decodedData],
				'emailId' => $emailId[$decodedData],
				'labourCharge' => $labourCharge[$decodedData],
				'serviceType' => $serviceType[$decodedData],
				'advance' => $advance[$decodedData],
				'total' => $total[$decodedData],
				'tax' => $tax[$decodedData],
				'paymentMode' => $paymentMode[$decodedData],
				'bankName' => $bankName[$decodedData],
				'chequeNo' => $chequeNo[$decodedData],
				'productArray' => $productArray[$decodedData],
				'entryDate' => $getEntryDate[$decodedData],
				'deliveryDate' => $getDeliveryDate[$decodedData],
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
				'client' => array(
					'clientId'=>$clientData->clientId,
					'clientName'=>$clientData->clientName,
					'companyName'=>$clientData->companyName,
					'contactNo'=>$clientData->contactNo,
					'emailId'=>$clientData->emailId,
					'address1'=>$clientData->address1,
					'isDisplay'=>$clientData->isDisplay,
					'createdAt'=>$clientData->createdAt,
					'updatedAt'=>$clientData->updatedAt,
					'stateAbb'=>$clientData->state->stateAbb,
					'cityId'=>$clientData->city->cityId
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