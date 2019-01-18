<?php
namespace ERP\Core\Accounting\Bills\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */  
class BillValidate
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
			// 'invoice_number'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/', 
			'address1'=>'between:1,200',
			// 'address2'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'contact_no'=>'between:10,12|regex:/^[0-9]+$/',
			'work_no'=>'between:10,12|regex:/^[0-9]+$/'
			// 'email_id'=>'regex:/^[_A-Za-z0-9-\\+]+(\\.[_A-Za-z0-9-]+)*@"
                // + "[A-Za-z0-9-]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})*$/',
			// 'total'=>'max:10|min:10|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
			// 'tax'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			// 'grand_total'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
			// 'advance'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
			// 'balance'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
			// 'bank_name'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
			// 'check_number'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
			// 'remark'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/'
		);
		$messages = [
			'company_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'company_name.regex' => 'company-name contains character from "a-zA-Z &_`#().\'-" only',
			'client_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'client_name.regex' => 'client-name contains character from "a-zA-Z &_`#().\'-" only',
			'address1.between' => 'StringLengthException :Enter the :attribute less then 200 character',
			// 'address1.regex' => 'address1 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			// 'address2.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			// 'address2.regex' => 'address2 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'contact_no.between' => 'StringLengthException :Enter the :attribute between 10-12 character',
			'contact_no.regex' => 'contact-number contains character from "0-9" only',
			'work_no.regex' => 'work-number contains character from "0-9" only'
			// 'email_id.regex' => 'email address is not correct',
			
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
	
	/**
	 * validate the specified resource for update data
	 * @param  Request object[Request $request]
	 * @return error-message/array
	*/
	public function billUpdateValidate()
	{
		
	}
}