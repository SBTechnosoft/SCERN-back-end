<?php
namespace ERP\Core\Authenticate\Entities;

use ERP\Core\Authenticate\Entities\Authenticate;
use ERP\Core\Users\Services\UserService;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends UserService
{
	//date conversion and merge with json data and returns json array
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$authenticate = new Authenticate();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$sessionId[$decodedData] = $decodedJson[$decodedData]['session_id'];
			$userId[$decodedData] = $decodedJson[$decodedData]['user_id'];
			$token[$decodedData] = $decodedJson[$decodedData]['token'];
			
			//get all user data
			$encodeData = new EncodeAllData();
			$userData[$decodedData] = $encodeData->getUserData($userId[$decodedData]);
			$userDecodedData[$decodedData] = json_decode($userData[$decodedData]);
			
			// date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y H:i:s');
			$authenticate->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $authenticate->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000 00:00:00";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y H:i:s');
				$authenticate->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $authenticate->getUpdated_at();
			}
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'sessionId' => $sessionId[$jsonData],
				'token' =>$token[$jsonData],
				'createdAt' =>$getCreatedDate[$jsonData],
				'updatedAt' =>$getUpdatedDate[$jsonData],
				'user' => array(
					'userId' => $userDecodedData[$jsonData]->userId,
					'userName' => $userDecodedData[$jsonData]->userName,
					'userType' => $userDecodedData[$jsonData]->userType,
					'contactNo' => $userDecodedData[$jsonData]->contactNo,
					'emailId' => $userDecodedData[$jsonData]->emailId,
					'address' => $userDecodedData[$jsonData]->address,
					'password' => $userDecodedData[$jsonData]->password,
					'pincode' => $userDecodedData[$jsonData]->pincode,
					'permissionArray' => $userDecodedData[$jsonData]->permissionArray,
					'defaultCompanyId' => $userDecodedData[$jsonData]->defaultCompanyId,
					'createdAt' => $userDecodedData[$jsonData]->createdAt,
					'updatedAt' => $userDecodedData[$jsonData]->updatedAt,
					'stateAbb' => $userDecodedData[$jsonData]->state->stateAbb,
					'cityId' => $userDecodedData[$jsonData]->city->cityId,
					'companyId' => $userDecodedData[$jsonData]->company->companyId,
					'branchId' => $userDecodedData[$jsonData]->branch->branchId
				)
			);	
		}
		return json_encode($data);
	}
}