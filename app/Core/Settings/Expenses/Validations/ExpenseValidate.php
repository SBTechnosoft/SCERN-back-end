<?php
namespace ERP\Core\Settings\Expenses\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class ExpenseValidate
{
	public function validate($request)
	{
		$rules = array(
			'expense_name'=> 'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'expense_value'=> 'regex:/^[0-9 .]+$/'
		);
		$messages = [
			'expense_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'expense_name.regex' => 'Template-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
			'expense_value.regex' => 'expense value contains character from "0-9 ." only'
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
		$validationArray = array('expense_name'=> 'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'expense_value'=> 'regex:/^[0-9 .]+$/');
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
				'expense_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'expense_name.regex' => 'Template-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
				'expense_value.regex' => 'expense value contains character from "0-9 ." only'
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