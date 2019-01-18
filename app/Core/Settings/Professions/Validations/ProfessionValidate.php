<?php
namespace ERP\Core\Settings\Professions\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class ProfessionValidate
{
	public function validate($request)
	{
		$rules = array(
			'profession_name'=> 'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'description'=> 'between:0,100|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'profession_parent_id'=> 'regex:/^[0-9]+$/'
		);
		$messages = [
			'profession_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'profession_name.regex' => 'Template-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
			'description.between' => 'StringLengthException :Enter the :attribute less then 100 character',
			'description.regex' => 'description contains character from "a-zA-Z0-9 &_`#().\'-" only',
			'profession_parent_id.regex' => 'profession parent contains character from "0-9" only',
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
		$validationArray = array('profession_name'=> 'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
								'description'=> 'between:0,100|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
								'profession_parent_id'=> 'regex:/^[0-9]+$/');
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
				'profession_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'profession_name.regex' => 'Template-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
				'description.between' => 'StringLengthException :Enter the :attribute less then 100 character',
				'description.regex' => 'description contains character from "a-zA-Z0-9 &_`#().\'-" only',
				'profession_parent_id.regex' => 'profession parent contains character from "0-9" only',
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