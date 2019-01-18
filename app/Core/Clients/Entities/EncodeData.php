<?php
namespace ERP\Core\Clients\Entities;

use ERP\Core\Clients\Entities\Client;
use ERP\Core\States\Services\StateService;
use ERP\Core\Entities\CityDetail;
use ERP\Core\Settings\Professions\Services\ProfessionService;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends StateService
{
	public function getEncodedData($status)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$decodedArrayJson = json_decode($status,true);
		$decodedJson = $decodedArrayJson['clientData'];
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$clientId= $decodedJson[0]['client_id'];
		$clientName= $decodedJson[0]['client_name'];
		$companyName= $decodedJson[0]['company_name'];
		$contactNo= $decodedJson[0]['contact_no'];
		$contactNo1= $decodedJson[0]['contact_no1'];
		$emailId= $decodedJson[0]['email_id'];
		$gst= $decodedJson[0]['gst'];
		$address1= $decodedJson[0]['address1'];
		$professionId= $decodedJson[0]['profession_id'];
		$birthDate= $decodedJson[0]['birth_date'];
		$anniversaryDate= $decodedJson[0]['anniversary_date'];
		$otherDate= $decodedJson[0]['other_date'];
		$creditLimit= $decodedJson[0]['credit_limit'];
		$creditDays= $decodedJson[0]['credit_days'];
		$isDisplay= $decodedJson[0]['is_display'];
		$stateAbb= $decodedJson[0]['state_abb'];
		$cityId= $decodedJson[0]['city_id'];
		// get the state details from database
		$encodeStateDataClass = new EncodeData();
		$stateStatus = $encodeStateDataClass->getStateData($stateAbb);
		$stateDecodedJson = json_decode($stateStatus,true);
		
		// get the city details from database
		$cityDetail = new CityDetail();
		$getCityDetail = $cityDetail->getCityDetail($cityId);
		
		//get all profession details from database 
		$professionService = new ProfessionService();
		$professionDetail = $professionService->getProfessionData($professionId);
		// date format conversion
		$client = new Client();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$client->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $client->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$client->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $client->getUpdated_at();
		}
		$birthDate = $birthDate=='0000-00-00' ? "00-00-0000"
		 : Carbon\Carbon::createFromFormat('Y-m-d', $birthDate)->format('d-m-Y');
		$anniversaryDate = $anniversaryDate=='0000-00-00' ? "00-00-0000"
		 : Carbon\Carbon::createFromFormat('Y-m-d', $anniversaryDate)->format('d-m-Y');
		$otherDate = $otherDate=='0000-00-00' ? "00-00-0000"
		 : Carbon\Carbon::createFromFormat('Y-m-d', $otherDate)->format('d-m-Y');
		// set all data into json array
		$data = array();
		$data['clientId'] = $clientId;
		$data['clientName'] = $clientName;
		$data['companyName'] = $companyName;
		$data['contactNo'] = $contactNo;
		$data['contactNo1'] = $contactNo1;
		$data['emailId'] = $emailId;
		$data['gst'] = $gst;
		$data['address1'] = $address1;
		$data['isDisplay'] = $isDisplay;
		$data['professionId'] = $professionId;
		$data['birthDate'] = $birthDate;
		$data['anniversaryDate'] = $anniversaryDate;
		$data['otherDate'] = $otherDate;
		$data['creditLimit'] = $creditLimit;
		$data['creditDays'] = $creditDays;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['state']= array(
			'stateAbb' => $stateDecodedJson['stateAbb'],
			'stateName' => $stateDecodedJson['stateName'],
			'isDisplay' => $stateDecodedJson['isDisplay'],	
			'createdAt' => $stateDecodedJson['createdAt'],	
			'updatedAt' => $stateDecodedJson['updatedAt']
		);
		if(strcmp($exceptionArray['404'],$professionDetail)==0)
		{
			$data['profession']= array(
				'professionId' => '',
				'professionName' => '',
				'description' => '',	
				'professionParentId' => '',	
				'createdAt' => '00-00-0000 00:00:00',	
				'updatedAt' => '00-00-0000 00:00:00'	
			);
		}
		else
		{
			$professionDecodedDtl = json_decode($professionDetail);
			$data['profession']= array(
				'professionId' => $professionDecodedDtl->professionId,
				'professionName' => $professionDecodedDtl->professionName,
				'description' => $professionDecodedDtl->description,	
				'professionParentId' => $professionDecodedDtl->professionParentId,
				'createdAt' => $professionDecodedDtl->createdAt,
				'updatedAt' => $professionDecodedDtl->updatedAt	
			);
		}
		$data['city']= array(
			'cityId' => $getCityDetail['cityId'],
			'cityName' => $getCityDetail['cityName'],	
			'isDisplay' => $getCityDetail['isDisplay'],	
			'createdAt' => $getCityDetail['createdAt'],	
			'updatedAt' => $getCityDetail['updatedAt'],	
			'stateAbb'=> $getCityDetail['state']['stateAbb']
		);
		$decodedDocumentJson = $decodedArrayJson['clientDocumentData'];
		$documentCount = count($decodedDocumentJson);
		if(count($decodedDocumentJson)==0)
		{
			$data['document']= array(
				'documentId' => '',
				'documentName' => '',
				'documentSize' => '',	
				'documentFormat' => '',	
				'documentType' => '',	
				'documentUrl' => '',	
				'createdAt' => '00-00-00 00:00:00',
				'updatedAt'=> '00-00-00 00:00:00',
				'clientId'=> ''
			);
		}
		else
		{
			$data['file'] = array();
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				if(strcmp($decodedDocumentJson[$documentArray]['updated_at'],'0000-00-00 00:00:00')==0)
				{
					$convertedUpdatedDate = "00-00-0000";
				}
				else
				{
					$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $decodedDocumentJson[$documentArray]['updated_at'])->format('d-m-Y');
				}
				$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $decodedDocumentJson[$documentArray]['created_at'])->format('d-m-Y');
				
				$data['file'][$documentArray]= array(
					'documentId' => $decodedDocumentJson[$documentArray]['document_id'],
					'documentName' => $decodedDocumentJson[$documentArray]['document_name'],
					'documentSize' => $decodedDocumentJson[$documentArray]['document_size'],	
					'documentFormat' => $decodedDocumentJson[$documentArray]['document_format'],	
					'documentType' => $decodedDocumentJson[$documentArray]['document_type'],	
					'documentUrl' => $constantArray['billDocumentUrl'],	
					'createdAt' => $convertedCreatedDate,
					'updatedAt'=> $convertedUpdatedDate,
					'clientId'=> $decodedDocumentJson[$documentArray]['client_id']
				);
			}
		}
		$encodeData = json_encode($data);
		return $encodeData;
	}
}