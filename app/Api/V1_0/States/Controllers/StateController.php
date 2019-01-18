<?php
namespace ERP\Api\V1_0\States\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\States\Services\StateService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\States\Processors\StateProcessor;
use ERP\Core\States\Persistables\StatePersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Model\States\StateModel;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class StateController extends BaseController implements ContainerInterface
{
	/**
     * @var stateService
     * @var processor
     * @var request
     * @var statePersistable
     */
	private $stateService;
	private $processor;
	private $request;
	private $statePersistable;	
	
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
			$stateDataFlag=0;
			
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			
			// get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			// insert
			if($requestMethod == 'POST')
			{
				//primary key state is available
				if($this->request->input('stateAbb')=="")
				{
					return $exceptionArray['stateAbb'];
				}
				else
				{
					//check state is exists 
					$stateModel = new StateModel();
					$stateData = $stateModel->getAllData();
					$decodedStateData = json_decode($stateData);
					for($data=0;$data<count($decodedStateData);$data++)
					{
						if(strcmp($decodedStateData[$data]->state_abb,trim($this->request->input('stateAbb')))==0)
						{
							$stateDataFlag=1;
							break;
						}
					}
					//state is exists
					if($stateDataFlag==1)
					{
						return $exceptionArray['stateMatch'];
					}
					else
					{
						$processor = new StateProcessor();
						$statePersistable = new StatePersistable();		
						$stateService= new StateService();			
						$statePersistable = $processor->createPersistable($this->request);
						
						if($statePersistable[0][0]=='[')
						{
							return $statePersistable;
						}
						else if(is_array($statePersistable))
						{
							$status = $stateService->insert($statePersistable);
							return $status;
						}
						else
						{
							return $statePersistable;
						}
					}	
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
     * get the specified resource.
     * @param  state_id
     */
    public function getData(Request $request,$stateId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($stateId==null)
			{			
				$stateService= new StateService();
				$status = $stateService->getAllStateData();
				return $status;
			}
			else
			{	
				$stateService= new StateService();
				$status = $stateService->getStateData($stateId);
				return $status;
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
     * @param  state_abb
     */
	public function update(Request $request,$stateAbb)
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
			$processor = new StateProcessor();
			$statePersistable = new StatePersistable();		
			$stateService= new StateService();
			$stateModel = new StateModel();	
			$result = $stateModel->getData($stateAbb);
			
			// get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $fileSizeArray['404'];
			}
			else
			{
				$statePersistable = $processor->createPersistableChange($this->request,$stateAbb);
				
				if(is_array($statePersistable))
				{
					$status = $stateService->update($statePersistable);
					return $status;
				}
				else
				{
					return $statePersistable;
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
     * @param  state_abb     
     */
    public function Destroy(Request $request,$stateAbb)
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
			$processor = new StateProcessor();
			$statePersistable = new StatePersistable();		
			$stateService= new StateService();	
			
			$stateModel = new StateModel();	
			$result = $stateModel->getData($stateAbb);
			
			// get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $fileSizeArray['404'];
			}
			else
			{		
				$statePersistable = $processor->createPersistableChange($this->request,$stateAbb);
				$status = $stateService->delete($statePersistable);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
