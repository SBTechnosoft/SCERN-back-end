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
			
			//get the company details from database
			$companyDetail = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			
			//get the client detail from database
			$clientService = new ClientService();
			$getClientDetails[$decodedData] = $clientService->getClientData($clientId[$decodedData]);
			
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
			if(strcmp($deliveryDate[$decodedData],'0000-00-00')==0)
			{
				$getDeliveryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getDeliveryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $deliveryDate[$decodedData])->format('d-m-Y');
			}
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$clientData = json_decode($getClientDetails[$jsonData]);
			$data[$jsonData]= array(
				'jobCardNo'=>$jobCardNo[$jsonData],
				'clientName' => $clientName[$jsonData],
				'address' => $address[$jsonData],
				'jobCardId' => $jobCardId[$jsonData],
				'contactNo' => $contactNo[$jsonData],
				'emailId' => $emailId[$jsonData],
				'labourCharge' => $labourCharge[$jsonData],
				'serviceType' => $serviceType[$jsonData],
				'advance' => $advance[$jsonData],
				'total' => $total[$jsonData],
				'tax' => $tax[$jsonData],
				'paymentMode' => $paymentMode[$jsonData],
				'bankName' => $bankName[$jsonData],
				'chequeNo' => $chequeNo[$jsonData],
				'productArray' => $productArray[$jsonData],
				'entryDate' => $getEntryDate[$jsonData],
				'deliveryDate' => $getDeliveryDate[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				
				'state' => array(
					'stateAbb' => $stateAbb[$jsonData],
					'stateName' => $stateName[$jsonData],
					'isDisplay' => $stateIsDisplay[$jsonData],
					'createdAt' => $stateCreatedAt[$jsonData],
					'updatedAt' => $stateUpdatedAt[$jsonData]
				),
				
				'city' => array(
					'cityId' => $cityId[$jsonData],
					'cityName' => $getCityDetail[$jsonData]['cityName'],
					'isDisplay' => $getCityDetail[$jsonData]['isDisplay'],
					'createdAt' => $getCityDetail[$jsonData]['createdAt'],
					'updatedAt' => $getCityDetail[$jsonData]['updatedAt'],
					'stateAbb' => $getCityDetail[$jsonData]['state']['stateAbb']
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
					'companyId' => $getCompanyDetails[$jsonData]['companyId'],
					'companyName' => $getCompanyDetails[$jsonData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$jsonData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$jsonData]['address1'],	
					'address2'=> $getCompanyDetails[$jsonData]['address2'],	
					'pincode' => $getCompanyDetails[$jsonData]['pincode'],	
					'pan' => $getCompanyDetails[$jsonData]['pan'],	
					'tin'=> $getCompanyDetails[$jsonData]['tin'],	
					'vatNo' => $getCompanyDetails[$jsonData]['vatNo'],	
					'serviceTaxNo' => $getCompanyDetails[$jsonData]['serviceTaxNo'],	
					'basicCurrencySymbol' => $getCompanyDetails[$jsonData]['basicCurrencySymbol'],	
					'formalName' => $getCompanyDetails[$jsonData]['formalName'],	
					'noOfDecimalPoints' => $getCompanyDetails[$jsonData]['noOfDecimalPoints'],	
					'currencySymbol' => $getCompanyDetails[$jsonData]['currencySymbol'],
					'logo' => array(
						'documentName' => $getCompanyDetails[$jsonData]['logo']['documentName'],	
						'documentUrl' => $getCompanyDetails[$jsonData]['logo']['documentUrl'],	
						'documentSize' =>$getCompanyDetails[$jsonData]['logo']['documentSize'],	
						'documentFormat' => $getCompanyDetails[$jsonData]['logo']['documentFormat']
					),
					'isDisplay' => $getCompanyDetails[$jsonData]['isDisplay'],	
					'isDefault' => $getCompanyDetails[$jsonData]['isDefault'],	
					'createdAt' => $getCompanyDetails[$jsonData]['createdAt'],	
					'updatedAt' => $getCompanyDetails[$jsonData]['updatedAt'],	
					'stateAbb' => $getCompanyDetails[$jsonData]['state']['stateAbb'],	
					'cityId' => $getCompanyDetails[$jsonData]['city']['cityId']	
				)		
			);
		}
		return json_encode($data);
	}
}