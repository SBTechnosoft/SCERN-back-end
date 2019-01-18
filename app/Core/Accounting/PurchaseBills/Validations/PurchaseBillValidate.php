<?php
namespace ERP\Core\Accounting\PurchaseBills\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use ERP\Exceptions\ExceptionMessage;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */  
class PurchaseBillValidate
{
	/**
	 * validate the specified resource for insertion of data
	 * @param  Request object[Request $request]
	 * @return error-message/array
	*/
	public function validate($request)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$rules = array(
			'companyId'=> 'regex:/^[0-9]*$/', 
			'vendorId'=> 'regex:/^[0-9]*$/', 
			'billNumber'=> 'between:1,30', 
			'total'=>'regex:/^([0-9 .])+$/',
			'tax'=>'regex:/^([0-9 .])+$/',
			'grandTotal'=>'regex:/^([0-9 .])+$/',
			'extraCharge'=>'regex:/^([0-9 .])+$/',
			'totalDiscount'=>'regex:/^([0-9 .])+$/',
			'advance'=>'regex:/^([0-9 .])+$/',
			'balance'=>'regex:/^([0-9 .])+$/',
			'bankName'=>'between:1,60',
			'checkNumber'=>'between:1,20',
			'remark'=>'between:1,100'
		);
		$messages = [
			'companyId.regex' => 'company-id contains character from "0-9" only',
			'vendorId.regex' => 'vendor-id contains character from "0-9" only',
			'billNumber.between' => 'StringLengthException :Enter the :attribute less then 30 character',
			'total.regex' => 'total contains character from "0-9 ." only',
			'tax.regex' => 'tax contains character from "0-9 ." only',
			'grandTotal.regex' => 'grandTotal contains character from "0-9 ." only',
			'extraCharge.regex' => 'extraCharge contains character from "0-9 ." only',
			'totalDiscount.regex' => 'totalDiscount contains character from "0-9 ." only',
			'advance.regex' => 'advance contains character from "0-9 ." only',
			'balance.regex' => 'balance contains character from "0-9 ." only',
			'bankName.between' => 'StringLengthException :Enter the :attribute less then 60 character',
			'checkNumber.between' => 'StringLengthException :Enter the :attribute less then 20 character',
			'remark.between' => 'StringLengthException :Enter the :attribute less then 100 character',
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
			return $exceptionArray['200'];
		}
	}
}