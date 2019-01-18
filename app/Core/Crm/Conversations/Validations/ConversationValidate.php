<?php
namespace ERP\Core\Crm\Conversations\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class ConversationValidate
{
	 /**
     * validate conversation data
     * $param Request object [Request $request]
     * @return error message/success message
     */
	public function validate($request)
	{
		$rules = array(
			// 'conversation'=>'between:1,200|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'subject'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'contact_no'=>'between:10,12|regex:/^[1-9][0-9]+$/',
			'email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'cc_email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'bcc_email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/'
		);
		$messages = [
			// 'conversation.between' => 'StringLengthException :Enter the :attribute less then 200 character',
			// 'conversation.regex' => 'conversation contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'subject.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'subject.regex' => 'subject contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 number',
			'contact_no.regex' => 'contact-no contains character from "0-9" only',
			'email_id.regex' => 'please enter your email-address in proper format',
			'cc_email_id.regex' => 'please enter your email-address in proper format',
			'bcc_email_id.regex' => 'please enter your email-address in proper format',
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
		else {
			return "Success";
		}
	}
}