<?php
namespace ERP\Core\Settings\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class SettingValidate
{
	public function validate($request)
	{
		$rules = array(
			'barcode_width'=> 'regex:/^[0-9 .]+$/',
			'barcode_height'=> 'regex:/^[0-9 .]+$/',
			'user_id'=> 'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
		);
		$messages = [
			'barcode_width.regex' => 'Enter the width contains character from "0-9 ." character',
			'barcode_height.regex' => 'Enter the height contains character from "0-9 ." character',
			'user_id' => 'Enter proper Email ID'
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
			'barcode_width'=> 'regex:/^[0-9 .]+$/',
			'barcode_height'=> 'regex:/^[0-9 .]+$/',);
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
				'barcode_width.regex' => 'Enter the width contains character from "0-9 ." character',
				'barcode_height.regex' => 'Enter the height contains character from "0-9 ." character',
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