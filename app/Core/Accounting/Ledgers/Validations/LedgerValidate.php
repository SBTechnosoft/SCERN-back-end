<?php
namespace ERP\Core\Accounting\Ledgers\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */  
class LedgerValidate
{
	public function validate($request)
	{
		$rules = array(
			'ledger_name'=> 'between:1,100|regex:/^[a-zA-Z0-9 &_`#().\'-\/]*$/', 
			'alias'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/', 
			// 'address1'=>'between:1,200|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			// 'address2'=>'between:1,200|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'address1'=>'between:1,200',
			'address2'=>'between:1,200',
			'contact_no'=>'between:10,12|regex:/^[0-9]+$/',
			'email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'pan'=>'max:10|min:10|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
			'tin'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			'gst'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
		);
		$messages = [
			'ledger_name.between' => 'StringLengthException :Enter the :attribute less then 100 character',
			'ledger_name.regex' => 'ledger-name contains character from "a-zA-Z &_`#().\'-" only',
			'alias.regex' => 'alias contains character from "a-zA-Z &_`#().\'-" only',
			'address1.between' => 'StringLengthException :Enter the :attribute less then 200 character',
			'address1.regex' => 'address1 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'address2.between' => 'StringLengthException :Enter the :attribute less then 200 character',
			'address2.regex' => 'address2 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 character',
			'contact_no.regex' => 'contact-number contains character from "0-9" only',
			// 'contact_no.required' => 'contact-number is required',
			'email_id.regex' => 'please enter your email-address in proper format',
			'pan.max' => 'NumberFormatException :Enter the :attribute number of 10 character',
			'pan.min' => 'NumberFormatException :Enter the :attribute number of 10 character',
			'pan.regex' => 'pan number is wrong',
			'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
			'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
			'tin.regex' => 'tin number is wrong',
			'gst.between' => 'NumberFormatException :Enter the:attribute less then 15 character',
			'gst.regex' => 'service tax number is wrong'
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
	public function validateUpdateData($keyName,$value,$request)
	{
		$validationArray = array(
			'ledger_name'=> 'between:1,100|regex:/^[a-zA-Z0-9 &_`#().\'-\/]*$/', 
			'alias'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/', 
			// 'address1'=>'between:1,200|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			// 'address2'=>'between:1,200|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'address1'=>'between:1,200',
			'address2'=>'between:1,200',
			'contact_no'=>'between:10,12|regex:/^[0-9]+$/',
			'email_id'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'pan'=>'max:10|min:10|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
			'tin'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			'gst'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
		);
		$rules =array();
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
				$key=> $rules[$key],
			);
			$messages = [
				'ledger_name.between' => 'StringLengthException :Enter the :attribute less then 100 character',
				'ledger_name.regex' => 'ledger-name contains character from "a-zA-Z &_`#().\'-" only',
				'alias.regex' => 'alias contains character from "a-zA-Z &_`#().\'-" only',
				'address1.between' => 'StringLengthException :Enter the :attribute less then 200 character',
				'address1.regex' => 'address1 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
				'address2.between' => 'StringLengthException :Enter the :attribute less then 200 character',
				'address2.regex' => 'address2 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
				'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 character',
				'contact_no.regex' => 'contact-number contains character from "0-9" only',
				// 'contact_no.required' => 'contact-number is required',
				'email_id.regex' => 'please enter your email-address in proper format',
				'pan.max' => 'NumberFormatException :Enter the :attribute number of 10 character',
				'pan.min' => 'NumberFormatException :Enter the :attribute number of 10 character',
				'pan.regex' => 'pan number is wrong',
				'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
				'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
				'tin.regex' => 'tin number is wrong',
				'gst.between' => 'NumberFormatException :Enter the:attribute less then 15 character',
				'gst.regex' => 'service tax number is wrong'
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
			else 
			{
				return "Success";
			}
		}
		else
		{
			return "Success";
		}
	}
}