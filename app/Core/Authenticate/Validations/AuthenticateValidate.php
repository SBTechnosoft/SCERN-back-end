<?php
namespace ERP\Core\Authenticate\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use ERP\Core\Users\Services\UserService;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Http\Request;
use ERP\Http\Requests;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class AuthenticateValidate extends UserService
{
	public function insertValidate(Request $request ,$tRequest)
	{
		$matchFlag=0;
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$rules = array(
			'email_id'=> 'between:1,200|regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/'
		);
		$messages = [
			'email_id.regex' => 'email address is not correct',
		];
		$validator = Validator::make($tRequest,$rules,$messages);
		if ($validator->fails()) {
			$errors = $validator->errors()->toArray();
			$validate = array();
			for($data=0;$data<count($errors);$data++)
			{
				$detail[$data] = $errors[array_keys($errors)[$data]];
				$key[$data] = array_keys($errors)[$data];
				$validate[$data]= array($key[$data]=>$detail[$data][0]);
			}
			return json_encode($validate);
		}

		//convert password into base64_encode
		$decodedPassword = base64_decode($tRequest['password']);
		//get user data
		$userService = new AuthenticateValidate();
		$getAllData = $userService->getEmailData($request,$tRequest['email_id']);
		$decodedUserData = json_decode($getAllData);
		for($arrayData=0;$arrayData<count($decodedUserData);$arrayData++)
		{
			if(strcmp($decodedUserData[$arrayData]->emailId,$tRequest['email_id'])==0 && strcmp($decodedUserData[$arrayData]->password,$decodedPassword)==0)
			{
				$matchFlag=1;
				$userId = $decodedUserData[$arrayData]->userId;
				$emailId = $decodedUserData[$arrayData]->emailId;
				$password = $decodedUserData[$arrayData]->password;
				$createdAt = $decodedUserData[$arrayData]->createdAt;
				break;
			}
		}
		if($matchFlag==0)
		{
			return $msgArray['content'];
		}
		else
		{
			$requestArray = array();
			$requestArray['userId'] = $userId;
			$requestArray['emailId'] = $emailId;
			$requestArray['password'] = $password;
			$requestArray['createdAt'] = $createdAt;
			return $requestArray;
		}	
	}
}