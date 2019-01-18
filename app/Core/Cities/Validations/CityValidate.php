<?php
namespace ERP\Core\Cities\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class CityValidate
{
	public function validate($request)
	{
		$rules = array(
			'city_name'=>"between:1,35|regex:/^[a-zA-Z &-]+$/",
        );
		$messages = [
			'city_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'city_name.regex' => 'city-name contains character from "a-zA-Z &-" only',
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
		$validationArray = array('city_name'=>"between:1,35|regex:/^[a-zA-Z &-]+$/");
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
				$key=> $rules[$key],
			);
			$messages = [
				'city_name.between' => 'StringLengthException :Enter the :attribute less then 35 character',
				'city_name.regex' => 'city-name contains character from "a-zA-Z &-" only',
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