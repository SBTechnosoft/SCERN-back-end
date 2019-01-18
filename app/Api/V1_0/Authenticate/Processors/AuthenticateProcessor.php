<?php
namespace ERP\Api\V1_0\Authenticate\Processors;

use ERP\Api\V1_0\Support\BaseProcessor;
use ERP\Core\Authenticate\Persistables\AuthenticatePersistable;
use Illuminate\Http\Request;
use ERP\Http\Requests;
use Illuminate\Http\Response;
use ERP\Core\Authenticate\Validations\AuthenticateValidate;
use ERP\Api\V1_0\Authenticate\Transformers\AuthenticateTransformer;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Authenticate\AuthenticateModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class AuthenticateProcessor extends BaseProcessor
{
	/**
     * @var request
     */
	private $request;    
	/**
     * get the form-data and set into the persistable object
     * $param Request object [Request $request]
     * @return Authenticate Persistable object
     */	
    public function createPersistable(Request $request)
	{	
		$this->request = $request;
		$stateArray = array();
		$stateValue = array();
		$keyName = array();
		$value = array();
		$data=0;
		
		//get exception message
		$exception = new ExceptionMessage();
		$msgArray = $exception->messageArrays();
		if(count($_POST)==0)
		{
			return $msgArray['204'];
		}
		else
		{
			//trim an input
			$authenticateTransformer = new AuthenticateTransformer();
			$tRequest = $authenticateTransformer->trimInsertData($this->request);
			if($tRequest==1)
			{
				return $msgArray['content'];
			}	
			else
			{
				//check emailId and password exist or not?
				$authenticationValidation = new AuthenticateValidate();
				$validationResult = $authenticationValidation->insertValidate($request,$tRequest);
				if(!is_array($validationResult))
				{
					return $validationResult;
				}
			}
			//check user exist in active_session
			$authenticationModel = new AuthenticateModel();
			$result = $authenticationModel->getData($validationResult['userId']);
			if(strcmp($result,$msgArray['404'])==0)
			{
				//generating an authentication token
				$splitDateTime = explode("-",$validationResult['createdAt']);
				$splitEmailId = explode(".",$validationResult['emailId']);
				$token = $splitDateTime[1].$validationResult['userId'].$validationResult['password'].$splitDateTime[2].$splitEmailId[0].$splitDateTime[0].$splitEmailId[1];
				
				//convert token into md5 format
				$convertedToken = md5($token);	
				
				$authenticationPersistable = new AuthenticatePersistable();
				$authenticationPersistable->setUserId($validationResult['userId']);
				$authenticationPersistable->setToken($convertedToken);
				return $authenticationPersistable;
			}
			else
			{
				$userId =  Array();
				$userId['userId'] = $validationResult['userId'];
				return $userId;
			}
		}		
    }
}