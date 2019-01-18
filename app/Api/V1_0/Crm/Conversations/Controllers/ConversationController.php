<?php
namespace ERP\Api\V1_0\Crm\Conversations\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Crm\Conversations\Services\ConversationService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Crm\Conversations\Processors\ConversationProcessor;
use ERP\Core\Crm\Conversations\Persistables\ConversationPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Entities\Constants\ConstantClass;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ConversationController extends BaseController implements ContainerInterface
{
	/**
     * @var conversationService
     * @var processor
     * @var request
     * @var conversationPersistable
     */
	private $conversationService;
	private $processor;
	private $request;
	private $conversationPersistable;	
	
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
    public function storeEmail(Request $request)
    {
    	//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$commentMessage = $constantClass->getCommentMessage();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;

			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == 'POST')
			{
				$processor = new ConversationProcessor();
				$conversationPersistable = new ConversationPersistable();
				$conversationService= new ConversationService();
				$conversationType=$constantArray['conversationEmailType'];

				$conversationPersistable = $processor->createPersistable($this->request,$conversationType,$commentMessage->crmMailSend,$commentMessage->crmSmsSend);
				
				if(is_array($conversationPersistable))
				{
					$status='';
					if(array_key_exists('clientSuccessData',$conversationPersistable))
					{
						$status = $conversationService->insert($conversationPersistable['clientSuccessData'],$conversationType,$request->header());
					}
					if(array_key_exists('clientFailData',$conversationPersistable))
					{
						return $conversationPersistable['clientFailData'];
					}
					else if($status!='')
					{
						return $status;
					}
				}
				else
				{
					return $conversationPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	public function storeEmailFromReminder(Request $request)
	{
		$this->request = $request;
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$commentMessage = $constantClass->getCommentMessage();
		$processor = new ConversationProcessor();
		$conversationPersistable = new ConversationPersistable();
		$conversationService= new ConversationService();
		$conversationType=$constantArray['conversationEmailType'];
		$conversationPersistable = $processor->createPersistable($this->request,$conversationType,$commentMessage->reminderMailSend,$commentMessage->reminderSmsSend);
		if(is_array($conversationPersistable))
		{
			$status='';
			if(array_key_exists('clientSuccessData',$conversationPersistable))
			{
				$status = $conversationService->insert($conversationPersistable['clientSuccessData'],$conversationType,$request->header());
			}
			if(array_key_exists('clientFailData',$conversationPersistable))
			{
				return $conversationPersistable['clientFailData'];
			}
			else if($status!='')
			{
				return $status;
			}
		}
		else
		{
			return $conversationPersistable;
		}
	}

	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function storeSmsForReminder(Request $request)
    {
    	//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$commentMessage = $constantClass->getCommentMessage();
		$this->request = $request;
		
		$processor = new ConversationProcessor();
		$conversationPersistable = new ConversationPersistable();
		$conversationService= new ConversationService();
		$conversationType=$constantArray['conversationSmsType'];
		$conversationPersistable = $processor->createPersistable($this->request,$conversationType,$commentMessage->reminderMailSend,$commentMessage->reminderSmsSend);
		if(is_array($conversationPersistable))
		{
			$status='';
			if(array_key_exists('clientSuccessData',$conversationPersistable))
			{
				$status = $conversationService->insert($conversationPersistable['clientSuccessData'],$conversationType,$request->header());
			}
			if(array_key_exists('clientFailData',$conversationPersistable))
			{
				return $conversationPersistable['clientFailData'];
			}
			else if($status!='')
			{
				return $status;
			}
		}
		else
		{
			return $conversationPersistable;
		}
		
	}

	/**
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function storeSms(Request $request)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$commentMessage = $constantClass->getCommentMessage();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == 'POST')
			{
				$processor = new ConversationProcessor();
				$conversationPersistable = new ConversationPersistable();
				$conversationService= new ConversationService();
				$conversationType=$constantArray['conversationSmsType'];
				$conversationPersistable = $processor->createPersistable($this->request,$conversationType,$commentMessage->crmMailSend,$commentMessage->crmSmsSend);
				if(is_array($conversationPersistable))
				{
					$status='';
					if(array_key_exists('clientSuccessData',$conversationPersistable))
					{
						$status = $conversationService->insert($conversationPersistable['clientSuccessData'],$conversationType,$request->header());
					}
					if(array_key_exists('clientFailData',$conversationPersistable))
					{
						return $conversationPersistable['clientFailData'];
					}
					else if($status!='')
					{
						return $status;
					}
				}
				else
				{
					return $conversationPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
}
