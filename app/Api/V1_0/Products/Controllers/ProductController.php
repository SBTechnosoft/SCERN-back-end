<?php
namespace ERP\Api\V1_0\Products\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Products\Services\ProductService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Products\Processors\ProductProcessor;
use ERP\Core\Products\Persistables\ProductPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Products\ProductModel;
use ERP\Entities\Constants\ConstantClass;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Core\Products\Entities\StockManageMpdf;
use ERP\Core\Products\Entities\PriceListMpdf;
use DB;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductController extends BaseController implements ContainerInterface
{
	/**
     * @var productService
     * @var processor
     * @var name
     * @var request
     * @var productPersistable
     */
	private $productService;
	private $processor;
	private $request;
	private $productPersistable;	
	
	/**
	 * get and invoke method is of ContainerInterface method
	 */		
    public function get($id,$name)
	{
		// echo "get";
	}
	public function invoke(callable $method)
	{
		// echo "invoke";
	}
	
	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function store(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == $constantArray['postMethod'])
			{
				$processor = new ProductProcessor();
				$productPersistable = new ProductPersistable();		
				$productService= new ProductService();		
				$productPersistable = $processor->createPersistable($this->request);
				if($productPersistable[0][0]=='[')
				{
					return $productPersistable;
				}
				else if(is_array($productPersistable))
				{
					$status = $productService->insert($productPersistable,$request->header());
					return $status;
				}
				else
				{
					return $productPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function multipleDataStore(Request $request)
    {
    	//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		// get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == "POST")
			{

				$processor = new ProductProcessor();
				$productPersistable = new ProductPersistable();		
				$productService= new ProductService();

				$productPersistable = $processor->createPersistableBatchData($this->request);
				if(is_array($productPersistable))
				{
					$status = $productService->insertBatchData($productPersistable);
					return $status;
				}
				else
				{
					return $productPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function inwardStore(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == $constantArray['postMethod'])
			{
				$processor = new ProductProcessor();
				$productPersistable = new ProductPersistable();		
				$productService= new ProductService();			
				$inward = $constantArray['journalInward'];
				$productPersistable = $processor->createPersistableInOutWard($this->request,$inward);
				
				if(is_array($productPersistable))
				{
					$status = $productService->insertInOutward($productPersistable);
					return $status;
				}
				else
				{
					return $productPersistable;
				}
				
			}
			else
			{
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function outwardStore(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == $constantArray['postMethod'])
			{
				$processor = new ProductProcessor();
				$productPersistable = new ProductPersistable();		
				$productService= new ProductService();			
				$outward = $constantArray['journalOutward'];
				$productPersistable = $processor->createPersistableInOutWard($this->request,$outward);
				if(is_array($productPersistable))
				{
					$status = $productService->insertInOutward($productPersistable);
					return $status;
				}
				else
				{
					return $productPersistable;
				}
			}
			else
			{
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param  int  $productId
     */
    public function getData(Request $request,$productId=null)
    {
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$param = "";
		if(isset($request->header()['jfid'][0]) && !empty($request->header()['jfid'][0]))
		{
			$param = $request->header()['jfid'][0];
		}
		$uri = "/accounting/journals/".$param;
		$RequestUri = explode("/", $_SERVER['REQUEST_URI']);

		if(strcmp($_SERVER['REQUEST_URI'],$uri)==0 || strcmp($RequestUri[1],"accounting")==0)
		{
			if($productId==null)
			{	
				//get product_transaction data as per given journal-folio id
				if(array_key_exists($constantArray['jfId'],$request->header()))
				{
					$productProcessor= new ProductProcessor();
					$productPersistable = new ProductPersistable();
					$productPersistable = $productProcessor->createJfIdPersistableData($request->header());
					
					$productService= new ProductService();
					$status = $productService->getJfIdProductData($productPersistable);
					return $status;
				}
				//get all product data
				else
				{
					$productService= new ProductService();
					$status = $productService->getAllProductData();
					return $status;
				}
			}
			else
			{	
				$productService= new ProductService();
				$status = $productService->getProductData($productId);
				return $status;
			} 
		}
		else
		{
			//Authentication
			$tokenAuthentication = new TokenAuthentication();
			$authenticationResult = $tokenAuthentication->authenticate($request->header());
			if(strcmp($constantArray['success'],$authenticationResult)==0)
			{
				if($productId==null)
				{	
					//get product_transaction data as per given journal-folio id
					if(array_key_exists($constantArray['jfId'],$request->header()))
					{
						$productProcessor= new ProductProcessor();
						$productPersistable = new ProductPersistable();
						$productPersistable = $productProcessor->createJfIdPersistableData($request->header());
						
						$productService= new ProductService();
						$status = $productService->getJfIdProductData($productPersistable);
						return $status;
					}
					else if(array_key_exists($constantArray['productCode'],$request->header()))
					{
						$productService= new ProductService();
						$status = $productService->getProductCodeData($request->header());
						return $status;
					}
					//get all product data
					else
					{
						$productService= new ProductService();
						$status = $productService->getAllProductData();
						return $status;
					}
				}
				else
				{	
					$productService= new ProductService();
					$status = $productService->getProductData($productId);
					return $status;
				} 
			}
			else
			{
				return $authenticationResult;
			}	
		}
    }
	
	/**
     * get the specified Product Document.
     * @param $productId and $branchId
     */
    public function getProductDocumentData(Request $request,$productId)
    {
    	//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();

		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$productService= new ProductService();
			$status = $productService->getProductDocumentData($productId);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}

	/**
     * get the specified Product Quantity Pricing.
     * @param $productId and $branchId
     */
    public function getProductQuantityPricingData(Request $request,$productId)
    {
    	//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$productService= new ProductService();
			$status = $productService->getProductQuantityPricingData($productId);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}

	/**
     * get the specified resource.
     * @param $productId and $branchId
     */
    public function getAllProductData(Request $request,$companyId=null,$branchId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($branchId=="" && $companyId=="")
			{	
				$productService= new ProductService();
				$status = $productService->getAllProductData();
				return $status;
			}
			else if($branchId=="" || $companyId=="")
			{
				if($branchId=="")	
				{
					$productService= new ProductService();
					$status = $productService->getCBProductData($branchId,$companyId);
					return $status;
				}
				else
				{
					$productService= new ProductService();
					$status = $productService->getCBProductData($branchId,$companyId);
					return $status;
				}
			}
			else
			{	
				$productService= new ProductService();
				$status = $productService->getCBProductData($branchId,$companyId);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param $companyId
     */
    public function getProductData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			$productService= new ProductService();
			$status = $productService->getData($request->header(),$companyId);
			return $status;	
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param $companyId
     */
    public function getStockDocumentPath(Request $request,$companyId)
    {
		$result = $this->getProductTransactionData($request,$companyId);
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['204'])==0)
		{
			return $result;
		}
		else
		{
			
			$stockManageMpdf = new StockManageMpdf();
			if(strcmp($request->header()['operation'][0],'pdf')==0)
			{
				$mpdfResult = $stockManageMpdf->calculateBalance($result);
			}
			else
			{
				$mpdfResult = $stockManageMpdf->generateExcelFile($result);
			}
			return $mpdfResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param $companyId and request object
     */
    public function getPriceListDocumentPath(Request $request,$companyId)
    {
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$result = $this->getProductData($request,$companyId);
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0)
		{
			return $result;
		}
		else
		{
			$priceListMpdf = new PriceListMpdf();
			if(strcmp($constantArray['operation'],$request->header()['operation'][0])==0)
			{
				$mpdfResult = $priceListMpdf->generatePdf($request->header(),$result);
			}
			else if (strcmp($constantArray['operationExcel'],$request->header()['operation'][0])==0)
			{
				$mpdfResult = $priceListMpdf->generateExcelFile($request->header(),$result);
			}
			else
			{
				return $exceptionArray['404'];
			}
			return $mpdfResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param $companyId and request object
     */
    public function getProductTransactionData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			// get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			if(array_key_exists($constantArray['fromDate'],$request->header()) && array_key_exists($constantArray['toDate'],$request->header()))
			{	
				$productPersistable = new ProductPersistable();
				$productProcessor = new ProductProcessor();
				$productPersistable = $productProcessor->createprocessDatePersistableData($request->header());
				
				$productService= new ProductService();
				$status = $productService->getProductTransactionData($productPersistable,$request->header(),$companyId);
				return $status;
			}
			else 
			{
				return $exceptionArray['content'];
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param $companyId and request object
     */
    public function getStockSummaryData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$productService= new ProductService();
			$status = $productService->getStockSummaryData($companyId);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * Update the specified resource in storage.
     * @param  Request object[Request $request]
     */
	public function update(Request $request,$productId)
    {  
    	// print_r(json_decode($request->getContent()));
    	// print_r($request->input());
    	// exit;

    	//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			$processor = new ProductProcessor();
			$productPersistable = new ProductPersistable();		
			$productService= new ProductService();			
			$productModel = new ProductModel();

			$result = $productModel->getData($productId);

			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($result,$exceptionArray['404'])==0)
			{
				return $exceptionArray['404'];
			}
			else
			{
				$productPersistable = $processor->createPersistableChange($this->request,$productId,$result);
				if(is_array($productPersistable))
				{
					$status = $productService->update($productPersistable,$request->header());
					return $status;
				}
				else
				{
					return $productPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}

	/**
     * Update the specified resource in storage.
     * @param  Request object[Request $request]
     */
	public function batchUpdate(Request $request)
    {   
    	//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			$processor = new ProductProcessor();
			$productPersistable = new ProductPersistable();		
			$productService= new ProductService();			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
				$productPersistable = $processor->createPersistableBatchUpdateChange($this->request);
				if(is_array($productPersistable))
				{
					$status = $productService->updateBatchData($productPersistable);
					return $status;
				}
				else
				{
					return $productPersistable;
				}
			
		}
		else
		{
			return $authenticationResult;
		}
	}
	

    /**
     * Remove the specified resource from storage.
     * @param  Request object[Request $request]     
     */
    public function Destroy(Request $request,$productId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			$processor = new ProductProcessor();
			$productPersistable = new ProductPersistable();		
			$productService= new ProductService();		
			$productModel = new ProductModel();
			$result = $productModel->getData($productId);

			//get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result;
			}
			else
			{		
				$status = $productService->delete($productId,$request->header());
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
