<?php
namespace ERP\Api\V1_0\ProductCategories\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\ProductCategories\Services\ProductCategoryService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\ProductCategories\Processors\ProductCategoryProcessor;
use ERP\Core\ProductCategories\Persistables\ProductCategoryPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\ProductCategories\ProductCategoryModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategoryController extends BaseController implements ContainerInterface
{
	/**
     * @var ProductCategoryService
     * @var processor
     * @var name
     * @var request
     * @var ProductCategoryPersistable
     */
	private $productCategoryService;
	private $processor;
	private $productCategoryName;
	private $request;
	private $productCategoryPersistable;	
	
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
			if($requestMethod == 'POST')
			{
				$processor = new ProductCategoryProcessor();
				$productCategoryPersistable = new ProductCategoryPersistable();		
				$productCategoryService= new ProductCategoryService();			
				$productCategoryPersistable = $processor->createPersistable($this->request);
				if($productCategoryPersistable[0][0]=='[')
				{
					return $productCategoryPersistable;
				}
				else if(is_array($productCategoryPersistable))
				{
					$status = $productCategoryService->insert($productCategoryPersistable);
					return $status;
				}
				else
				{
					return $productCategoryPersistable;
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
    public function multipleDataStore(Request $request)
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
			if($requestMethod == 'POST')
			{
				$processor = new ProductCategoryProcessor();
				$productCategoryPersistable = new ProductCategoryPersistable();		
				$productCategoryService= new ProductCategoryService();			
				$productCategoryPersistable = $processor->createPersistableBatchData($this->request);
				
				if(is_array($productCategoryPersistable))
				{
					$status = $productCategoryService->insertBatchData($productCategoryPersistable);
					return $status;
				}
				else
				{
					return $productCategoryPersistable;
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
     * @param  int  $productCategoryId
     */
    public function getData(Request $request,$productCategoryId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($productCategoryId==null)
			{			
				$productCategoryService= new ProductCategoryService();
				$status = $productCategoryService->getAllProductCatData();
				return $status;
			}
			else
			{	
				$productCategoryService= new ProductCategoryService();
				$status = $productCategoryService->getProductCatData($productCategoryId);
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
     * @param  int  $productCategoryId
     */
    public function getBulkData(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if(isset($request->header()['productcategoryid']))
			{	
				$productCategoryIds = $request->header()['productcategoryid'][0];
				
				if($productCategoryIds != '')
				{
					$productCategoryIds = explode(',', $productCategoryIds);
					$productCategoryService= new ProductCategoryService();
					$status = $productCategoryService->getBulkProductCatData($productCategoryIds);
					return $status;
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
	public function update(Request $request,$productCategoryId)
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
			$processor = new ProductCategoryProcessor();
			$productCategoryPersistable = new ProductCategoryPersistable();		
			$productCategoryService= new ProductCategoryService();	
			$productCategoryModel = new ProductCategoryModel();		
			$result = $productCategoryModel->getData($productCategoryId);
			
			// get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$productCategoryPersistable = $processor->createPersistableChange($this->request,$productCategoryId);
				if(is_array($productCategoryPersistable))
				{
					$status = $productCategoryService->update($productCategoryPersistable);
					return $status;
				}
				else
				{
					return $productCategoryPersistable;
				}
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
    public function Destroy(Request $request,$productCategoryId)
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
			$processor = new ProductCategoryProcessor();
			$productCategoryPersistable = new ProductCategoryPersistable();		
			$productCategoryService= new ProductCategoryService();			
			$productCategoryModel = new ProductCategoryModel();		
			$result = $productCategoryModel->getData($productCategoryId);
			
			// get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$productCategoryPersistable = $processor->createPersistableChange($this->request,$productCategoryId);
				$status = $productCategoryService->delete($productCategoryPersistable);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
