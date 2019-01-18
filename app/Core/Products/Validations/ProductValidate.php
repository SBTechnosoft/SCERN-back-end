<?php
namespace ERP\Core\Products\Validations;

use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Products\ProductModel;
use ERP\Core\Settings\Services\SettingService;
/**
  * @author Reema Patel<reema.p@siliconbrain.in>
  */
class ProductValidate extends ProductModel
{
	/**
	 * validate data for insert
     * @param object
     * @return error-message/success
     */
	public function validate($request)
	{
		//  Get ERP Settings before validation
		$settingService= new SettingService();
		$settingData = $settingService->getData();
		$settingData = json_decode($settingData);
		$stCount = count($settingData);
		$stIndex = 0;
		$mrpValidate = '';
		while ($stIndex < $stCount) {
			$settingSingleData = $settingData[$stIndex];

			if($settingSingleData->settingType == 'product')
			{
				/* If MRP Required is enable then set validation Rule as Enable */
				if ($settingSingleData->productMrpRequireStatus == 'enable') {
					$mrpValidate = 'required|';
				}
				break;
			}
			$stIndex++;
		}
	/* End Setting */
		$rules = array(
			'product_name'=> 'required|between:1,100|regex:/^[a-zA-Z0-9 &,\/_`#().\'-]+$/', 
			'highest_purchase_price'=> 'regex:/^[0-9 .]+$/', 
			'higher_purchase_price'=> 'regex:/^[0-9 .]+$/', 
			'purchase_price'=> 'regex:/^[0-9 .]+$/', 
			'wholesale_margin'=> 'regex:/^[0-9 .]+$/', 
			'semi_wholesale_margin'=> 'regex:/^[0-9 .]+$/', 
			'margin'=> 'regex:/^[0-9 .]+$/', 
			'vat'=> 'regex:/^[0-9 .]+$/', 
			'mrp'=> $mrpValidate.'regex:/^[0-9 .]+$/', 
			'color'=> 'required', 
			'size'=> 'required', 
			'minimum_stock_level'=> 'regex:/^[0-9]+$/', 
		);
		$messages = [
			'product_name.between' => 'StringLengthException :Enter the product name less then 100 character',
			'product_name.regex' => 'product-name contains character from "a-zA-Z0-9 &,\/_`#().\'-" only',
			'highest_purchase_price.regex' => 'Highest-purchase-price contains character from "0-9" only',
			'higher_purchase_price.regex' => 'Higher-purchase-price contains character from "0-9" only',
			'purchase_price.regex' => 'purchase-price contains character from "0-9" only',
			'wholesale_margin.regex' => 'wholesale-margin contains character from "0-9" only',
			'semi_wholesale_margin.regex' => 'semi-wholesale-margin contains character from "0-9" only',
			'margin.regex' => 'margin contains character from "0-9" only',
			'vat.regex' => 'vat contains character from "0-9" only',
			'mrp.regex' => 'mrp contains character from "0-9" only',
			'minimum_stock_level.regex' => 'size contains character from "0-9" only',
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
	
	/**
	 * validate data for insert product_trn data
     * @param object
     * @return error-message/success
     */
	public function validateInOutward($request)
	{
		$rules = array(
			'tax'=> 'regex:/^[0-9 .]*$/' 
		);
		$messages = [
			'tax.regex' => 'tax contains character from "0-9 ." only'
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
			for($arrayData=0;$arrayData<count($request[0]);$arrayData++)
			{
				$rules = array(
					'discount'=> 'regex:/^[0-9 .]*$/', 
					'price'=> 'regex:/^[0-9 .]*$/', 
					'qty'=> 'regex:/^[0-9]*$/', 
				);
				$messages = [
					'discount.regex' => 'discount contains character from "0-9 ." only',
					'price.regex' => 'price contains character from "0-9 ." only',
					'qty.regex' => 'qty contains character from "0-9" only',
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
	 * validate data for update
     * @param key, value and object
     * @return error-message/success
     */
	public function validateUpdateData($keyName,$value,$request)
	{
		//  Get ERP Settings before validation
		$settingService= new SettingService();
		$settingData = $settingService->getData();
		$settingData = json_decode($settingData);
		$stCount = count($settingData);
		$stIndex = 0;
		$mrpValidate = '';
		while ($stIndex < $stCount) {
			$settingSingleData = $settingData[$stIndex];

			if($settingSingleData->settingType == 'product')
			{
				/* If MRP Required is enable then set validation Rule as Enable */
				if ($settingSingleData->productMrpRequireStatus == 'enable') {
					$mrpValidate = 'required|';
				}
				break;
			}
			$stIndex++;
		}
	/* End Setting */
		$validationArray = array(
				'product_name'=> 'required|between:1,100|regex:/^[a-zA-Z0-9 &,\/_`#().\'-]+$/', 
				'highest_purchase_price'=> 'regex:/^[0-9 .]+$/', 
				'higher_purchase_price'=> 'regex:/^[0-9 .]+$/', 
				'purchase_price'=> 'regex:/^[0-9 .]+$/', 
				'wholesale_margin'=> 'regex:/^[0-9 .]+$/', 
				'semi_wholesale_margin'=> 'regex:/^[0-9 .]+$/', 
				'margin'=> 'regex:/^[0-9 .]+$/', 
				'vat'=> 'regex:/^[0-9 .]+$/', 
				'mrp'=> $mrpValidate.'regex:/^[0-9 .]+$/', 
				'color'=> 'required', 
				'size'=> 'required', 
				'minimum_stock_level'=> 'regex:/^[0-9]+$/',
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
				'product_name.between' => 'StringLengthException :Enter the product name less then 100 character',
				'product_name.regex' => 'product-name contains character from "a-zA-Z0-9 &,\/_`#().\'-" only',
				'highest_purchase_price.regex' => 'Highest-purchase-price contains character from "0-9" only',
				'higher_purchase_price.regex' => 'Higher-purchase-price contains character from "0-9" only',
				'purchase_price.regex' => 'purchase-price contains character from "0-9" only',
				'wholesale_margin.regex' => 'wholesale-margin contains character from "0-9" only',
				'semi_wholesale_margin.regex' => 'semi-wholesale-margin contains character from "0-9" only',
				'margin.regex' => 'margin contains character from "0-9" only',
				'vat.regex' => 'vat contains character from "0-9" only',
				'mrp.regex' => 'mrp contains character from "0-9" only',
				'minimum_stock_level.regex' => 'size contains character from "0-9" only',
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
	 * validate data for update product_trn data
     * @param key,value and object
     * @return error-message/success
     */
	public function validateTransactionUpdateData($keyName,$value,$request)
	{
		$validationArray = array(
			'tax'=> 'regex:/^[0-9 .]*$/',
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
				'tax.regex' => 'tax contains character from "0-9 ." only',
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
	
	/**
	 * validate array-data for update product_trn data
     * @param trim request object
     * @return error-message/success
     */
	public function validateTransactionArrayUpdateData($tRequest)
	{
		for($arrayData=0;$arrayData<count($tRequest);$arrayData++)
		{
			$rules = array(
				'discount'=> 'regex:/^[0-9 .]*$/', 
				'price'=> 'regex:/^[0-9 .]*$/', 
				'qty'=> 'regex:/^[0-9]*$/', 
			);
			$messages = [
				'discount.regex' => 'discount contains character from "0-9 ." only',
				'price.regex' => 'price contains character from "0-9 ." only',
				'qty.regex' => 'qty contains character from "0-9" only',
			];
			
			$validator = Validator::make($tRequest[$arrayData],$rules,$messages);
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
		}
		return "Success";
	}
	
	/**
     * validate data for product name
     * $param trim request data
     * @return error messgage/trim request array
     */	
	// public function productNameValidate($tRequest)
	// {
		// get exception message
		// $exception = new ExceptionMessage();
		// $exceptionArray = $exception->messageArrays();
		
		// if (strpos($tRequest['product_name'], '\'') !== FALSE)
		// {
			// $tRequest['product_name']= str_replace("'","\'",$tRequest['product_name']);
		// }
		
		// get product-data
		// $productValidation = new ProductValidate();
		// $productResult = $productValidation->getProductName($tRequest['product_name'],$tRequest['company_id']);
		
		// if(!is_array($productResult))
		// {
			// return $tRequest;
		// }
		// else
		// {
			// return $exceptionArray['content'];
		// }
	// }
	
	/**
     * validate data for product name
     * $param trim request data
     * @return error messgage/trim request array
     */	
	public function productCodeValidate($companyId,$productCode)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get product-data
		$productValidation = new ProductValidate();
		$productResult = $productValidation->getProductCode($companyId,$productCode);
		
		if(!is_array($productResult))
		{
			return $exceptionArray['200'];
		}
		else
		{
			return $exceptionArray['content'];
		}
	}
	
	/**
     * validate update data for product name
     * $param trim request data
     * @return error messgage/trim request array
     */	
	// public function productNameValidateUpdate($tRequest,$productId)
	// {
		// get exception message
		// $exception = new ExceptionMessage();
		// $exceptionArray = $exception->messageArrays();
		
		// get product-data
		// $productValidation = new ProductValidate();
		// $productData = $productValidation->getData($productId);
		// $decodedProductdata = json_decode($productData);
		
		// if (strpos($tRequest['product_name'], '\'') !== FALSE)
		// {
			// $tRequest['product_name'] = str_replace("'","\'",$tRequest['product_name']);
		// }
		
		// $productResult = $productValidation->getProductName($tRequest['product_name'],$decodedProductdata[0]->company_id);
		// if(!is_array($productResult))
		// {
			// return $tRequest;
		// }
		// else
		// {
			// if($productResult[0]->product_id==$productId)
			// {
				// return $tRequest;
			// }
			// else
			// {
				// return $exceptionArray['content'];
			// }
		// }
	// }
	
	/**
     * validate update data for product code
     * $param trim request data
     * @return error messgage/trim request array
     */	
	public function productUpdateCodeValidate($companyId,$productCode,$productId)
	{
		$flag=0;
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productValidation = new ProductValidate();
		$productResult = $productValidation->getProductCode($companyId,$productCode);
		if(!is_array($productResult))
		{
			return $productResult;
		}
		else
		{
			for($arrayData=0;$arrayData<count($productResult);$arrayData++)
			{
				if($productResult[$arrayData]->product_id!=$productId)
				{
					$flag=1;
					break;
				}
			}
			if($flag==1)
			{
				return $exceptionArray['content'];
			}
			else
			{
				return $exceptionArray['200'];
			}
			
		}
	}
}