<?php
namespace ERP\Core\Crm\JobForm\Entities;

use ERP\Core\Crm\JobForm\Entities\JobForm;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\CityDetail;
use Carbon;
use ERP\Core\Clients\Services\ClientService;
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
		$entryDate= $decodedJson[0]['entry_date'];
		$deliveryDate= $decodedJson[0]['delivery_date'];
		$jobCardId= $decodedJson[0]['job_card_id'];
		$clientName= $decodedJson[0]['client_name'];
		$address= $decodedJson[0]['address'];
		$contactNo= $decodedJson[0]['contact_no'];
		$emailId = $decodedJson[0]['email_id'];
		$jobCardNo= $decodedJson[0]['job_card_no'];
		$labourCharge= $decodedJson[0]['labour_charge'];
		$serviceType= $decodedJson[0]['service_type'];
		$advance= $decodedJson[0]['advance'];
		$total= $decodedJson[0]['total'];
		$tax= $decodedJson[0]['tax'];
		$paymentMode= $decodedJson[0]['payment_mode'];
		$bankName= $decodedJson[0]['bank_name'];
		$chequeNo= $decodedJson[0]['cheque_no'];
		$productArray= $decodedJson[0]['product_array'];
		$stateAbb= $decodedJson[0]['state_abb'];
		$cityId= $decodedJson[0]['city_id'];
		$companyId= $decodedJson[0]['company_id'];
		$clientId= $decodedJson[0]['client_id'];
		
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
		
		//get the client details from database
		$clientService = new ClientService();
		$clientStatus = $clientService->getClientData($clientId);
		$clientDecodedJson = json_decode($clientStatus,true);
		
		//convert amount(number_format) into their company's selected decimal points
		$advance = number_format($advance,$companyDetails['noOfDecimalPoints'],'.','');
		$total = number_format($total,$companyDetails['noOfDecimalPoints'],'.','');
		$tax = number_format($tax,$companyDetails['noOfDecimalPoints'],'.','');
		$labourCharge = number_format($labourCharge,$companyDetails['noOfDecimalPoints'],'.','');
			
		// date format conversion
		$jobForm = new JobForm();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$jobForm->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $jobForm->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$jobForm->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $jobForm->getUpdated_at();
		}
		if(strcmp($entryDate,'0000-00-00')==0)
		{
			$getEntryDate = "00-00-0000";
		}
		else
		{
			$getEntryDate = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate)->format('d-m-Y');
			
		}
		if(strcmp($deliveryDate,'0000-00-00')==0)
		{
			$getDeliveryDate = "00-00-0000";
		}
		else
		{
			$getDeliveryDate = Carbon\Carbon::createFromFormat('Y-m-d', $deliveryDate)->format('d-m-Y');
		}
		
		// set all data into json array
		$data = array();
		$data['jobCardNo']=$jobCardNo;
		$data['clientName'] = $clientName;
		$data['address']= $address;
		$data['jobCardId']= $jobCardId;
		$data['contactNo']= $contactNo;
		$data['emailId']= $emailId;
		$data['labourCharge']= $labourCharge;
		$data['serviceType']= $serviceType;
		$data['advance']= $advance;
		$data['total']= $total;
		$data['tax']= $tax;
		$data['paymentMode']= $paymentMode;
		$data['bankName']= $bankName;
		$data['chequeNo']= $chequeNo;
		$data['productArray']= $productArray;
		$data['entryDate']= $getEntryDate;
		$data['deliveryDate']= $getDeliveryDate;
		$data['createdAt']= $getCreatedDate;
		$data['updatedAt']= $getUpdatedDate;
		
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
		$data['client']= array(
			'clientId' => $clientDecodedJson['clientId'],	
			'clientName' => $clientDecodedJson['clientName'],	
			'companyName' => $clientDecodedJson['companyName'],	
			'contactNo' => $clientDecodedJson['contactNo'],	
			'emailId' => $clientDecodedJson['emailId'],	
			'address1' => $clientDecodedJson['address1'],		
			'isDisplay' => $clientDecodedJson['isDisplay'],	
			'createdAt' => $clientDecodedJson['createdAt'],	
			'updatedAt' => $clientDecodedJson['updatedAt'],	
			'stateAbb' => $clientDecodedJson['state']['stateAbb'],	
			'cityId' => $clientDecodedJson['city']['cityId']
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