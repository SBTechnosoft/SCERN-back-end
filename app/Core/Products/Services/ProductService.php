<?php
namespace ERP\Core\Products\Services;

use ERP\Core\Products\Persistables\ProductPersistable;
use ERP\Core\Products\Entities\Product;
use ERP\Model\Products\ProductModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Core\User\Entities\User;
use ERP\Core\Products\Entities\EncodeData;
use ERP\Core\Products\Entities\EncodeAllData;
use ERP\Core\Products\Entities\DocumentEncodeData;
use ERP\Core\Products\Entities\QuantityPricingEncodeData;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Products\Entities\EncodeProductTrnAllData;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Products\Entities\EncodeAllStockSummaryData;
use ERP\Core\Settings\Services\SettingService;
use ERP\Model\Authenticate\AuthenticateModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Core\Products\Entities\EncodeAllItemizeSummaryData;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductService extends AbstractService
{
    /**
     * @var productService
	 * $var productModel
     */
    private $productService;
    private $productModel;
	
    /**
     * @param ProductService $productService
     */
    public function initialize(ProductService $productService)
    {		
		echo "init";
    }
	
    /**
     * @param ProductPersistable $persistable
     */
    public function create(ProductPersistable $persistable)
    {
		return "create method of ProductService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param ProductPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$productArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$productArray = func_get_arg(0);
		$headerData = func_get_arg(1);
		$documentFlag=0;
		$document = array();
		//check document is available
		if(is_array($productArray[count($productArray)-1][0]))
		{
			$documentCount = count($productArray[count($productArray)-1]);
			//get document data
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				$document[$documentArray] = array();
				$document[$documentArray][0] = $productArray[count($productArray)-1][$documentArray][0];
				$document[$documentArray][1] = $productArray[count($productArray)-1][$documentArray][1];
				$document[$documentArray][2] = $productArray[count($productArray)-1][$documentArray][2];
				$document[$documentArray][3] = $productArray[count($productArray)-1][$documentArray][3];
			}
			$documentFlag=1;
		}
		for($data=0;$data<count($productArray);$data++)
		{
			if($documentFlag==1 && $data==(count($productArray)-1))
			{
				break;
			}
			else
			{
				$funcName[$data] = $productArray[$data][0]->getName();
				$getData[$data] = $productArray[$data][0]->$funcName[$data]();
				$keyName[$data] = $productArray[$data][0]->getkey();
			}
		}

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		//data pass to the model object for insert
		$productModel = new ProductModel();
		$status = $productModel->insertData($getData,$keyName,$document,$headerData);
		// if(strcmp($status,$exceptionArray['200'])==0)
		// {
		// 	$ProductServiceData = $this->fireWebIntegrationPush($productId,$headerData);
		// }
		return $status;
	} 
	
	/**
     * get the data from persistable object and call the model for database insertion opertation
     * @param ProductPersistable $persistable
     * @return status
     */
	public function insertBatchData()
	{
		$getArrayData = array();
		$keyArrayData = array();
		$productArray = array();
		$productArrayResult = func_get_arg(0);
		$productArray = $productArrayResult['dataArray'];
		for($arrayData=0;$arrayData<count($productArray);$arrayData++)
		{
			$funcName = array();
			$getData = array();
			$keyName = array();
			for($data=0;$data<count($productArray[$arrayData]);$data++)
			{
				$funcName[$data] = $productArray[$arrayData][$data][0]->getName();
				$getData[$data] = $productArray[$arrayData][$data][0]->$funcName[$data]();
				$keyName[$data] = $productArray[$arrayData][$data][0]->getkey();
			}
			array_push($getArrayData,$getData);
			array_push($keyArrayData,$keyName);
		}
		//data pass to the model object for insert
		$productModel = new ProductModel();
		$status = $productModel->insertBatchData($getArrayData,$keyArrayData,$productArrayResult['errorArray']);
		return $status;
	}
	
	/**
     * get the data from persistable object and call the model for database insertion opertation
     * @param JournalPersistable $persistable
     * @return status
     */
	public function insertInOutward()
	{
		$productArray = array();
		$discountArray = array();
		$discountValueArray = array();
		$discountTypeArray = array();
		$qtyArray = array();
		$priceArray = array();
		$transactionDateArray = array();
		$transactionTypeArray = array();
		$companyIdArray = array();
		$productIdArray = array();
		$billNumberArray = array();
		$invoiceNumberArray = array();
		$productArray = func_get_arg(0);
		$jfId = func_get_arg(1);
		$vendorId = func_get_arg(2);
		
		for($data=0;$data<count($productArray);$data++)
		{
			$discountArray[$data] = $productArray[$data]->getDiscount();
			$discountValueArray[$data] = $productArray[$data]->getDiscountValue();
			$discountTypeArray[$data] = $productArray[$data]->getDiscountType();
			$productIdArray[$data] = $productArray[$data]->getProductId();
			$qtyArray[$data] = $productArray[$data]->getQty();
			$priceArray[$data] = $productArray[$data]->getPrice();
			
			$transactionDateArray[$data] = $productArray[$data]->getTransactionDate();
			$companyIdArray[$data] = $productArray[$data]->getCompanyId();
			$transactionTypeArray[$data] = $productArray[$data]->getTransactionType();
			$billNumberArray[$data] = $productArray[$data]->getBillNumber();
			$invoiceNumberArray[$data] = $productArray[$data]->getInvoiceNumber();
			$taxArray[$data] = $productArray[$data]->getTax();
		}
		// data pass to the model object for insert
		$productModel = new ProductModel();
		$status = $productModel->insertInOutwardData($discountArray,$discountValueArray,$discountTypeArray,$productIdArray,$qtyArray,$priceArray,$transactionDateArray,$companyIdArray,$transactionTypeArray,$billNumberArray,$invoiceNumberArray,$jfId,$taxArray,$vendorId);
		return $status;
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getAllProductData()
	{
		$productModel = new ProductModel();
		$status = $productModel->getAllData();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data from the table and call the model for database selection opertation
     * @param $productId
     * @return status
     */
	public function getProductData($productId)
	{
		$productModel = new ProductModel();
		$status = $productModel->getData($productId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $status;
		}
		else
		{
			$encoded = new EncodeData();
			$encodeData = $encoded->getEncodedData($status);
			return $encodeData;
		}
	}
	
	/**
     * get all the data from the table and call the model for database selection opertation
     * @param $companyId and header-data
     * @return exception-message/data
     */
	public function getProductTransactionData()
	{
		$persistableObject = func_get_arg(0);
		$header = func_get_arg(1);
		$companyId = func_get_arg(2);
		
		$arrayResult = array();
		
		$fromDate = $persistableObject->getFromDate();
		$toDate = $persistableObject->getToDate();
		
		$productModel = new ProductModel();
		$status = $productModel->getTransactionData($fromDate,$toDate,$header,$companyId);
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $status;
		}
		else
		{
			$decodedData = json_decode($status);
			for($arrayData=0;$arrayData<count($decodedData);$arrayData++)
			{
				// $arrayResult[$arrayData] = array();
				$encodedData = json_encode($decodedData[$arrayData]);
				$encoded = new EncodeProductTrnAllData();
				$encodeData = $encoded->getEncodedAllData($encodedData);
				$encodedJsonData = json_decode($encodeData);
				array_push($arrayResult,$encodedJsonData);
			}
			return json_encode($arrayResult);
		}
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return exception-message/data
     */
	public function getAllData($companyId)
	{
		$productModel = new ProductModel();
		$status = $productModel->getAllProductData($companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$encoded = new EncodeAllData();
			$encodeAllData = $encoded->getEncodedAllData($status);
			return $encodeAllData;
		}
	}
	
	/**
     * get all the data as per given jf-id and call the model for database selection opertation
     * @return exception-message/data
     */
	public function getJfIdProductData()
	{
		$jfIdData = func_get_arg(0);
		$jfId = $jfIdData->getJfId();
		
		//get data from database
		$productModel = new ProductModel();
		$status = $productModel->getJfIdProductData($jfId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$encodeProductData = new EncodeProductTrnAllData();
			$getEncodedData = $encodeProductData->getEncodedAllData($status);
			return $getEncodedData;
		}
	}
	
	/**
     * get all the data as per given id and call the model for database selection opertation
     * @return status
     */
	public function getCBProductData($branchId,$companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if($branchId=="")
		{
			//getCompanyProductData(getCProductData)
			$productModel = new ProductModel();
			$status = $productModel->getCProductData($companyId);
			if(strcmp($status,$exceptionArray['204'])==0)
			{
				return $status;
			}
			else
			{
				$encoded = new EncodeAllData();
				$encodeAllData = $encoded->getEncodedAllData($status);
				return $encodeAllData;
			}
		}
		else if($companyId=="")
		{
			//getBranchProductData(getBProductData)
			$productModel = new ProductModel();
			$status = $productModel->getBProductData($branchId);
			if(strcmp($status,$exceptionArray['204'])==0)
			{
				return $status;
			}
			else
			{
				
				$encoded = new EncodeAllData();
				$encodeAllData = $encoded->getEncodedAllData($status);
				return $encodeAllData;
			}
			
		}
		else
		{
			//getBranchCompanyProductData(getBCProductData)
			$productModel = new ProductModel();
			$status = $productModel->getBCProductData($companyId,$branchId);
			if(strcmp($status,$exceptionArray['204'])==0)
			{
				return $status;
			}
			else
			{
				$encoded = new EncodeAllData();
				$encodeAllData = $encoded->getEncodedAllData($status);
				return $encodeAllData;
			}
		}
	}
	
	/**
     * get all the data as per given companyId and headerData and call the model for database selection opertation
     * @param companyId & productName
     * @return status
     */
	public function getData($headerData,$companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productModel = new ProductModel();
		$status = $productModel->getProductData($headerData,$companyId);
		
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$encodedAllData = new EncodeAllData();
			$encodeData = $encodedAllData->getEncodedAllData($status);
			return $encodeData;
		}
	}
	
	/**
     * get Document of Perticular Product
     * @return exception-message/data
     */
	public function getProductDocumentData($productId)
	{
		$productModel = new ProductModel();
		$status = $productModel->getSingleProductDocumentData($productId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$encoded = new DocumentEncodeData();
			$encodeAllData = $encoded->getEncodedData($status);
			return $encodeAllData;
		}
	}

	/**
     * get Qunatity Pricing of Perticular Product
     * @return exception-message/data
     */
	public function getProductQuantityPricingData($productId)
	{
		$productModel = new ProductModel();
		$status = $productModel->getSingleProductQuantityPricingData($productId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['204'])==0)
		{
			return $exceptionArray['204'];
		}
		else
		{
			$encoded = new QuantityPricingEncodeData();
			$encodeAllData = $encoded->getEncodedData($status);
			return $encodeAllData;
		}
	}

	/**
     * get all the data as per given headerData and call the model for database selection opertation
     * @param headerData(product-code)
     * @return error-message/array-data
     */
	public function getProductCodeData($headerData)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productModel = new ProductModel();
		$status = $productModel->getProductCodeData($headerData);
		
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$encodedData = new EncodeData();
			$encodeData = $encodedData->getEncodedData($status);
			return $encodeData;
		}
	}
	
	/**
     * get all the data as per given company-id and call the model for database selection opertation
     * @param company-id
     * @return error-message/array-data
     */
	public function getStockSummaryData($companyId)
	{
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productModel = new ProductModel();
		$status = $productModel->getStockSummaryData($companyId);
		
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$encodedAllData = new EncodeAllStockSummaryData();
			$encodeData = $encodedAllData->getEncodedStockSummaryData($status);
			return $encodeData;
		}
	}
	
	/**
     * get the data from persistable object and call the model for database update opertation
     * @param ProductPersistable $persistable
     * @param updateOptions $options [optional]
     * @return status
     */
    public function update()
    {
		$productArray = array();
		$getData = array();
		$funcName = array();
		$documentFlag=0;
		$dataFlag=0;
		$document = array();
		$productArray = func_get_arg(0);
		$headerData = func_get_arg(1);

		if(is_array($productArray[count($productArray)-1][0]))
		{
			$documentCount = count($productArray[count($productArray)-1]);
			//get document data
			for($documentArray=0;$documentArray<$documentCount;$documentArray++)
			{
				$document[$documentArray] = array();
				$document[$documentArray][0] = $productArray[count($productArray)-1][$documentArray][0];
				$document[$documentArray][1] = $productArray[count($productArray)-1][$documentArray][1];
				$document[$documentArray][2] = $productArray[count($productArray)-1][$documentArray][2];
				$document[$documentArray][3] = $productArray[count($productArray)-1][$documentArray][3];
			}
			$documentFlag=1;
		}
		for($data=0;$data<count($productArray);$data++)
		{
			if($documentFlag==1 && $data==(count($productArray)-1))
			{
				break;
			}
			else
			{
				$dataFlag=1;
				$funcName[$data] = $productArray[$data][0]->getName();
				$getData[$data] = $productArray[$data][0]->$funcName[$data]();
				$keyName[$data] = $productArray[$data][0]->getkey();
			}
		}
		$productId = $productArray[0][0]->getProductId();
		
		//data pass to the model object for update
		$productModel = new ProductModel();
		$status = $productModel->updateData($getData,$keyName,$productId,$document);

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($status,$exceptionArray['200'])==0)
		{
			$ProductServiceData = $this->fireWebIntegrationPush($productId,$headerData);
		}
		return $status;
	}

	/**
     * get the data from persistable object and call the model for database update opertation
     * @param ProductPersistable $persistable
     * @param updateOptions $options [optional]
     * @return status
     */
    public function updateBatchData()
    {
		$productArray = array();
		$getData = array();
		$funcName = array();
		$productArray = func_get_arg(0);
		for($data=0;$data<count($productArray);$data++)
		{
			$funcName[$data] = $productArray[$data][0]->getName();
			$getData[$data] = $productArray[$data][0]->$funcName[$data]();
			$keyName[$data] = $productArray[$data][0]->getkey();
		}
		$productId = $productArray[0][0]->getProductId();
		//data pass to the model object for update
		$productModel = new ProductModel();
		$status = $productModel->updateBatchData($getData,$keyName,$productId);
		return $status;
	}
	
	/**
     * get the data from persistable object and call the model for database update opertation
     * @param ProductPersistable $persistable
     * @param updateOptions $options [optional]
	 * parameter is in array form
     * @return status
     */
    public function updateInOutwardData()
    {
		$productArray = array();
		$getData = array();
		$funcName = array();
		$productArray = func_get_arg(0);
		$jfId = func_get_arg(1);
		$inOutWardData = func_get_arg(2);
		$multipleArray = array();
		$singleData = array();
		$arrayFlag=0;
		$flagData=0;
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(array_key_exists($constantArray['flag'],$productArray))
		{
			$flagData=1;
		}
		//only array exists
		if($flagData==1)
		{
			for($innerData=0;$innerData<count($productArray)-1;$innerData++)
			{
				$multipleArray[$innerData] = array();
				$multipleArray[$innerData]['discount']=$productArray[$innerData][0]->getDiscount();
				$multipleArray[$innerData]['discount_value']=$productArray[$innerData][0]->getDiscountValue();
				$multipleArray[$innerData]['discount_type']=$productArray[$innerData][0]->getDiscountType();
				$multipleArray[$innerData]['product_id']=$productArray[$innerData][0]->getProductId();
				$multipleArray[$innerData]['price']=$productArray[$innerData][0]->getPrice();
				$multipleArray[$innerData]['qty']=$productArray[$innerData][0]->getQty();
			}
		}
		else
		{
			for($persistableArray=0;$persistableArray<count($productArray);$persistableArray++)
			{
				// if array is available
				if(is_array($productArray[$persistableArray][0]))
				{
					for($innerData=0;$innerData<count($productArray[$persistableArray]);$innerData++)
					{
						$multipleArray[$innerData] = array();
						$multipleArray[$innerData]['discount']=$productArray[$persistableArray][$innerData][0]->getDiscount();
						$multipleArray[$innerData]['discount_value']=$productArray[$persistableArray][$innerData][0]->getDiscountValue();
						$multipleArray[$innerData]['discount_type']=$productArray[$persistableArray][$innerData][0]->getDiscountType();
						$multipleArray[$innerData]['product_id']=$productArray[$persistableArray][$innerData][0]->getProductId();
						$multipleArray[$innerData]['price']=$productArray[$persistableArray][$innerData][0]->getPrice();
						$multipleArray[$innerData]['qty']=$productArray[$persistableArray][$innerData][0]->getQty();
					}
					
				}
				else
				{
					$funcName = $productArray[$persistableArray][0]->getName();
					$value = $productArray[$persistableArray][0]->$funcName();
					$key = $productArray[$persistableArray][0]->getKey();
					$singleData[$key] = $value;
				}
			}
		}
		if(count($multipleArray)!=0 && count($singleData)!=0)
		{
			$productModel = new ProductModel();
			$status = $productModel->updateArrayData($multipleArray,$singleData,$jfId);
			return $status;
		}
		else if(count($multipleArray)!=0)
		{
			$productModel = new ProductModel();
			$status = $productModel->updateTransactionData($multipleArray,$jfId,$inOutWardData);
			return $status;
		}
		else
		{
			$productModel = new ProductModel();
			$status = $productModel->updateTransactionData($singleData,$jfId,$inOutWardData);
			return $status;
		}
	}
	
	/**
     * Push to Web Integration
     * @param int $productId
     */
    public function fireWebIntegrationPush($productId,$headerData)
    {
    	$settingService= new SettingService();
		$settingData = $settingService->getData();
		$settingData = json_decode($settingData);
		$setting_webIntegration = false;
		$webIntegrationObject = array();

		if(count($settingData) > 0)
		{
			$constantClass = new ConstantClass();
			$constantArray = $constantClass->constantVariable();
			$webIntegrationConstant = $constantClass->webIntegrationConstants();

			$stCount = count($settingData);
			$stIndex = 0;
			while ($stIndex < $stCount) {
				$settingSingleData = $settingData[$stIndex];

				if($settingSingleData->settingType == $constantArray['webIntegrationSetting'])
				{
					if ($settingSingleData->webintegrationStatus == 'on') 
					{
						if ($settingSingleData->webintegrationUserId != 'undefined' && $settingSingleData->webintegrationUserId != '' && $settingSingleData->webintegrationPassword != 'undefined' && $settingSingleData->webintegrationPassword != '' && $settingSingleData->webintegrationPushUrl != 'undefined' && $settingSingleData->webintegrationPushUrl != '') 
						{
							if (filter_var($settingSingleData->webintegrationPushUrl, FILTER_VALIDATE_URL))
							{
								$setting_webIntegration = true;
								$webIntegrationArray = (array) $settingSingleData;
							}
						}
					}
					break;
				}
				$stIndex++;
			}

			if ($setting_webIntegration)
			{
				$authToken = $headerData['authenticationtoken'][0];
				$productData = $this->getProductData($productId);
				$productData = (array) json_decode($productData);
				
				if (count($productData) > 0) {
					if ($productData['webIntegration'] == 'on') {
						$AuthenticateModel = new AuthenticateModel();
						$userData = $AuthenticateModel->getActiveWebUser($authToken);
						
						if (is_array($userData) && !empty($userData)) {
							$userDataArray = (array) $userData[0];
							$returnKey = $this->checkWebAuthentication($userDataArray,$webIntegrationArray);

							if ($returnKey){
								$this->pushWebProduct($productData,$webIntegrationArray,$authToken);
							}
						}
					}
				}
			}
		}
    }

    public function checkWebAuthentication($userArray,$settingData)
    {
    	if (count($userArray) > 0)
    	{
    		if (isset($userArray['web_integration_token']) && isset($userArray['web_integration_expire_datetime']))
    		{
    			if ($userArray['web_integration_token'] == '' || $userArray['web_integration_expire_datetime'] == '0000-00-00 00:00:00') {
    				return $this->getAuthenticate($userArray,$settingData);
    			} else {
    				return true;
    			}
    		}
    		else
    		{
    			return $this->getAuthenticate($userArray,$settingData);
    		}
    	}

    	return false;
    }

    public function getAuthenticate($userArray,$settingData)
    {
    	$constantClass = new ConstantClass();
		$webIntegrationConstant = $constantClass->webIntegrationConstants();
		
    	$ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $settingData['webintegrationPushUrl'].$webIntegrationConstant['Authenticate'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'emailId' => $settingData['webintegrationUserId'],
                'password' => $settingData['webintegrationPassword']
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        $output = curl_exec($ch);
        curl_close($ch);

        $tokenData = json_decode($output);
        if ($tokenData != ''){
        	if (isset($tokenData->token)){
        		$tokenArray = (array) $tokenData;
        		$tokenAuthentication = new TokenAuthentication();
        		$returnData = $tokenAuthentication->webAuthentication($tokenArray,$userArray);
        		return $returnData;
        	}
        }
        return false;

    }

    public function http_build_query_for_curl( $arrays, &$new = array(), $prefix = null ) {

	    if ( is_object( $arrays ) ) {
	        $arrays = get_object_vars( $arrays );
	    }

	    foreach ( $arrays AS $key => $value ) {
	        $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
	        if ( is_array( $value ) OR is_object( $value )  ) {
	            $this->http_build_query_for_curl( $value, $new, $k );
	        } else {
	            $new[$k] = $value;
	        }
	    }
	}

    public function pushWebProduct($productData,$settingData,$authToken)
    {
    	$oneTimeAuth = 0; // Check Auth when token expire or invalid
    	//Load Constant
    	$constantClass = new ConstantClass();
		$webIntegrationConstant = $constantClass->webIntegrationConstants();

		/* Get User Data */
			$AuthenticateModel = new AuthenticateModel();
			$userData = $AuthenticateModel->getActiveWebUser($authToken);
			$userDataArray = array();

			if (is_array($userData) && !empty($userData)) 
			{
				$userDataArray = (array) $userData[0];
			}
		/* End User */

		if (count($userDataArray) > 0)
		{
			if ($userDataArray['web_integration_token'] != '')
			{
		    	$productArray = $productData;
		    	$documentData = $this->getProductDocumentData($productArray['productId']);
		    	$documentData = json_decode($documentData);
		    	if (is_array($documentData) && !empty($productArray)) {
		    		$productArray['document'] = (array) $documentData;
		    	}
		    	
		    	$productArray = json_decode(json_encode($productArray), true);
		    	$this->http_build_query_for_curl( $productArray, $post );

		    	$ch = curl_init();
		        curl_setopt_array($ch, [
		            CURLOPT_URL => $settingData['webintegrationPushUrl'].$webIntegrationConstant['Product'],
		            CURLOPT_POST => true,
		            CURLOPT_HTTPHEADER => array(
		            	"Content-type: multipart/form-data",
		            	"authenticationtoken: ".$userDataArray['web_integration_token']
		            ),
		            CURLOPT_POSTFIELDS => $post,
		            CURLOPT_RETURNTRANSFER => true
		        ]);
		        
		        $output = curl_exec($ch);

		        curl_close($ch);

		        $productResponse = json_decode($output);

		        if ($productResponse != '') {
		        	if (@$productResponse->code == '401') {
		        		if (!$oneTimeAuth)
		        		{
		        			$oneTimeAuth = 1;
			        		if($this->getAuthenticate($userDataArray,$settingData)) {
			        			$this->pushWebProduct($productData,$settingData,$authToken);
			        		}
		        		}
		        	}
		        }
		    }
	    }
    }
    public function insertInOutwardItemizeData($batchData)
    {
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if (empty($batchData) || count($batchData) == 0) {
			return $exceptionArray['content'];
		}
		//data pass to the model object for insert
		$productModel = new ProductModel();
		$status = $productModel->insertItemizeTrnDtl($batchData);
		return $status;
    }
    public function updateInOutwardItemizeData()
    {

    }

    public function getItemizeStockSummary($productId)
    {
    	//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$productModel = new ProductModel();
		$status = $productModel->getItemizeStockSummaryData($productId);
		
		if(strcmp($status,$exceptionArray['404'])==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$encodedAllData = new EncodeAllItemizeSummaryData();
			$encodedData = $encodedAllData->getEncodedItemizeSummaryData($status);
			return $encodedData;
		}
    }

    /**
     * get and invoke method is of Container Interface method
     * @param int $id,$name
     */
    public function get($id,$name)
    {
		echo "get";		
    } 

	public function invoke(callable $method)
	{
		echo "invoke";
	}

    /**
     * @param int $id
     */
    public function delete($productId,$requestHeader)
    {    
		// $productId = $persistable->getProductId();
        $productModel = new ProductModel();
		$status = $productModel->deleteData($productId,$requestHeader);
		return $status;
    }   
}