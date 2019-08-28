<?php
namespace ERP\Api\V1_0\Merge\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;

use ERP\Core\Products\Services\ProductService;
use ERP\Api\V1_0\Products\Processors\ProductProcessor;
use ERP\Core\Products\Persistables\ProductPersistable;
use ERP\Model\Products\ProductModel;


// use ERP\Api\V1_0\Merge\Processors\MergeProcessor;
use ERP\Core\Merge\Services\MergeService;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class MergeController extends BaseController implements ContainerInterface
{
	/**
     * @var settingService
     * @var processor
     * @var request
     * @var settingPersistable
     */
	private $settingService;
	private $processor;
	private $request;
	private $settingPersistable;	
	
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
	public function mergeProducts(Request $request,$productId)
    {
  		// Authentication
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
			if($requestMethod == 'POST')
			{
				$this->request = $request;
				//get exception message
				$exception = new ExceptionMessage();
				$exceptionArray = $exception->messageArrays();
				$result = $this->updateProductBeforeMerge($this->request,$productId);
				if (strcmp($result, $exceptionArray['200'])==0) {
					$mergeService = new MergeService();
					$products['from_product'] = $productId;
					$products['to_product'] = $request->header('productId');
					$status = $mergeService->mergeProduct($products);
					if (strcmp($status, $exceptionArray['200'])==0) {
						$productService= new ProductService();
						$destoryStatus = $productService->delete($productId,$request->header());
						return $destoryStatus;
					}else{
						return $status;
					}
				}else{
					return $result;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	protected function updateProductBeforeMerge(Request $request,$productId)
	{
		$processor = new ProductProcessor();
		$productPersistable = new ProductPersistable();
		$productService= new ProductService();
		$productModel = new ProductModel();

		$result = $productModel->getData($request->header('productId'));
		$result1 = $productModel->getData($productId);

		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($result,$exceptionArray['404'])==0 || strcmp($result1,$exceptionArray['404'])==0)
		{
			return $exceptionArray['404'];
		}
		else
		{
			$productPersistable = $processor->createPersistableChange($request,$request->header('productId'),$result);
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

	/**
	 * @param companyId
	 * @return status about DB seeder
	 */
	public function mergeLedgers($companyId)
	{
		return '404';
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();


		// Step 1 Create Ledgers for expenses
		$mergeService = new MergeService();
		$status = $mergeService->mergeLedgers($companyId);
		return $status;
	}

	public function fixInventory($inventoryType)
	{
		$mergeService = new MergeService();
		$status = $mergeService->fixInventory($inventoryType);
		return $status;
	}
}
