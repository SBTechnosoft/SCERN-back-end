<?php
namespace ERP\Core\Companies\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use ERP\Model\Companies\CompanyModel;
use ERP\Exceptions\ExceptionMessage;
/**
  * @author Reema Patel<reema.p@siliconbrain.in> 
  */
class CompanyValidate extends CompanyModel
{
	/**
     * validate data for insertion
     * $param trim request data
     * @return error messgage/trim request array
     */	
	public function validate($request)
	{
		$rules = array(
			'company_name'=> 'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/', 
			'company_display_name'=>'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'address1'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'address2'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'customer_care'=> 'between:10,12|regex:/^[1-9][0-9]+$/', 
			'email_id'=> 'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'pincode'=>'between:6,10|regex:/^[0-9]+$/',
			'pan'=>'max:10|min:10|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
			'tin'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			'vat_no'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			'service_tax_no'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/',
			'basic_currency_symbol'=>'max:3|min:3',
			'formal_name'=>'between:1,35|regex:/^[a-zA-Z &_`#().\'-]+$/',
			'no_of_decimal_points'=>'regex:/^[0-9]+$/'
		);
		$messages = [
			'company_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'company_name.regex' => 'company-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
			'company_display_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'company_display_name.regex' => 'company-display-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
			'address1.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'address1.regex' => 'address1 contains character from "a-zA-Z0-9* ,- /_`#[]().\" only',
			'address2.between' => 'StringLengthException :Enter the :attribute less then 50 character',
			'address2.regex' => 'address2 contains character from "a-zA-Z0-9* ,- /_`#[]().\" only',
			'customer_care.between' => 'StringLengthException :Enter the :attribute between 10-12 number',
			'customer_care.regex' => 'contact-no contains character from "0-9" only',
			'email_id.regex' => 'please enter your email-address in proper format',
			'pincode.between' => 'NumberFormatException :Enter the :attribute between 6 and 10 character',
			'pincode.regex' => 'pincode contains numbers only',
			'pan.max' => 'NumberFormatException :Enter the :attribute number of 10 character',
			'pan.min' => 'NumberFormatException :Enter the :attribute number of 10 character',
			'pan.regex' => 'pan number is wrong',
			'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
			'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
			'tin.regex' => 'tin number is wrong',
			'vat_no.regex' => 'vat number is wrong',
			'vat_no.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
			'vat_no.min' => 'NumberFormatException :Enter the :attribute number of 11 character',
			'service_tax_no.between' => 'NumberFormatException :Enter the:attribute less then 15 character',
			'service_tax_no.regex' => 'service tax number is wrong',
			'basic_currency_symbol.min' => 'StringLengthException :Enter the :attribute of 3 character',
			'basic_currency_symbol.max' => 'StringLengthException :Enter the :attribute of 3 character',
			'basic_currency_symbol.regex' => 'basic currency symbol contains character only',
			'formal_name.between' => 'StringLengthException :Enter the :attribute less the 35 character',
			'formal_name.regex' => 'formal-name contains character from "a-zA-Z &_`#().\'-" only',
			'no_of_decimal_points.regex' => 'decimal-points contains character from "0 to 9" only'
		];
		$validator = Validator::make($request,$rules,$messages);
		if ($validator->fails()) 
		{
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
	
	/**
     * validate data for update
     * $param trim request data
     * @return error messgage/trim request array
     */	
	public function validateUpdateData($keyName,$value,$request)
	{
		$validationArray = array(
			'company_name'=> 'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/', 
			'company_display_name'=>'between:2,50|regex:/^[a-zA-Z0-9 &_`#().\'-]+$/',
			'address1'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'address2'=>'between:1,50|regex:/^[a-zA-Z0-9 *,-\/_`#\[\]().\']+$/',
			'customer_care'=> 'between:10,12|regex:/^[1-9][0-9]+$/', 
			'email_id'=> 'regex:/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,})$/',
			'pincode'=>'between:6,10|regex:/^[0-9]+$/',
			'pan'=>'max:10|min:10|regex:/^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/',
			'tin'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			'vat_no'=>'max:11|min:11|regex:/^([a-zA-Z0-9])+$/',
			'service_tax_no'=>'between:1,35|regex:/^([a-zA-Z0-9]{15})+$/',
			'basic_currency_symbol'=>'max:3|min:3',
			'formal_name'=>'between:1,35|regex:/^[a-zA-Z &_`#().\'-]+$/',
			'no_of_decimal_points'=>'regex:/^[0-9]+$/'
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
				$key=> $rules[$key]
			);
			$messages = [
				'company_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'company_name.regex' => 'company-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
				'company_display_name.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'company_display_name.regex' => 'company-display-name contains character from "a-zA-Z0-9 &_`#().\'-" only',
				'address1.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'address1.regex' => 'address1 contains character from "a-zA-Z0-9* ,- /_`#[]().\" only',
				'address2.between' => 'StringLengthException :Enter the :attribute less then 50 character',
				'address2.regex' => 'address2 contains character from "a-zA-Z0-9* ,- /_`#[]().\" only',
				'customer_care.between' => 'StringLengthException :Enter the :attribute between 10-12 number',
				'customer_care.regex' => 'contact-no contains character from "0-9" only',
				'email_id.regex' => 'please enter your email-address in proper format',
				'pincode.between' => 'NumberFormatException :Enter the :attribute between 6 and 10 character',
				'pincode.regex' => 'pincode contains numbers only',
				'pan.max' => 'NumberFormatException :Enter the :attribute number of 10 character',
				'pan.min' => 'NumberFormatException :Enter the :attribute number of 10 character',
				'pan.regex' => 'pan number is wrong',
				'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
				'tin.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
				'tin.regex' => 'tin number is wrong',
				'vat_no.regex' => 'vat number is wrong',
				'vat_no.max' => 'NumberFormatException :Enter the :attribute number of 11 character',
				'vat_no.min' => 'NumberFormatException :Enter the :attribute number of 11 character',
				'service_tax_no.between' => 'NumberFormatException :Enter the:attribute less then 15 character',
				'service_tax_no.regex' => 'service tax number is wrong',
				'basic_currency_symbol.min' => 'StringLengthException :Enter the :attribute of 3 character',
				'basic_currency_symbol.max' => 'StringLengthException :Enter the :attribute of 3 character',
				'basic_currency_symbol.regex' => 'basic currency symbol contains character only',
				'formal_name.between' => 'StringLengthException :Enter the :attribute less the 35 character',
				'formal_name.regex' => 'formal-name contains character from "a-zA-Z &_`#().\'-" only',
				'no_of_decimal_points.regex' => 'decimal-points contains character from "0 to 9" only'
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
	
	/**
     * validate data for company name
     * $param trim request data
     * @return error messgage/trim request array
     */	
	public function companyNameValidate($tRequest)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get all company-data
		$companyValidation = new CompanyValidate();
		$companyResult = $companyValidation->getCompanyName($tRequest['company_name']);
		
		if(!is_array($companyResult))
		{
			return $tRequest;
		}
		else
		{
			return $exceptionArray['content'];
		}
	}
	
	/**
     * validate data for company name
     * $param trim request data
     * @return error messgage/trim request array
     */	
	public function companyNameValidateUpdate($tRequest,$companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get all company-data
		$companyValidation = new CompanyValidate();
		$companyResult = $companyValidation->getCompanyName($tRequest['company_name']);
		if(!is_array($companyResult))
		{
			return $tRequest;
		}
		else
		{
			if($companyResult[0]->company_id==$companyId)
			{
				return $tRequest;
			}
			else
			{
				return $exceptionArray['content'];
			}
		}
	}
}