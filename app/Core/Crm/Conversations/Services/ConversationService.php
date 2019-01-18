<?php
namespace ERP\Core\Crm\Conversations\Services;

use ERP\Model\Crm\Conversations\ConversationModel;
use ERP\Core\Shared\Options\UpdateOptions;
use ERP\Core\Support\Service\AbstractService;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Authenticate\AuthenticateModel;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ConversationService extends AbstractService
{
    /**
     * @var conversationService
	 * $var conversationModel
     */
    private $conversationService;
    private $conversationModel;
	
    /**
     * @param ConversationService $conversationService
     */
    public function initialize(ConversationService $conversationService)
    {		
		echo "init";
    }
	
    /**
     * @param ConversationPersistable $persistable
     */
    public function create(ConversationPersistable $persistable)
    {
		return "create method of ConversationService";
		
    }
	
	 /**
     * get the data from persistable object and call the model for database insertion opertation
     * @param ConversationPersistable $persistable
     * @return status
     */
	public function insert()
	{
		$conversationArray = array();
		$getData = array();
		$keyName = array();
		$funcName = array();
		$conversationArray = func_get_arg(0);
		$conversationType= func_get_arg(1);
		$headerData= func_get_arg(2);
		$documentFlag=0;
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		//get user-id
		$authenticationModel = new AuthenticateModel();
		$userData = $authenticationModel->getActiveUser($headerData);
		if(!is_array($userData))
		{
			if(strcmp($exceptionArray['userLogin'],$userData)==0)
			{
				return $exceptionArray['userLogin'];
			}
		}
		$conversationCount = count($conversationArray);
		for($data=0;$data<$conversationCount;$data++)
		{
			$document = array();
			if(array_key_exists('document',$conversationArray[$data]))
			{
				if($data==0)
				{
					$innerArrayCount = count($conversationArray[$data])-1;
					$document = $conversationArray[$data]['document'];
				}
			}
			else
			{
				$innerArrayCount = count($conversationArray[$data]);
			}
			for($innerArray=0;$innerArray<$innerArrayCount;$innerArray++)
			{
				$funcName[$data][$innerArray] = $conversationArray[$data][$innerArray][0]->getName();
				$getData[$data][$innerArray] = $conversationArray[$data][$innerArray][0]->$funcName[$data][$innerArray]();
				$keyName[$data][$innerArray] = $conversationArray[$data][$innerArray][0]->getkey();
			}
		}
		// data pass to the model object for insert
		$conversationModel = new ConversationModel();
		if(strcmp($conversationType,'email')==0)
		{
			$status = $conversationModel->insertEmailData($getData,$keyName,$document,$userData[0]->user_id);
		}
		else
		{
			$status = $conversationModel->insertSmsData($getData,$keyName,$document,$userData[0]->user_id);
		}
		return $status;
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
}