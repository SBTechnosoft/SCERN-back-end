<?php
namespace ERP\Core\Authenticate\Entities;

use ERP\Core\Authenticate\Entities\Authenticate;
use ERP\Core\Users\Services\UserService;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends UserService
{
	//date conversion and merge with json data and returns json array
    public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
			
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$sessionId= $decodedJson[0]['session_id'];
		$userId= $decodedJson[0]['user_id'];
		$token= $decodedJson[0]['token'];
		
		//get constant document-url from document
		$documentUrl =  new ConstantClass();
		$documentArray = $documentUrl->constantVariable();
		//get user data
		$encodeData = new EncodeData();
		$userData = $encodeData->getUserData($userId);
		$userDecodedData = json_decode($userData);
			
		//date format conversion
		$authenticate = new Authenticate();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y H:i:s');
		$authenticate->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $authenticate->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000 00:00:00";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y H:i:s');
			$authenticate->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $authenticate->getUpdated_at();
		}
		
		//set all data into json array
		$data = array();
		$data['sessionId'] = $sessionId;
		$data['token'] = $token;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;
		$data['user'] = array(
			'userId' => $userDecodedData->userId,
			'userName' => $userDecodedData->userName,	
			'userType' => $userDecodedData->userType,	
			'contactNo' => $userDecodedData->contactNo,	
			'emailId' => $userDecodedData->emailId,	
			'address' => $userDecodedData->address,
			'password' => $userDecodedData->password,
			'pincode' => $userDecodedData->pincode,
			'permissionArray' => $userDecodedData->permissionArray,
			'defaultCompanyId' => $userDecodedData->defaultCompanyId,
			'createdAt' => $userDecodedData->createdAt,
			'updatedAt' => $userDecodedData->updatedAt,
			'stateAbb' => $userDecodedData->state->stateAbb,
			'cityId' => $userDecodedData->city->cityId,
			'companyId' => $userDecodedData->company->companyId,
			'branchId' => $userDecodedData->branch->branchId
		);
		$encodeData = $documentArray['prefixConstant'].json_encode($data);
		return $encodeData;
	}
}