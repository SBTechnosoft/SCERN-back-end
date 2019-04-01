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
		$data = array();
		$userRecords = array();
		$encodeData = new EncodeAllData();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$sessionId[$decodedData] = $decodedJson[$decodedData]['session_id'];
			$userId[$decodedData] = $decodedJson[$decodedData]['user_id'];
			$token[$decodedData] = $decodedJson[$decodedData]['token'];
			
			//get all user data
			
			$userData[$decodedData] = $encodeData->getUserData($userId[$decodedData]);
			if (!isset($userRecords[$userId[$decodedData]])) {
				$userRecords[$userId[$decodedData]] = $encodeData->getUserData($userId[$decodedData]);
			}
			$userData[$decodedData] = $userRecords[$userId[$decodedData]];
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
			$data[$decodedData]= array(
				'sessionId' => $sessionId[$decodedData],
				'token' =>$token[$decodedData],
				'createdAt' =>$getCreatedDate[$decodedData],
				'updatedAt' =>$getUpdatedDate[$decodedData],
				'user' => array(
					'userId' => $userDecodedData[$decodedData]->userId,
					'userName' => $userDecodedData[$decodedData]->userName,
					'userType' => $userDecodedData[$decodedData]->userType,
					'contactNo' => $userDecodedData[$decodedData]->contactNo,
					'emailId' => $userDecodedData[$decodedData]->emailId,
					'address' => $userDecodedData[$decodedData]->address,
					'password' => $userDecodedData[$decodedData]->password,
					'pincode' => $userDecodedData[$decodedData]->pincode,
					'permissionArray' => $userDecodedData[$decodedData]->permissionArray,
					'defaultCompanyId' => $userDecodedData[$decodedData]->defaultCompanyId,
					'createdAt' => $userDecodedData[$decodedData]->createdAt,
					'updatedAt' => $userDecodedData[$decodedData]->updatedAt,
					'stateAbb' => $userDecodedData[$decodedData]->state->stateAbb,
					'cityId' => $userDecodedData[$decodedData]->city->cityId,
					'companyId' => $userDecodedData[$decodedData]->company->companyId,
					'branchId' => $userDecodedData[$decodedData]->branch->branchId
				)
			);
		}
		return json_encode($data);
	}
}