<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ConversationTrait
{
	/**
     * @var conversation
     */
    private $conversation;
	/**
	 * @param string $conversation
	 */
	public function setConversation($conversation)
	{
		$this->conversation = $conversation;
	}
	/**
	 * @return conversation
	 */
	public function getConversation()
	{
		return $this->conversation;
	}
}