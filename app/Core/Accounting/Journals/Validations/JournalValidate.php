<?php
namespace ERP\Core\Accounting\Journals\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class JournalValidate
{
	/**
	 * validate the specified resource for insertion of data
	 * @param  Request object[Request $request]
	 * @return error-message/array
	*/
	public function validate($request)
	{
		$rules = array(
			'jfId'=> 'regex:/^[0-9]+$/'
		);
		$messages = [
			'jfId.regex' => 'journal folio id contains character from "0-9" only'
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
			//validate an array
			for($arrayData=0;$arrayData<count($request[0]);$arrayData++)
			{
				$rules = array(
				'amount'=> 'regex:/^[0-9 .]+$/'
				);
				$messages = [
					'amount.regex' => 'amount contains character from "0-9 ." only'
				];
				$validator = Validator::make($request[0][$arrayData],$rules,$messages);
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
			}
			return "Success";
			
		}
	}
	
	/**
	 * validate the specified resource for udpate of data(array-data)
	 * @param  Request object[Request $request]
	 * @return error-message/success message
	*/
	public function validateArrayData($request)
	{
		$requestData = $request;
		if(array_key_exists("flag",$requestData))
		{
			if(empty($requestData[0]))
			{
				return "Success";
			}
			$requestData = $requestData[0];
		}
		//validate an array
		for($arrayData=0;$arrayData<count($requestData);$arrayData++)
		{
			$rules = array(
				'amount'=> 'regex:/^[0-9 .]+$/'
			);
			$messages = [
				'amount.regex' => 'amount contains character from "0-9 ." only'
			];
			$validator = Validator::make($requestData[$arrayData],$rules,$messages);
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
		}
		return "Success";
	}
	
	/**
	 * validate the specified resource for update data
	 * @param  Request object[Request $request]
	 * @return error-message/array
	*/
	public function validateUpdateData($keyName,$value,$request)
	{
		// echo "journal validate";
		$validationArray = array(
			'amount'=> 'regex:/^[0-9 .]*$/'
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
				'amount.regex' => 'amount contains character from "0-9 ." only'
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