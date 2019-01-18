<?php
namespace ERP\Core\Branches\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class BranchValidate
{
	 /**
     * validate branch insertion data
     * $param Request object [Request $request]
     * @return error message/success message
     */
	public function validate($request)
	{
		$rules = array(
			'branch_name'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-]*$/', 
			'address1'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'address2'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'pincode'=>'between:6,10|regex:/^[0-9]+$/',
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