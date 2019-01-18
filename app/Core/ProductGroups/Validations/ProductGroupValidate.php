<?php
namespace ERP\Core\ProductGroups\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class ProductGroupValidate
{
	public function validate($request)
	{
		$rules = array(
			'product_group_name'=> 'required|between:1,35|regex:/^[a-zA-Z0-9 &,\/_`#().\'-]+$/', 
			'product_group_description'=> 'regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/'
		);
		$messages = [
			'product_group_name.between' => 'StringLengthException :Enter the product group name less then 35 character',
			'product_group_name.required' => 'product group name is required',
			'product_group_name.regex' => 'product-group-name contains character from "a-zA-Z0-9 &,\/_`#().\'-" only',
			'product_group_description.regex' => 'product-group-description contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only'
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
		$validationArray = array(
			'product_group_name'=> 'required|between:1,35|regex:/^[a-zA-Z0-9 &,\/_`#().\'-]+$/', 
			'product_group_description'=> 'regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/' 
		);
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
				'product_group_name.between' => 'StringLengthException :Enter the product group name less then 35 character',
				'product_group_name.required' => 'product group name is required',
				'product_group_name.regex' => 'product-group-name contains character from "a-zA-Z0-9 &,\/_`#().\'-" only',
				'product_group_description.regex' => 'product-group-description contains character from "a-zA-Z0-9 *,-\/_`#\[\]().\'" only'
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