<?php
namespace ERP\Api\V1_0\Settings\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Settings\Services\SettingService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Settings\Processors\SettingProcessor;
use ERP\Core\Settings\Persistables\SettingPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Model\Settings\SettingModel;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SettingController extends BaseController implements ContainerInterface
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
	public function store(Request $request)
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
				//get exception message
				// $exception = new ExceptionMessage();
				// $exceptionArray = $exception->messageArrays();
				// $settingModel = new SettingModel();
				// $settingData = $settingModel->getAllData();
				// $settingFlag=0;
				// $exploadedString = array();
				// echo $str = str_replace('_', ' ', array_keys($request->input())[0]);
				// echo " \n";
				// $exploadedString = explode(' ',$str);
				// if(strcmp($settingData,$exceptionArray['204'])!=0)
				// {
				// 	$decodedSettingData = json_decode($settingData);
				// 	foreach ($decodedSettingData as $key => $value) 
				// 	{
				// 		// echo $value->setting_type.'Type';
				// 		// echo " - ";
				// 		// echo $exploadedString[0];
				// 		if(strcmp($value->setting_type,$exploadedString[0])==0)
				// 		{
				// 			$settingFlag=1;
				// 		}
				// 		echo " \n";
				// 	}
				// }
				// echo $settingFlag;
				// exit;
				// if($settingFlag==0)
				// {
					$processor = new SettingProcessor();
					$settingPersistable = new SettingPersistable();		
					$settingService= new SettingService();		
					$settingPersistable = $processor->createPersistable($this->request);
					if($settingPersistable[0][0]=='[')
					{
						return $settingPersistable;
					}
					else if(is_array($settingPersistable))
					{
						$status = $settingService->insert($settingPersistable);
						return $status;
					}
					else
					{
						return $settingPersistable;
					}
				// }
				// else
				// {
				// 	return $exceptionArray['updateSetting'];
				// }
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * update the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
	public function update(Request $request)
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
			if($requestMethod == 'PATCH')
			{
				$requestData = $this->getUpdateRequestData();
				$processor = new SettingProcessor();
				$settingPersistable = new SettingPersistable();		
				$settingService= new SettingService();					
				$settingPersistable = $processor->createPersistableChange($requestData);

				if($settingPersistable[0][0]=='[')
				{
					return $settingPersistable;
				}
				else if(is_array($settingPersistable))
				{
					$status = $settingService->update($settingPersistable);
					return $status;
				}
				else
				{
					return $settingPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
		
	}
	
	/**
	 * get all the data
	 * @param  Request object[Request $request]
	 * method calls the service for getting the data
	*/
	public function getData(Request $request)
    {
		// Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		// get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$settingService= new SettingService();
			$status = $settingService->getData();
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * get update request data (patch call)
	*/
	public function getUpdateRequestData()
	{
		$raw_data = file_get_contents('php://input');
		$boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));
		   	if(empty($boundary))
			{
				parse_str($raw_data,$data);
				return $data;
			}
		    // Fetch each part
			$parts = array_slice(explode($boundary, $raw_data), 1);
			$data = array();
			
		foreach ($parts as $part) 
		{
			// If this is the last part, break
			if ($part == "--\r\n") break; 

			// Separate content from headers
			$part = ltrim($part, "\r\n");
			list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

			// Parse the headers list
			$raw_headers = explode("\r\n", $raw_headers);
			$headers = array();
			foreach ($raw_headers as $header) {
				list($name, $value) = explode(':', $header);
				$headers[strtolower($name)] = ltrim($value, ' '); 
			} 

			// Parse the Content-Disposition to get the field name, etc.
			if (isset($headers['content-disposition'])) 
			{
				$filename = null;
				preg_match('/^(.+); name="([^"]+)"(; filename="([^"]+)")?/',$headers['content-disposition'],$matches);
				list(, $type, $name) = $matches;
				isset($matches[4]) and $filename = $matches[4]; 

				// handle your fields here
				switch ($name) 
				{
					// this is a file upload
					case 'userfile':
						 file_put_contents($filename, $body);
						 break;

					// default for all other files is to populate $data
					default: 
						$data[$name] = substr($body, 0, strlen($body) - 2);
						break;
				} 
			}
		}
		return $data;
	}

	/**
	 * get payment-remaniing data
	*/
	public function getPaymentData(Request $request)
	{
		// Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		// get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			//get exception message
	        $exception = new ExceptionMessage();
	        $exceptionArray = $exception->messageArrays();

			$settingModel= new SettingModel();
			$status = $settingModel->getRemainingPaymentData();
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
}
