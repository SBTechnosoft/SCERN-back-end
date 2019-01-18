<?php
namespace ERP\Core\Users\Commissions\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use ERP\Core\Users\Commissions\Services\CommissionService;
use ERP\Exceptions\ExceptionMessage;
use Illuminate\Http\Request;
use ERP\Http\Requests;
/**
  * @author Hiren Faldu<hiren.f@siliconbrain.in>
  */
class CommissionValidate
{
	public function validate($request)
	{
		if ($request['commission_status'] == 'on') {
			$rules = array(
					'user_id' => 'required|regex:/^[0-9 .]+$/',
					'commission_rate' => 'regex:/^[0-9 .]+$/',
					'commission_type' => 'required',
					'commission_rate_type' => 'required',
					'commission_calc_on' => 'required',
					'commission_for' => 'required',
				);
			$messages = [
				'user_id.regex' => 'Invalid User Id is given',
				'commission_rate.regex' => 'Commission rate contains character from "0-9" only',
			];
		}else{
			$rules = array(
					'user_id' => 'required|regex:/^[0-9 .]+$/',
				);
			$messages = [
				'user_id.regex' => 'Invalid User Id is given',
			];
		}
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
}