<?php
namespace ERP\Core\States\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class StateValidate
{
	public function validate($request)
	{
		$rules = array(
			'state_name'=>"between:1,35|regex:/^[a-zA-Z &-]+$/",
		);
		$messages = [
			'state_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'state_name.regex' => 'state-name contains character from "a-zA-Z -&" only',
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
	public function validateUpdateData($keyName,$value,$request)
	{
		$validationArray = array('state_name'=>"between:1,35|regex:/^[a-zA-Z &-]+$/");
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
				'state_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
				'state_name.regex' => 'state-name contains character from "a-zA-Z -&" only',
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