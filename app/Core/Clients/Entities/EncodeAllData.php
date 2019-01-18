<?php
namespace ERP\Core\Clients\Entities;

use ERP\Core\Clients\Entities\Client;
use ERP\Core\States\Services\StateService;
use ERP\Core\Settings\Professions\Services\ProfessionService;
use ERP\Core\Entities\CityDetail;
use Carbon;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
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
		$decodedArrayJson = json_decode($status,true);
		$decodedJson = $decodedArrayJson['clientData'];
		$client = new Client();
		$professionDetail = array();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$clientId[$decodedData] = $decodedJson[$decodedData]['client_id'];
			$clientName[$decodedData] = $decodedJson[$decodedData]['client_name'];
			$companyName[$decodedData] = $decodedJson[$decodedData]['company_name'];
			$contactNo[$decodedData] = $decodedJson[$decodedData]['contact_no'];
			$contactNo1[$decodedData] = $decodedJson[$decodedData]['contact_no1'];
			$emailId[$decodedData] = $decodedJson[$decodedData]['email_id'];
			$address1[$decodedData] = $decodedJson[$decodedData]['address1'];
			$gst[$decodedData] = $decodedJson[$decodedData]['gst'];
			$creditLimit[$decodedData] = $decodedJson[$decodedData]['credit_limit'];
			$creditDays[$decodedData] = $decodedJson[$decodedData]['credit_days'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			$stateAbb[$decodedData] = $decodedJson[$decodedData]['state_abb'];
			$cityId[$decodedData] = $decodedJson[$decodedData]['city_id'];
			$professionIdArray[$decodedData] = $decodedJson[$decodedData]['profession_id'];
			
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
			 
			//get all profession details from database 
			$professionService = new ProfessionService();
			$professionDetail[$decodedData] = $professionService->getProfessionData($professionIdArray[$decodedData]);
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$client->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $client->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$client->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $client->getUpdated_at();
			}
			
			$birthDate[$decodedData]=$decodedJson[$decodedData]['birth_date']=='0000-00-00' ? "00-00-0000"
			 : Carbon\Carbon::createFromFormat('Y-m-d',$decodedJson[$decodedData]['birth_date'])->format('d-m-Y');
			$anniversaryDate[$decodedData]=$decodedJson[$decodedData]['anniversary_date']=="0000-00-00" ? "00-00-0000"
			 : Carbon\Carbon::createFromFormat('Y-m-d',$decodedJson[$decodedData]['anniversary_date'])->format('d-m-Y');
			$otherDate[$decodedData]=$decodedJson[$decodedData]['other_date']=='0000-00-00' ? "00-00-0000"
			 : Carbon\Carbon::createFromFormat('Y-m-d',$decodedJson[$decodedData]['other_date'])->format('d-m-Y');
		}
		$data = array();
		$professionDecodedDetail = array();
		$decodedDocumentJson = $decodedArrayJson['clientDocumentData'];
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			if(strcmp($professionDetail[$jsonData],$exceptionArray['404'])==0)
			{
				$professionId='';
				$professionName='';
				$description='';
				$professionParentId='';
				$createdAt='00-00-0000 00:00:00';
				$updatedAt='00-00-0000 00:00:00';
			}
			else
			{
				$professionDecodedDetail[$jsonData] = json_decode($professionDetail[$jsonData]);
				if($professionIdArray[$jsonData]=='' || $professionIdArray[$jsonData]==0 || $professionIdArray[$jsonData]==null)
				{
					$professionId='';
					$professionName='';
					$description='';
					$professionParentId='';
					$createdAt='00-00-0000 00:00:00';
					$updatedAt='00-00-0000 00:00:00';
				}
				else
				{
					$professionId=$professionIdArray[$jsonData];
					$professionName=$professionDecodedDetail[$jsonData]->professionName;
					$description=$professionDecodedDetail[$jsonData]->description;
					$professionParentId=$professionDecodedDetail[$jsonData]->professionParentId;
					$createdAt=$professionDecodedDetail[$jsonData]->createdAt;
					$updatedAt=$professionDecodedDetail[$jsonData]->updatedAt;
				}
			}
			$documentArrayData = array();
			if(count($decodedDocumentJson[$jsonData])==0)
			{
				$documentArrayData[$jsonData][0]['documentId']='';
				$documentArrayData[$jsonData][0]['documentName']='';
				$documentArrayData[$jsonData][0]['documentSize']='';
				$documentArrayData[$jsonData][0]['documentFormat']='';
				$documentArrayData[$jsonData][0]['documentType']='';
				$documentArrayData[$jsonData][0]['documentUrl']='';
				$documentArrayData[$jsonData][0]['clientId']='';
				$documentArrayData[$jsonData][0]['saleId']='';
				$documentArrayData[$jsonData][0]['createdAt']='00-00-0000 00:00:00';
				$documentArrayData[$jsonData][0]['updatedAt']='00-00-0000 00:00:00';
			}
			else
			{
				for($documentArray=0;$documentArray<count($decodedDocumentJson[$jsonData]);$documentArray++)
				{
					if(strcmp($decodedDocumentJson[$jsonData][$documentArray]['updated_at'],'0000-00-00 00:00:00')==0)
					{
						$convertedUpdatedDate[$documentArray] = '00-00-0000';
					}
					else
					{
						$convertedUpdatedDate[$documentArray] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$decodedDocumentJson[$jsonData][$documentArray]['updated_at'])->format('d-m-Y');
					}
					$convertedCreatedDate[$documentArray] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $decodedDocumentJson[$jsonData][$documentArray]['created_at'])->format('d-m-Y');
					$documentArrayData[$jsonData][$documentArray]['documentId'] = $decodedDocumentJson[$jsonData][$documentArray]['document_id'];
					$documentArrayData[$jsonData][$documentArray]['documentName'] = $decodedDocumentJson[$jsonData][$documentArray]['document_name'];
					$documentArrayData[$jsonData][$documentArray]['documentSize']=$decodedDocumentJson[$jsonData][$documentArray]['document_size'];
					$documentArrayData[$jsonData][$documentArray]['documentFormat']=$decodedDocumentJson[$jsonData][$documentArray]['document_format'];
					$documentArrayData[$jsonData][$documentArray]['documentType']=$decodedDocumentJson[$jsonData][$documentArray]['document_type'];
					$documentArrayData[$jsonData][$documentArray]['documentUrl']=$constantArray['billDocumentUrl'];
					$documentArrayData[$jsonData][$documentArray]['clientId']=$decodedDocumentJson[$jsonData][$documentArray]['client_id'];
					$documentArrayData[$jsonData][$documentArray]['saleId']=$decodedDocumentJson[$jsonData][$documentArray]['sale_id'];
					$documentArrayData[$jsonData][$documentArray]['createdAt']=$convertedCreatedDate[$documentArray];
					$documentArrayData[$jsonData][$documentArray]['updatedAt']=$convertedUpdatedDate[$documentArray];
				}
			}
			
			$data[$jsonData]= array(
				'clientId'=>$clientId[$jsonData],
				'clientName' => $clientName[$jsonData],
				'companyName' => $companyName[$jsonData],
				'contactNo' => $contactNo[$jsonData],
				'contactNo1' => $contactNo1[$jsonData],
				'gst' => $gst[$jsonData],
				'emailId' => $emailId[$jsonData],
				'address1' => $address1[$jsonData],
				'isDisplay' => $isDisplay[$jsonData],
				'birthDate' => $birthDate[$jsonData],
				'anniversaryDate' => $anniversaryDate[$jsonData],
				'otherDate' => $otherDate[$jsonData],
				'creditLimit' => $creditLimit[$jsonData],
				'creditDays' => $creditDays[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				
				'profession' => array(
					'professionId' => $professionId,
					'professionName' => $professionName,
					'description' => $description,
					'professionParentId' => $professionParentId,
					'createdAt' => $createdAt,
					'updatedAt' => $updatedAt
				),
				'state' => array(
					'stateAbb' => $stateAbb[$jsonData],
					'stateName' => $stateName[$jsonData],
					'isDisplay' => $stateIsDisplay[$jsonData],
					'createdAt' => $stateCreatedAt[$jsonData],
					'updatedAt' => $stateUpdatedAt[$jsonData]
				),
				
				'city' => $getCityDetail[$jsonData]
			);
			$data[$jsonData]['file'] = $documentArrayData[$jsonData];
		}
		ini_set('memory_limit', '256M');
		
		return json_encode($data);
	}
}