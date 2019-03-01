<?php
namespace ERP\Api\V1_0\Settings\MeasurementUnits\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Settings\MeasurementUnits\Services\MeasurementService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Settings\MeasurementUnits\Processors\MeasurementProcessor;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Settings\MeasurementUnits\MeasurementModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class MeasurementController extends BaseController implements ContainerInterface
{
	/**
     * @var MeasurementService
     * @var processor
     * @var request
     * @var MeasurementUnitPersistable
     */
	private $measurementService;
	private $processor;
	private $request;
	private $measurementPersistable;	

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
				$processor = new MeasurementProcessor();
				$measurementService= new MeasurementService();
				$measurementPersistable = $processor->createPersistable($this->request);
				if($measurementPersistable[0][0]=='[')
				{
					return $measurementPersistable;
				}
				else if(is_array($measurementPersistable))
				{
					$status = $measurementService->insert($measurementPersistable);
					return $status;
				}
				else
				{
					return $measurementPersistable;
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
     * @param  int  $measurementUnitId
     */
    public function getData(Request $request,$measurementUnitId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$measurementService= new MeasurementService();
			if($measurementUnitId==null)
			{
				$status = $measurementService->getAllMeasurementData();
				return $status;
			}
			else
			{	
				$status = $measurementService->getMeasurementData($measurementUnitId);
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
     * @param  measurementUnitId
     */
	public function update(Request $request,$measurementUnitId)
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
			$processor = new MeasurementProcessor();
			$measurementModel = new MeasurementModel();
			$result = $measurementModel->getData($measurementUnitId);

			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			if(strcmp($result,$exceptionArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$measurementPersistable = $processor->createPersistableChange($this->request,$measurementUnitId);

				//here two array and string is return at a time
				if(is_array($measurementPersistable))
				{
					// print_r($measurementPersistable);
					$measurementService= new MeasurementService();	
					$status = $measurementService->update($measurementPersistable);
					return $status;
				}
				else
				{
					return $measurementPersistable;
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
     * @param  measurementUnitId     
     */
    public function destroy(Request $request,$measurementUnitId)
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
			$measurementService= new MeasurementService();	
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			$measurementModel = new MeasurementModel();
			$result = $measurementModel->getData($measurementUnitId);

			if(strcmp($result,$exceptionArray['404'])==0)
			{	
				return $result;
			}
			else
			{
				$status = $measurementService->delete($measurementUnitId);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}
