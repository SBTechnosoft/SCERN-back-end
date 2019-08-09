<?php
namespace ERP\Core\Accounting\SalesReturns\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Hiren Faldu<hiren.f@siliconbrain.in>
  */  
class SalesReturnValidate
{
	/**
	 * validate the specified resource for insertion of data
	 * @param  Request object[Request $request]
	 * @return error-message/array
	*/
	public function validate($request)
	{
		$rules = array(
			'company_name'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/', 
			'client_name'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/',
			'contact_no'=>'between:10,12|regex:/^[0-9]+$/'
		);
		$messages = [
			'company_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'company_name.regex' => 'company-name contains character from "a-zA-Z &_`#().\'-" only',
			'client_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'client_name.regex' => 'client-name contains character from "a-zA-Z &_`#().\'-" only',
			'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 character',
			'contact_no.regex' => 'contact-number contains character from "0-9" only'
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