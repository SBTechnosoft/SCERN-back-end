<?php
namespace ERP\Core\Users\Validations;

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
class UserValidate
{
	/**
     * @param Request $request
     * @return success/error-message
     */
	public function validate($request)
	{
		$rules = array(
			'user_name'=>"between:1,35|regex:/^[a-zA-Z0-9 &_`#.\'-]*$/",
			'address'=>'between:1,100|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'contact_no'=>'required|between:10,12|regex:/^[0-9]+$/',
			'pincode'=>'between:6,10|regex:/^[0-9]+$/'
		);
		$messages = [
			'user_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'user_name.regex' => 'user-name contains character from "a-zA-Z0-9 &_`#.\'-" only',
			'address.between' => 'StringLengthException :Enter the :attribute less then 100 character',
			'address.regex' => 'address contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'email_id.regex' => 'please enter your email-address in proper format',
			'pincode.between' => 'NumberFormatException :Enter the :attribute between 6 and 10 character',
			'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 character',
			'contact_no.regex' => 'contact-number contains character from "0-9" only',
			'pincode.regex' => 'pincode contains numbers only'
		];
		
		$validator = Validator::make($request,$rules,$messages);
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
		else 
		{
			return "Success";
		}
	}
	
	/**
     * @param array of trimRequest
     * @return trimRequest/error-message
     */
	public function emailIdCheck($trimRequest,$request)
	{
		$emailFlag=0;
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		
		//get all user data
		$userService = new UserService();
		$userData = $userService->getAllUserData($request);
		$decodedUserData = json_decode($userData);
		for($arrayData=0;$arrayData<count($decodedUserData);$arrayData++)
		{
			if(strcmp($decodedUserData[$arrayData]->emailId,$trimRequest['email_id'])==0)
			{
				$emailFlag=1;
				break;
			}
		}
		if($emailFlag==1)
		{
			return $msgArray['content'];
		}
		return $trimRequest;
	}
	
	/**
     * @param Request $request
     * @return success/error-message
     */
	public function validateUpdateData($keyName,$value,$request)
	{
		$validationArray = array(
			'user_name'=>"between:1,35|regex:/^[a-zA-Z0-9 &_`#.\'-]*$/",
			'address'=>'between:1,100|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'contact_no'=>'required|between:10,12|regex:/^[0-9]+$/',
			'pincode'=>'between:6,10|regex:/^[0-9]+$/'
		);
		$rules = array();
		foreach ($validationArray as $key => $value) 
		{
			if($key == $keyName)
			{
				$rules[$key]=$value;
				break;
			}
		}
		if(!empty($rules))
		{
			$rules = array(
				$key=> $rules[$key]
			);
			$messages = [
				'user_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'user_name.regex' => 'user-name contains character from "a-zA-Z0-9 &_`#.\'-" only',
			'address.between' => 'StringLengthException :Enter the :attribute less then 100 character',
			'address.regex' => 'address contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'email_id.regex' => 'please enter your email-address in proper format',
			'pincode.between' => 'NumberFormatException :Enter the :attribute between 6 and 10 character',
			'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 character',
			'contact_no.regex' => 'contact-number contains character from "0-9" only',
			'pincode.regex' => 'pincode contains numbers only'
			];
			$validator = Validator::make($request,$rules,$messages);
			
			if ($validator->fails()) 
			{
				$errors = $validator->errors()->toArray();
				$validate = array();
				for($data=0;$data<count($errors);$data++)
				{
					$detail[$data] = $errors[array_keys($errors)[$data]];
					$key[$data]=array_keys($errors)[$data];
					$validate[$data]= array($key=>$detail[$data][0]);
				}
				return $validate;
			}
			else {
				return "Success";
			}
		}
		else
		{
			return "Success";
		}
	}
}