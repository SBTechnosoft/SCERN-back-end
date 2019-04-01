<?php
namespace ERP\Api\V1_0\Products\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Products\Persistables\ProductPersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Products\Validations\ProductValidate;
use ERP\Api\V1_0\Products\Transformers\ProductTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Accounting\Journals\Validations\BuisnessLogic;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Companies\CompanyModel;
use ERP\Model\ProductGroups\ProductGroupModel;
use ERP\Model\ProductCategories\ProductCategoryModel;
use Carbon;
use Illuminate\Container\Container;
use ERP\Api\V1_0\Documents\Controllers\DocumentController;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductProcessor extends BaseProcessor
{
	/**
     * @var productPersistable
	 * @var request
     */
	private $productPersistable;
	private $request;    
	
    /**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return product Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;	
		$productValue = array();
		$tKeyValue = array();
		$keyName = array();
		$value = array();
		$data=0;
		$codeFlag=0;
		$docFlag=0;
		$documentName="";
		$documentUrl="";
		$documentFormat="";
		$documentSize="";
		
		// get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		$file = $request->file();
		if(count($_POST)==0)
		{
			return $msgArray['204'];
		}
		else
		{
			if(in_array(true,$file))
			{
				$constantClass = new ConstantClass();
				$constantArray = $constantClass->constantVariable();
				$documentController = new DocumentController(new Container());
				$processedData = $documentController->insertUpdate($request,$constantArray['productDocumentUrl']);
				if(is_array($processedData))
				{
					$docFlag=1;
				}
				else
				{
					return $processedData;
				}
			}
			$productValidate = new ProductValidate();
			
			// trim an input 
			$productTransformer = new ProductTransformer();
			$tRequest = $productTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}
			else
			{
				if($tRequest['color']=="")
				{
					$tRequest['color'] = "XX";
				}
				if($tRequest['size']=="")
				{
					$tRequest['size'] = "ZZ";
				}
				if($tRequest['variant']=="")
				{
					$tRequest['variant'] = "YY";
				}
				if ($tRequest['product_code'] == '' || $tRequest['product_code'] == null) {
					//make a product_code and validate it with other codes
					//get company_name 
					$companyModel = new CompanyModel();
					$companyResult = $companyModel->getData($tRequest['company_id']);
					$decodedCompanyData = json_decode($companyResult);
					
					//get product group name
					$productGroupData = new ProductGroupModel();
					$groupData = $productGroupData->getData($tRequest['product_group_id']);
					$decodedGroupData = json_decode($groupData);
					
					//get product category name
					$productCategoryData = new ProductCategoryModel();
					$categoryData = $productCategoryData->getData($tRequest['product_category_id']);
					$decodedCategoryData = json_decode($categoryData);
					$color = preg_replace('/[^A-Za-z0-9\-]/', '', $tRequest['color']);
					$size = preg_replace('/[^A-Za-z0-9\-]/', '', $tRequest['size']);
					$variant = preg_replace('/[^A-Za-z0-9\-]/', '', $tRequest['variant']);
					$product_name = preg_replace('/[^A-Za-z0-9]/', '', $tRequest['product_name']);
					$company_name = preg_replace('/[^A-Za-z0-9]/', '', $decodedCompanyData[0]->company_name);
					$group_name = preg_replace('/[^A-Za-z0-9]/', '', $decodedGroupData[0]->product_group_name);
					$category_name = preg_replace('/[^A-Za-z0-9]/', '', $decodedCategoryData[0]->product_category_name);
									
					$convertedCompanyName = substr($company_name,0,2);
					$convertedCategoryName = substr($category_name,0,2);
					$convertedGroupName = substr($group_name,0,2);
					
					$productNameLength = strlen($product_name);
					$productName1 = substr($product_name,0,2);
					$productName2 = substr($product_name,$productNameLength-2,2);
					$convertedProductName  = $productName1.$productName2;
					
					$colorLength = strlen($color);
					$color1 = substr($color,0,1);
					$color2 = substr($color,$colorLength-1,1);
					$convertedColor = $color1.$color2;
					
					$sizeLength = strlen($size);
					$size1 = substr($size,0,1);
					$size2 = substr($size,$sizeLength-1,1);
					$convertedSize = $size1.$size2;

					$variantLength = strlen($variant);
					$variant1 = substr($variant,0,1);
					$variant2 = substr($variant,$variantLength-1,1);
					$convertedVariant = $variant1.$variant2;

					$tRequest['product_code'] = $convertedCompanyName.
												$convertedCategoryName.
												$convertedGroupName.
												$convertedProductName.
												$convertedColor.
												$convertedVariant.
												$convertedSize;
					//convert string to upper-case
					$convertedProductCode = strtoupper($tRequest['product_code']);
					$tRequest['product_code'] = $convertedProductCode;
					
				}else{
					$convertedProductCode = $tRequest['product_code'];
				}
				// validation
				$validationResult = $productValidate->productCodeValidate($tRequest['company_id'],$convertedProductCode);
				$indexNumber=1;
				if(strcmp($validationResult,$msgArray['200'])!=0)
				{
					$productCode = $convertedProductCode.$indexNumber;
					$result = $this->batchRepeatProductCodeValidate($productCode,$tRequest['company_id'],$indexNumber);
					$tRequest['product_code'] = $result;
					$codeFlag=1;
				}
			}	

			if(strcmp($validationResult,$msgArray['200'])==0 || $codeFlag==1)
			{
				// validation
				$status = $productValidate->validate($tRequest);
				if($status=="Success")
				{
					foreach ($tRequest as $key => $value)
					{
						if(!is_numeric($value))
						{
							if (strpos($value, '\'') !== FALSE)
							{
								$productValue[$data]= str_replace("'","\'",$value);
								$keyName[$data] = $key;
							}
							else
							{
								$productValue[$data] = $value;
								$keyName[$data] = $key;
							}
						}
						else
						{
							$productValue[$data]= $value;
							$keyName[$data] = $key;
						}
						$data++;
					}
					
					// set data to the persistable object
					for($data=0;$data<count($productValue);$data++)
					{
						// set the data in persistable object
						$productPersistable = new ProductPersistable();	
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));

						// make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$productPersistable->$setFuncName($productValue[$data]);
						$productPersistable->setName($getFuncName[$data]);
						$productPersistable->setKey($keyName[$data]);
						$productArray[$data] = array($productPersistable);
						if($data==(count($productValue)-1))
						{
							if($docFlag==1)
							{
								$productArray[$data+1]=$processedData;
							}
						}
					}

					return $productArray;
				}
				else
				{
					return $status;
				}
			}
			else
			{
				return $validationResult;
			}
		}
	}
	
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return product Persistable object
     */	
    public function createPersistableBatchData(Request $request)
	{
		$tKeyValue = array();
		$value = array();
		
		// get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		if(count($_POST)==0)
		{
			return $msgArray['204'];
		}
		else
		{
			$productValidate = new ProductValidate();
			// trim an input 
			$productTransformer = new ProductTransformer();
			$trimData = $productTransformer->trimInsertBatchData($request);
	
			if(is_array($trimData))
			{
				$tRequestData = $trimData['dataArray'];
				$totalErrorArray = count($trimData['errorArray']);
				$countRequestedData = count($tRequestData);
				
				$productCodeArray = array();
				$newProductArray = array();

				//Duplicate Reduction Array
				$pro_company_array = array();
				$pro_group_array = array();
				$pro_category_array = array();

				$companyModel = new CompanyModel();
				$productGroupData = new ProductGroupModel();
				$productCategoryData = new ProductCategoryModel();
				$query_count = 0;
				for($dataArray=0;$dataArray<count($tRequestData);$dataArray++)
				{
					$data=0;
					$codeFlag=0;
					$keyName = array();
					$productValue = array();
					$tRequest = $tRequestData[$dataArray];
					// make a product_code and validate it with other codes
					// get company_name 
					if (!isset($pro_company_array[$tRequest['company_id']])) {
						
						$companyResult = $companyModel->getData($tRequest['company_id']);
						$pro_company_array[$tRequest['company_id']] = json_decode($companyResult);
					}	
					$decodedCompanyData = $pro_company_array[$tRequest['company_id']];
					
					//get product group name
					if (!isset($pro_group_array[$tRequest['product_group_id']])) {
						
						$groupData = $productGroupData->getData($tRequest['product_group_id']);
						$pro_group_array[$tRequest['product_group_id']] = json_decode($groupData);
					}
					$decodedGroupData = $pro_group_array[$tRequest['product_group_id']];

					//get product category name
					if (!isset($pro_category_array[$tRequest['product_category_id']])) {
						
						$categoryData = $productCategoryData->getData($tRequest['product_category_id']);
						$pro_category_array[$tRequest['product_category_id']] = json_decode($categoryData);
					}
					$decodedCategoryData = $pro_category_array[$tRequest['product_category_id']];

					if($tRequest['color']=="")
					{
						$tRequest['color'] = "XX";
					}
					if($tRequest['size']=="")
					{
						$tRequest['size'] = "ZZ";
					}
					if(@$tRequest['variant']=="")
					{
						$tRequest['variant'] = "YY";
					}
					$color = preg_replace('/[^A-Za-z0-9\-]/', '', $tRequest['color']);
					$size = preg_replace('/[^A-Za-z0-9\-]/', '', $tRequest['size']);
					$variant = preg_replace('/[^A-Za-z0-9\-]/', '', $tRequest['variant']);
					$product_name = preg_replace('/[^A-Za-z0-9]/', '', $tRequest['product_name']);
					$company_name = preg_replace('/[^A-Za-z0-9]/', '', $decodedCompanyData[0]->company_name);
					$group_name = preg_replace('/[^A-Za-z0-9]/', '', $decodedGroupData[0]->product_group_name);
					$category_name = preg_replace('/[^A-Za-z0-9]/', '', $decodedCategoryData[0]->product_category_name);
									
					$convertedCompanyName = substr($company_name,0,2);
					$convertedCategoryName = substr($category_name,0,2);
					$convertedGroupName = substr($group_name,0,2);
					
					$productNameLength = strlen($product_name);
					$productName1 = substr($product_name,0,2);
					$productName2 = substr($product_name,$productNameLength-2,2);
					$convertedProductName  = $productName1.$productName2;
					
					$colorLength = strlen($color);
					$color1 = substr($color,0,1);
					$color2 = substr($color,$colorLength-1,1);
					$convertedColor = $color1.$color2;
					
					$sizeLength = strlen($size);
					$size1 = substr($size,0,1);
					$size2 = substr($size,$sizeLength-1,1);
					$convertedSize = $size1.$size2;

					$variantLength = strlen($variant);
					$variant1 = substr($variant,0,1);
					$variant2 = substr($variant,$variantLength-1,1);
					$convertedVariant = $variant1.$variant2;

					$tRequest['product_code'] = $convertedCompanyName.
											$convertedCategoryName.
											$convertedGroupName.
											$convertedProductName.
											$convertedColor.
											$convertedVariant.
											$convertedSize;
					// convert string to upper-case
					$convertedProductCode = strtoupper($tRequest['product_code']);
					$tRequest['product_code'] = $convertedProductCode;
					
					// validation
					$status = $productValidate->validate($tRequest);
					
					if($status=="Success")
					{
						$productArray = array();
						$getFuncName = array();

						foreach ($tRequest as $key => $value)
						{
							if(!is_numeric($value))
							{
								if (strpos($value, '\'') !== FALSE)
								{
									$productValue_v= str_replace("'","\'",$value);
									$keyName_v = $key;
								}
								else
								{
									$productValue_v = $value;
									$keyName_v = $key;
								}
							}
							else
							{
								$productValue_v= $value;
								$keyName_v = $key;
							}

							// set the data in persistable object
							$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName_v)));
							// make function name dynamically
							$productPersistable = new ProductPersistable();
							$setFuncName = 'set'.$str;
							$getFuncName = 'get'.$str;
							$productPersistable->$setFuncName($productValue_v);
							$productPersistable->setName($getFuncName);
							$productPersistable->setKey($keyName_v);
							$productArray[$data] = array($productPersistable);

							$data++;
						}
						array_push($newProductArray,$productArray);
					}
					else
					{
						$decodedArrayStatus = json_decode($status);
						//convert object to array
						$decodedArray = (array)$decodedArrayStatus[0];
						$totalErrorArray = count($trimData['errorArray']);
						
						$trimData['errorArray'][$totalErrorArray]['productName'] = $trimData['dataArray'][$dataArray]['product_name'];
						$trimData['errorArray'][$totalErrorArray]['measurementUnit'] = $trimData['dataArray'][$dataArray]['measurement_unit'];
						$trimData['errorArray'][$totalErrorArray]['color'] = $trimData['dataArray'][$dataArray]['color'];
						$trimData['errorArray'][$totalErrorArray]['size'] = $trimData['dataArray'][$dataArray]['size'];
						$trimData['errorArray'][$totalErrorArray]['variant'] = $trimData['dataArray'][$dataArray]['variant'];
						$trimData['errorArray'][$totalErrorArray]['isDisplay'] = $trimData['dataArray'][$dataArray]['is_display'];
						$trimData['errorArray'][$totalErrorArray]['purchasePrice'] = $trimData['dataArray'][$dataArray]['purchase_price'];
						$trimData['errorArray'][$totalErrorArray]['wholesaleMargin'] = $trimData['dataArray'][$dataArray]['wholesale_margin'];
						$trimData['errorArray'][$totalErrorArray]['wholesaleMarginFlat'] = $trimData['dataArray'][$dataArray]['wholesale_margin_flat'];
						$trimData['errorArray'][$totalErrorArray]['vat'] = $trimData['dataArray'][$dataArray]['vat'];
						$trimData['errorArray'][$totalErrorArray]['mrp'] = $trimData['dataArray'][$dataArray]['mrp'];
						$trimData['errorArray'][$totalErrorArray]['margin'] = $trimData['dataArray'][$dataArray]['margin'];
						$trimData['errorArray'][$totalErrorArray]['marginFlat'] = $trimData['dataArray'][$dataArray]['margin_flat'];
						$trimData['errorArray'][$totalErrorArray]['productDescription'] = $trimData['dataArray'][$dataArray]['product_description'];
						$trimData['errorArray'][$totalErrorArray]['additionalTax'] = $trimData['dataArray'][$dataArray]['additional_tax'];
						$trimData['errorArray'][$totalErrorArray]['minimumStockLevel'] = $trimData['dataArray'][$dataArray]['minimum_stock_level'];
						$trimData['errorArray'][$totalErrorArray]['productMenu'] = $trimData['dataArray'][$dataArray]['product_menu'];
						$trimData['errorArray'][$totalErrorArray]['productType'] = $trimData['dataArray'][$dataArray]['product_type'];
						$trimData['errorArray'][$totalErrorArray]['maxSaleQty'] = $trimData['dataArray'][$dataArray]['max_sale_qty'];
						$trimData['errorArray'][$totalErrorArray]['notForSale'] = $trimData['dataArray'][$dataArray]['not_for_sale'];
						$trimData['errorArray'][$totalErrorArray]['taxInclusive'] = $trimData['dataArray'][$dataArray]['tax_inclusive'];
						$trimData['errorArray'][$totalErrorArray]['bestBeforeTime'] = $trimData['dataArray'][$dataArray]['best_before_time'];
						$trimData['errorArray'][$totalErrorArray]['bestBeforeType'] = $trimData['dataArray'][$dataArray]['best_before_type'];
						$trimData['errorArray'][$totalErrorArray]['cessFlat'] = $trimData['dataArray'][$dataArray]['cess_flat'];
						$trimData['errorArray'][$totalErrorArray]['cessPercentage'] = $trimData['dataArray'][$dataArray]['cess_percentage'];
						$trimData['errorArray'][$totalErrorArray]['opening'] = $trimData['dataArray'][$dataArray]['opening'];
						// Git missed change
						// $trimData['errorArray'][$totalErrorArray]['igst'] = $trimData['dataArray'][$dataArray]['igst'];
						// $trimData['errorArray'][$totalErrorArray]['hsn'] = $trimData['dataArray'][$dataArray]['hsn'];

						$trimData['errorArray'][$totalErrorArray]['semiWholesaleMargin'] = $trimData['dataArray'][$dataArray]['semi_wholesale_margin'];
						$trimData['errorArray'][$totalErrorArray]['companyId'] = $trimData['dataArray'][$dataArray]['company_id'];
						$trimData['errorArray'][$totalErrorArray]['productCategoryId'] = $trimData['dataArray'][$dataArray]['product_category_id'];
						$trimData['errorArray'][$totalErrorArray]['productGroupId'] = $trimData['dataArray'][$dataArray]['product_group_id'];
						$trimData['errorArray'][$totalErrorArray]['branchId'] = $trimData['dataArray'][$dataArray]['branch_id'];
						$trimData['errorArray'][$totalErrorArray]['remark'] = $decodedArray[array_keys($decodedArray)[0]];
						$totalErrorArray++;
					}
				}
				// $trimData['dataArray'] = array();
				// unset($trimData['dataArray']);
				$trimData['dataArray'] = $newProductArray;
				return $trimData;
			}
			else
			{
				$errorResult = array();
				$errorResult['mapping_error'] = $trimData;
				$encodeResult = json_encode($errorResult);
				
				return $encodeResult;
			}
		}
	}
	
	/**
     * repeat product-code validation with database 
     * product-code,company-id and index-number
     * @return status 200:ok
     */	
	public function batchRepeatProductCodeValidate($productCode,$companyId,$indexNumber)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productValidate = new ProductValidate();
		$validationResult = $productValidate->productCodeValidate($companyId,$productCode);
		if(strcmp($validationResult,$exceptionArray['200'])!=0)
		{
			$indexNumber= $indexNumber+1;
			if($indexNumber<=10)
			{
				$productCode = substr_replace($productCode,'', -1);
			}
			if($indexNumber>=11)
			{
				$productCode = substr_replace($productCode,'', -2);
			}
			$newProductCode = $productCode.$indexNumber;
			$result = $this->batchRepeatProductCodeValidate($newProductCode,$companyId,$indexNumber);
			return $result;
		}
		else
		{
			return $productCode;
		}
	}
	
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return product Persistable object
     */	
    public function createPersistableInOutWard(Request $request,$inOutWard)
	{	
		$this->request = $request;	
		$data=0;
		$productValidate = new ProductValidate();
		// trim an input 
		$productTransformer = new ProductTransformer();
		$tRequest = $productTransformer->trimInsertInOutwardData($this->request,$inOutWard);
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		if($tRequest==1)
		{
			return $exceptionArray['content'];
		}	
		else
		{
			if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$tRequest['transactionDate']))
			{
				return "transaction-date is not valid";
			}
			// validation
			$status = $productValidate->validateInOutward($tRequest);
			if($status=="Success")
			{
				$productPersistable=array();
				for($data=0;$data<count($tRequest[0]);$data++)
				{
					$productPersistable[$data] = new ProductPersistable();
					$productPersistable[$data]->setTransactionDate($tRequest['transactionDate']);
					$productPersistable[$data]->setCompanyId($tRequest['companyId']);
					$productPersistable[$data]->setTransactionType($tRequest['transactionType']);
					$productPersistable[$data]->setInvoiceNumber($tRequest['invoiceNumber']);
					$productPersistable[$data]->setBillNumber($tRequest['billNumber']);
					$productPersistable[$data]->setTax($tRequest['tax']);
					
					$productPersistable[$data]->setProductId($tRequest[0][$data]['productId']);
					$productPersistable[$data]->setDiscount($tRequest[0][$data]['discount']);
					$productPersistable[$data]->setDiscountValue($tRequest[0][$data]['discountValue']);
					$productPersistable[$data]->setDiscountType($tRequest[0][$data]['discountType']);
					$productPersistable[$data]->setPrice($tRequest[0][$data]['price']);
					$productPersistable[$data]->setQty($tRequest[0][$data]['qty']);
				}
				return $productPersistable;
			}
			else
			{
				return $status;
			}
		}
	}
	
	/**
     * update product
     * $param Request object [Request $request] and product id
     * @return product Persistable object
     */	
	public function createPersistableChange(Request $request,$productId,$result)
	{
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		// update
		if($requestMethod == $constantArray['postMethod'])
		{
			$productValue = array();
			$productPersistable;
			$productArray = array();
			$productValidate = new ProductValidate();
			$status;
			$docFlag=0;
			$documentName="";
			$documentArray = array();
			$file = $request->file();
			// if data is not available in update request
			if(count($request->input())==0  && count($file)==0)
			{
				$status = $exceptionArray['204'];
				return $status;
			}
			// data is avalilable for update
			else
			{
				//file uploading
				if(in_array(true,$file))
				{
					$documentController =new DocumentController(new Container());
					$processedData = $documentController->insertUpdate($request,$constantArray['productDocumentUrl']);
					if(is_array($processedData))
					{
						$docFlag=1;
					}
					else
					{
						return $processedData;
					}
				}
				$productTransformer = new ProductTransformer();
				$tRequest = $productTransformer->trimUpdateData($request->input(),$request->header());
				
				//get product data
				$decodedProductData = json_decode($result);
				
				if(!is_array($tRequest))
				{
					return $exceptionArray['content'];
				}
				$countRequest = count($tRequest);
				//product-code validation
				if(array_key_exists('companyId',$request->input()) || array_key_exists('productGroupId',$request->input()) || 
				array_key_exists('productCategoryId',$request->input()) || array_key_exists('color',$request->input()) || 
				array_key_exists('size',$request->input()) || array_key_exists('variant',$request->input()) || 
				array_key_exists('productName',$request->input()))
				{
					if(!array_key_exists('companyId',$request->input()))
					{
						$companyModel = new CompanyModel();
						$companyResult = $companyModel->getData($decodedProductData[0]->company_id);
						$companyId = json_decode($companyResult)[0]->company_id;
					}
					else
					{
						$companyId = $request->input()['companyId'];
					}
					$validationResult = $this->productCodeValidation($tRequest,$productId,$decodedProductData);
					if(!is_array($validationResult))
					{
						$indexNumber=1;
						$productCode = $validationResult.$indexNumber;
						$result = $this->batchRepeatProductCodeUpdateValidate($productCode,$companyId,$indexNumber,$productId);
						$tRequest[$countRequest]['product_code'] = $result;
						$codeFlag=1;
					}
					else
					{
						$tRequest = $validationResult;
					}
					
				}

				for($data=0;$data<count($tRequest);$data++)
				{
					// data get from body
					$productPersistable = new ProductPersistable();
					
					// get key value from trim array
					$tKeyValue[$data] = array_keys($tRequest[$data])[0];
					$tValue[$data] = $tRequest[$data][array_keys($tRequest[$data])[0]];
					
					// validation
					$status = $productValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[$data]);
					
					// enter data is valid(one data validate status return)
					if($status=="Success")
					{
						// check data is string or not
						if(!is_numeric($tValue[$data]))
						{
							if (strpos($tValue[$data], '\'') !== FALSE)
							{
								$productValue[$data] = str_replace("'","\'",$tValue[$data]);
							}
							else
							{
								$productValue[$data] = $tValue[$data];
							}
						}
						else
						{
							$productValue[$data] = $tValue[$data];
						}
						
						// flag=0...then data is valid(consider one data at a time)
						if($flag==0)
						{
							$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
							// make function name dynamically
							$setFuncName = 'set'.$str;
							$getFuncName[$data] = 'get'.$str;
							$productPersistable->$setFuncName($productValue[$data]);
							$productPersistable->setName($getFuncName[$data]);
							
							$productPersistable->setKey($tKeyValue[$data]);
							$productPersistable->setProductId($productId);
							
							$productArray[$data] = array($productPersistable);
							if($data==(count($tRequest)-1))
							{
								if($docFlag==1)
								{
									$productArray[$data+1]=$processedData;
								}
							}
						}
					}
					// enter data is not valid
					else
					{
						// if flag==1 then enter data is not valid ..so error return(consider one data at a time)
						$flag=1;
						if(!empty($status[0]))
						{
							$errorStatus[$errorCount]=$status[0];
							$errorCount++;
						}
					}
					if($data==(count($tRequest)-1))
					{
						if($flag==1)
						{
							return json_encode($errorStatus);
						}
						else
						{
							
							return $productArray;
						}
					}
					
				}
			}
		}
		
		// delete
		else if($requestMethod == $constantArray['deleteMethod'])
		{
			$productPersistable = new productPersistable();		
			$productPersistable->setproductId($productId);			
			return $productPersistable;
		}
	}	
	
	/**
     * repeat product-code validation with database for update 
     * product-code,company-id and index-number
     * @return product-code
     */	
	public function batchRepeatProductCodeUpdateValidate($productCode,$companyId,$indexNumber,$productId)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productValidate = new ProductValidate();
		$validationResult = $productValidate->productUpdateCodeValidate($companyId,$productCode,$productId);
		if(strcmp($validationResult,$exceptionArray['200'])!=0)
		{
			$indexNumber= $indexNumber+1;
			if($indexNumber<=10)
			{
				$productCode = substr_replace($productCode,'', -1);
			}
			if($indexNumber>=11)
			{
				$productCode = substr_replace($productCode,'', -2);
			}
			$newProductCode = $productCode.$indexNumber;
			$result = $this->batchRepeatProductCodeUpdateValidate($newProductCode,$companyId,$indexNumber,$productId);
			return $result;
		}
		else
		{
			return $productCode;
		}
	}
	
	/**
     * validate product-code if required
     * $param trim array and product-id
     * @return validation-result
     */
	public function productCodeValidation($tRequest,$productId,$decodedProductData)
	{
		$companyFlag=0;
		$categoryFlag=0;
		$groupFlag=0;
		$colorFlag=0;
		$sizeFlag=0;
		$variantFlag=0;
		$productNameFlag=0;
		$productValidate = new ProductValidate();
		
		//make a product_code and validate it with other codes
		$companyModel = new CompanyModel();
		$productGroupData = new ProductGroupModel();
		$productCategoryData = new ProductCategoryModel();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//get company_name for checking product-code
		for($arrayData=0;$arrayData<count($tRequest);$arrayData++)
		{
			if(array_key_exists('company_id',$tRequest[$arrayData]))
			{
				$companyFlag=1;
				$companyResult = $companyModel->getData($tRequest[$arrayData]['company_id']);
				$companyId = json_decode($companyResult)[0]->company_id;
				$companyResult = json_decode($companyResult)[0]->company_name;
			}
			if(array_key_exists('product_group_id',$tRequest[$arrayData]))
			{
				$groupFlag=1;
				//get product group name
				$groupData = $productGroupData->getData($tRequest[$arrayData]['product_group_id']);
				$groupData = json_decode($groupData)[0]->product_group_name;
			}
			if(array_key_exists('product_category_id',$tRequest[$arrayData]))
			{
				$categoryFlag=1;
				//get product category name
				$categoryData = $productCategoryData->getData($tRequest[$arrayData]['product_category_id']);
				$categoryData = json_decode($categoryData)[0]->product_category_name;
			}
			if(array_key_exists('color',$tRequest[$arrayData]))
			{
				$colorFlag=1;
				if($tRequest[$arrayData]['color']=="")
				{
					$color = "XX";
				}
				else
				{
					$color = $tRequest[$arrayData]['color'];
				}
			}
			if(array_key_exists('size',$tRequest[$arrayData]))
			{
				$sizeFlag=1;
				if($tRequest[$arrayData]['size']=="")
				{
					$tRequest[$arrayData]['size']="ZZ";
				}
				else
				{
					$size = $tRequest[$arrayData]['size'];
				}
			}
			if(array_key_exists('variant',$tRequest[$arrayData]))
			{
				$variantFlag=1;
				if($tRequest[$arrayData]['variant']=="")
				{
					$tRequest[$arrayData]['variant']="YY";
				}
				else
				{
					$variant = $tRequest[$arrayData]['variant'];
				}
			}
			if(array_key_exists('product_name',$tRequest[$arrayData]))
			{
				$productNameFlag=1;
				$productName = $tRequest[$arrayData]['product_name'];
			}
		}
		if($companyFlag==0)
		{
			$companyResult = $companyModel->getData($decodedProductData[0]->company_id);
			$companyId = json_decode($companyResult)[0]->company_id;
			$companyResult = json_decode($companyResult)[0]->company_name;
		}
		if($groupFlag==0)
		{
			$groupData = $productGroupData->getData($decodedProductData[0]->product_group_id);
			$groupId = json_decode($groupData)[0]->product_group_id;
			$groupData = json_decode($groupData)[0]->product_group_name;
		}
		if($categoryFlag==0)
		{
			$categoryData = $productCategoryData->getData($decodedProductData[0]->product_category_id);
			$categoryId = json_decode($categoryData)[0]->product_category_id;
			$categoryData = json_decode($categoryData)[0]->product_category_name;
		}
		if($colorFlag==0)
		{
			$color = $decodedProductData[0]->color;
		}
		if($sizeFlag==0)
		{
			$size = $decodedProductData[0]->size;
		}
		if($variantFlag==0)
		{
			$variant = $decodedProductData[0]->variant;
		}
		if($productNameFlag==0)
		{
			$productName = $decodedProductData[0]->product_name;
		}
		$color = preg_replace('/[^A-Za-z0-9]/', '', $color);
		$size = preg_replace('/[^A-Za-z0-9]/', '', $size);
		$variant = preg_replace('/[^A-Za-z0-9]/', '', $variant);
		$product_name = preg_replace('/[^A-Za-z0-9]/', '', $productName);
		$company_name = preg_replace('/[^A-Za-z0-9]/', '', $companyResult);
		$group_name = preg_replace('/[^A-Za-z0-9]/', '', $groupData);
		$category_name = preg_replace('/[^A-Za-z0-9]/', '', $categoryData);
		
		$convertedCompanyName = substr($company_name,0,2);
		$convertedCategoryName = substr($category_name,0,2);
		$convertedGroupName = substr($group_name,0,2);
		
		$productNameLength = strlen($product_name);
		$productName1 = substr($product_name,0,2);
		$productName2 = substr($product_name,$productNameLength-2,2);
		$convertedProductName  = $productName1.$productName2;
		
		$colorLength = strlen($color);
		$color1 = substr($color,0,1);
		$color2 = substr($color,$colorLength-1,1);
		$convertedColor = $color1.$color2;
		
		$sizeLength = strlen($size);
		$size1 = substr($size,0,1);
		$size2 = substr($size,$sizeLength-1,1);
		$convertedSize = $size1.$size2;

		$variantLength = strlen($variant);
		$variant1 = substr($variant,0,1);
		$variant2 = substr($variant,$variantLength-1,1);
		$convertedVariant = $variant1.$variant2;
		
		$totalCount = count($tRequest);
		$tRequest[$totalCount]['product_code']=$convertedCompanyName.
												$convertedCategoryName.
												$convertedGroupName.
												$convertedProductName.
												$convertedColor.
												$convertedVariant.
												$convertedSize;
		//convert string to upper-case
		$convertedProductCode = strtoupper($tRequest[$totalCount]['product_code']);
		$tRequest[$totalCount]['product_code'] = $convertedProductCode;
		// validation
		$validationResult = $productValidate->productUpdateCodeValidate($companyId,$convertedProductCode,$productId);
		if(strcmp($exceptionArray['200'],$validationResult)==0)
		{
			return $tRequest;
		}
		else
		{
			return $convertedProductCode;
		}
	}
	
	/**
     * process product-transaction data(sale/purchase)
     * $param product-array and transaction-type
     * @return product-transaction Persistable object/exception message/error message
     */	
	public function createPersistableChangeInOutWard($productArray,$inOutWard,$jfId)
	{
		$errorCount=0;
		$errorStatus=array();
		$flag=0;
		$trimFlag=0;
		$trimArrayFalg=0;
		$productPersistable;
		$productValidate = new ProductValidate();
		$status;
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		// update
		if(strcmp($requestMethod,$constantArray['postMethod'])==0)
		{
			// if data is not available in update request
			if(count($_POST)==0)
			{
				$status = $exceptionArray['204'];
				return $status;
			}
			// data is avalilable for update
			else
			{
				$productPersistable = array();
				$productMultipleArray = array();
				$productSingleArray = array();
				
				// trim an input 
				$productTransformer = new ProductTransformer();
				$tRequest = $productTransformer->trimUpdateProductData($productArray,$inOutWard);
				
				if($tRequest==1)
				{
					return $exceptionArray['content'];
				}
				else
				{
					if(strcmp($inOutWard,$constantArray['journalInward'])==0)
					{
						$headerType=$constantArray['purchase'];
					}
					else
					{
						$headerType=$constantArray['sales'];
					}
					$journalData = array();
					if(array_key_exists("tax",$tRequest) || array_key_exists("0",$tRequest))
					{
						if(array_key_exists("0",$tRequest))
						{
							if(array_key_exists('flag',$tRequest))
							{
								$tRequest = $tRequest[0];
							}
							$validationResult = $productValidate->validateTransactionArrayUpdateData($tRequest);
							if(strcmp($validationResult,"Success")!=0)
							{
								return $validationResult;
							}
						}
						// check accounting Rules
						$buisnessLogic = new BuisnessLogic();
						$buisnessResult = $buisnessLogic->validateUpdateProductBuisnessLogic($headerType,$journalData,$tRequest,$jfId);
						if(!is_array($buisnessResult))
						{
							return $buisnessResult;
						}
					}
				}
				// get data from trim array
				if(is_array($tRequest))
				{
					// data is exists in request or not checking by flag
					if(array_key_exists($constantArray['flag'],$tRequest))
					{
						$trimFlag=1;
					}
					// data
					if($trimFlag==1)
					{
						// check array is exists 
						if(array_key_exists(0,$tRequest))
						{
							$trimArrayFalg=1;
						}	
						// array with data
						if($trimArrayFalg==1)
						{
							// validate single data
							for($multipleArray=0;$multipleArray<count($tRequest[0]);$multipleArray++)
							{
								$productPersistable[$multipleArray] = new ProductPersistable();
								$productPersistable[$multipleArray]->setDiscount($tRequest[0][$multipleArray]['discount']);
								$productPersistable[$multipleArray]->setDiscountValue($tRequest[0][$multipleArray]['discount_value']);
								$productPersistable[$multipleArray]->setDiscountType($tRequest[0][$multipleArray]['discount_type']);
								$productPersistable[$multipleArray]->setProductId($tRequest[0][$multipleArray]['product_id']);
								$productPersistable[$multipleArray]->setPrice($tRequest[0][$multipleArray]['price']);
								$productPersistable[$multipleArray]->setQty($tRequest[0][$multipleArray]['qty']);
								$productMultipleArray[$multipleArray] = array($productPersistable[$multipleArray]);
							}
						
							for($trimResponse=0;$trimResponse<count($tRequest)-2;$trimResponse++)
							{
								$tKeyValue = array_keys($tRequest)[$trimResponse];
								$tValue =$tRequest[array_keys($tRequest)[$trimResponse]];
								$trimRequest[0] = array($tKeyValue=>$tValue);
								
								//validate transaction-date
								if(array_key_exists('transaction_date',$trimRequest[0]))
								{
									if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$trimRequest[0]['transaction_date']))
									{
										return "transaction-date is not valid";
									}
								}
								$status = $productValidate->validateTransactionUpdateData($tKeyValue,$tValue,$trimRequest[0]);
								
								if(strcmp($status,"Success")!=0)
								{
									return $status;
								}
								else
								{
									$productPersistable[$trimResponse] = new ProductPersistable();
									$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue)));
									$setFuncName = 'set'.$str;
									$getFuncName = 'get'.$str;
									$productPersistable[$trimResponse]->$setFuncName($tValue);
									$productPersistable[$trimResponse]->setName($getFuncName);
									$productPersistable[$trimResponse]->setKey($tKeyValue);
									$productSingleArray[$trimResponse] = array($productPersistable[$trimResponse]);
								}
							}
							array_push($productSingleArray,$productMultipleArray);
							return $productSingleArray;
						}
						// only data exists
						else
						{
							for($trimResponse=0;$trimResponse<count($tRequest)-1;$trimResponse++)
							{
								$tKeyValue = array_keys($tRequest)[$trimResponse];
								$tValue =$tRequest[array_keys($tRequest)[$trimResponse]];
								$trimRequest[0] = array($tKeyValue=>$tValue);
								
								//validate transaction-date
								if(array_key_exists('transaction_date',$trimRequest[0]))
								{
									if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$trimRequest[0]['transaction_date']))
									{
										return "transaction-date is not valid";
									}
								}
								$status = $productValidate->validateTransactionUpdateData($tKeyValue,$tValue,$trimRequest[0]);
								
								if(strcmp($status,"Success")!=0)
								{
									return $status;
								}
								else
								{
									$productPersistable[$trimResponse] = new ProductPersistable();
									$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue)));
									$setFuncName = 'set'.$str;
									$getFuncName = 'get'.$str;
									$productPersistable[$trimResponse]->$setFuncName($tValue);
									$productPersistable[$trimResponse]->setName($getFuncName);
									$productPersistable[$trimResponse]->setKey($tKeyValue);
									$productSingleArray[$trimResponse] = array($productPersistable[$trimResponse]);
								}
							}
							return $productSingleArray;
						}
					}
					// only array exists
					else
					{
						for($multipleArray=0;$multipleArray<count($tRequest);$multipleArray++)
						{
							$productPersistable[$multipleArray] = new ProductPersistable();
							$productPersistable[$multipleArray]->setDiscount($tRequest[$multipleArray]['discount']);
							$productPersistable[$multipleArray]->setDiscountValue($tRequest[$multipleArray]['discount_value']);
							$productPersistable[$multipleArray]->setDiscountType($tRequest[$multipleArray]['discount_type']);
							$productPersistable[$multipleArray]->setProductId($tRequest[$multipleArray]['product_id']);
							$productPersistable[$multipleArray]->setPrice($tRequest[$multipleArray]['price']);
							$productPersistable[$multipleArray]->setQty($tRequest[$multipleArray]['qty']);
							$productMultipleArray[$multipleArray] = array($productPersistable[$multipleArray]);
						}
						$productMultipleArray['flag']="1";
						return $productMultipleArray;
					}
				}
				else
				{
					return $tRequest;
				}
			}
		}
	}	
	
	/**
     * process header data
     * $param request header
     * @return persistable object of header data
     */	
	public function createJfIdPersistableData($requestHeader)
	{
		$trimJfId = trim($requestHeader['jfid'][0]);
		$productPersistable = new ProductPersistable();
		$productPersistable->setJfId($trimJfId);
		return $productPersistable;
	}
	
	/**
     * process vendor id
     * $param request input
     * @return vendor_id
     */	
	public function processVendorId($requestInput)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$trimVendorId = trim($requestInput['vendorId']);
		// if($trimVendorId=="")
		// {
			// return $exceptionArray['invalidClientName'];
		// }
		// $result =  preg_match("/^[a-zA-Z &_`#().\'-]*$/",$trimclientName);
		// if($result == 1)
		// {
		  	$dataArray = array();
			$dataArray['vendor_id'] = $trimVendorId;
			return $dataArray;
		// }
		// else
		// {
			// return $exceptionArray['invalidClientName'];
		// }

	}
	
	/**
     * process header data
     * $param request header
     * @return persistable object of header data
     */	
	public function createprocessDatePersistableData($requestHeader)
	{
		$fromDate = $requestHeader['fromdate'][0];
		$toDate = $requestHeader['todate'][0];
		
		//date conversion
		// from date conversion
		$splitedFromDate = explode("-",$fromDate);
		$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
		// to date conversion
		$splitedToDate = explode("-",$toDate);
		$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
	
		//validate date
		if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$transformFromDate))
		{
			return "from-date is not valid";
		}
		if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$transformToDate))
		{
			return "to-date is not valid";
		}
		$productPersistable = new ProductPersistable();
		$productPersistable->setFromDate($transformFromDate);
		$productPersistable->setToDate($transformToDate);
		return $productPersistable;
	}

	/**
     * process request-input data for batch-update
     * $param request-input data
     * @return persistable object of header data
     */
	public function createPersistableBatchUpdateChange(Request $request)
	{
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		// if data is not available in update request
		if(count($request->input())==1 || count($request->input())==0)
		{
			$status = $exceptionArray['204'];
			return $status;
		}
		// data is avalilable for update
		else
		{	
			$flag=0;
			$productValue = array();
			$productArray = array();
			$productValidate = new ProductValidate();
			$productData = $request->input()['product'];
			$inputData = $request->input();
			$headerData = $request->header();
			unset($inputData['product']);
			$productTransformer = new ProductTransformer();
			$tRequest = $productTransformer->trimUpdateData($inputData,$headerData);
			if(!is_array($tRequest))
			{
				return $exceptionArray['content'];
			}
			// $tRequest[count($tRequest)]['product'] = $productData;
			$requestCount = count($tRequest);
			for($data=0;$data<$requestCount;$data++)
			{
				// data get from body
				$productPersistable = new ProductPersistable();
				
				// get key value from trim array
				$tKeyValue[$data] = array_keys($tRequest[$data])[0];
				$tValue[$data] = $tRequest[$data][array_keys($tRequest[$data])[0]];
				// validation
				$status = $productValidate->validateUpdateData($tKeyValue[$data],$tValue[$data],$tRequest[$data]);
				// enter data is valid(one data validate status return)
				if($status=="Success")
				{
					// check data is string or not
					if(!is_numeric($tValue[$data]))
					{
						if (strpos($tValue[$data], '\'') !== FALSE)
						{
							$productValue[$data] = str_replace("'","\'",$tValue[$data]);
						}
						else
						{
							$productValue[$data] = $tValue[$data];
						}
					}
					else
					{
						$productValue[$data] = $tValue[$data];
					}
					// flag=0...then data is valid(consider one data at a time)
					if($flag==0)
					{
						$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $tKeyValue[$data])));
						// make function name dynamically
						$setFuncName = 'set'.$str;
						$getFuncName[$data] = 'get'.$str;
						$productPersistable->$setFuncName($productValue[$data]);
						
						$productPersistable->setName($getFuncName[$data]);
						
						$productPersistable->setKey($tKeyValue[$data]);
						$productPersistable->setProductId($productData);
						
						$productArray[$data] = array($productPersistable);
					}
				}
				// enter data is not valid
				else
				{
					// if flag==1 then enter data is not valid ..so error return(consider one data at a time)
					$flag=1;
					if(!empty($status[0]))
					{
						$errorStatus[$errorCount]=$status[0];
						$errorCount++;
					}
				}
				if($data==(count($tRequest)-1))
				{
					if($flag==1)
					{
						return json_encode($errorStatus);
					}
					else
					{
						return $productArray;
					}
				}
			}
		}	
	}
}