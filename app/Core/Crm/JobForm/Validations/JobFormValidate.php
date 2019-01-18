<?php
namespace ERP\Core\Crm\JobForm\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class JobFormValidate
{
	 /**
     * validate branch insertion data
     * $param Request object [Request $request]
     * @return error message/success message
     */
	public function validate($request)
	{
		$rules = array(
			'clientName'=> 'between:1,100|regex:/^[a-zA-Z &_`#().\'-]*$/', 
			'address'=>'between:1,200|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'contactNo'=>'between:10,12|regex:/^[1-9][0-9]+$/',
			'emailId'=>'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'productInformation'=>'regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'qty'=>'regex:/^[0-9]+$/',
			'tax'=>'regex:/^[0-9 .]+$/',
			'discount'=>'regex:/^[0-9 .]+$/',
			'additionalTax'=>'regex:/^[0-9 .]+$/',
			'price'=>'regex:/^[0-9 .]+$/',
			'labourCharge'=>'regex:/^[0-9 .]+$/',
			'advance'=>'regex:/^[0-9 .]+$/',
			'total'=>'regex:/^[0-9 .]+$/'
		);
		$messages = [
			'clientName.between' => 'StringLengthException :Enter the :attribute less then 100 character',
			'clientName.regex' => 'client-name contains character from "a-zA-Z &_`#().\'-" only',
			'address.between' => 'StringLengthException :Enter the :attribute less then 200 character',
			'address.regex' => 'address contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
			'contactNo.between' => 'StringLengthException :Enter the :attribute between 10-12 number',
			'contactNo.regex' => 'contact-no contains character from "0-9" only',
			'emailId.regex' => 'please enter your email-address in proper format',
			'productInformation.regex' => 'product-information contains character from "a-zA-Z0-9 &_`#().\'-" only',
			'qty.regex' => 'qty contains character from "0-9" only',
			'tax.regex' => 'tax contains character from "0-9 ." only',
			'discount.regex' => 'discount contains character from "0-9 ." only',
			'additionalTax.regex' => 'additional_tax contains character from "0-9 ." only',
			'price.regex' => 'price contains character from "0-9 ." only',
			'labourCharge.regex' => 'labour_charge contains character from "0-9 ." only',
			'advance.regex' => 'advance contains character from "0-9 ." only',
			'total.regex' => 'total contains character from "0-9 ." only'
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
     * validate branch data
     * $param Request object [Request $request]
     * @return error message/success message
     */
	public function validateUpdateData($keyName,$value,$request)
	{
		$validationArray = array('branch_name'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]+$/', 
		'address1'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
		'address2'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
		'pincode'=>'between:6,10|regex:/^[0-9]+$/');
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
				'branch_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
				'branch_name.regex' => 'branch-name contains character from "a-zA-Z &_`#().\'-" only',
				'address1.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'address1.regex' => 'address1 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
				'address2.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'address2.regex' => 'address2 contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only',
				'pincode.between' => 'NumberFormatException :Enter the :attribute between 6 and 10 character',
				'pincode.regex' => 'pincode contains numbers only',
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