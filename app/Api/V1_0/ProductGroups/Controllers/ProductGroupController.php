<?php
namespace ERP\Api\V1_0\ProductGroups\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\ProductGroups\Services\ProductGroupService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\ProductGroups\Processors\ProductGroupProcessor;
use ERP\Core\ProductGroups\Persistables\ProductGroupPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\ProductGroups\ProductGroupModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupController extends BaseController implements ContainerInterface
{
	/**
     * @var productGroupService
     * @var processor
     * @var productGroupName
     * @var request
     * @var productGroupPersistable
     */
	private $productGroupService;
	private $processor;
	private $name;
	private $request;
	private $productGroupPersistable;	
	
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
				$productGroupProcessor = new ProductGroupProcessor();
				$productGroupPersistable = new ProductGroupPersistable();		
				$productGroupService= new ProductGroupService();			
				$productGroupPersistable = $productGroupProcessor->createPersistable($this->request);
				if($productGroupPersistable[0][0]=='[')
				{
					return $productGroupPersistable;
				}
				else if(is_array($productGroupPersistable))
				{
					$status = $productGroupService->insert($productGroupPersistable);
					return $status;
				}
				else
				{
					return $productGroupPersistable;
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
				$productGroupProcessor = new ProductGroupProcessor();
				$productGroupPersistable = new ProductGroupPersistable();		
				$productGroupService= new ProductGroupService();			
				$productGroupPersistable = $productGroupProcessor->createPersistableBatchData($this->request);
				
				if(is_array($productGroupPersistable))
				{
					$status = $productGroupService->insertBatchData($productGroupPersistable);
					return $status;
				}
				else
				{
					return $productGroupPersistable;
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
     * @param  int  $companyId
     */
    public function getData(Request $request,$productGroupId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($productGroupId==null)
			{			
				$productGroupService= new productGroupService();
				$status = $productGroupService->getAllproductGrpData();
				return $status;
			}
			else
			{	
				$productGroupService= new ProductGroupService();
				$status = $productGroupService->getproductGrpData($productGroupId);
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
     * @param  int  $companyId
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
			if(isset($request->header()['productgroupid']))
			{	
				$productGroupIds = $request->header()['productgroupid'][0];
				
				if($productGroupIds != '')
				{	
					$productGroupIds = explode(',', $productGroupIds);	
					$productGroupService= new productGroupService();
					$status = $productGroupService->getBulkProductGrpData($productGroupIds);
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
	public function update(Request $request,$productGroupId)
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
			$productGroupProcessor = new ProductGroupProcessor();
			$productGroupPersistable = new ProductGroupPersistable();		
			$productGroupService= new ProductGroupService();		
			$productGroupModel = new ProductGroupModel();	
			$result = $productGroupModel->getData($productGroupId);
			
			//get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result; 
			}
			else
			{
				$productGroupPersistable = $productGroupProcessor->createPersistableChange($this->request,$productGroupId);
				//here two array and string is return at a time
				if(is_array($productGroupPersistable))
				{
					$status = $productGroupService->update($productGroupPersistable);
					return $status;
				}
				else
				{
					return $productGroupPersistable;
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
    public function Destroy(Request $request,$productGroupId)
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
			$processor = new ProductGroupProcessor();
			$productGroupPersistable = new ProductGroupPersistable();		
			$productGroupService= new ProductGroupService();		
			$productGroupModel = new ProductGroupModel();	
			$result = $productGroupModel->getData($productGroupId);
			
			//get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result; 
			}
			else
			{		
				$productGroupPersistable = $processor->createPersistableChange($this->request,$productGroupId);
				$status = $productGroupService->delete($productGroupPersistable);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
