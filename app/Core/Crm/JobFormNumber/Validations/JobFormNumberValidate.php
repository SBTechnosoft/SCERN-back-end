<?php
namespace ERP\Core\Crm\JobFormNumber\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class JobFormNumberValidate
{
	public function validate($request)
	{
		$rules = array(
			'job_form_number_label'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-\/]*$/', 
			'start_at'=> 'regex:/^[0-9]*$/', 
			'end_at'=> 'regex:/^[0-9]*$/', 
		);
		$messages = [
			'job_form_number_label.between' => 'StringLengthException :Enter the :attribute less then 35 character',
			'start_at.between' => 'start_at contains character from "0-9" only',
			'end_at.between' => 'end_at contains character from "0-9" only',
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
			'job_form_number_label'=> 'between:1,35|regex:/^[a-zA-Z &_`#().\'-\/]*$/', 
			'start_at'=> 'regex:/^[0-9]*$/', 
			'end_at'=> 'regex:/^[0-9]*$/', 
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
				'job_form_number_label.between' => 'StringLengthException :Enter the :attribute less then 35 character',
				'start_at.between' => 'start_at contains character from "0-9" only',
				'end_at.between' => 'end_at contains character from "0-9" only',
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