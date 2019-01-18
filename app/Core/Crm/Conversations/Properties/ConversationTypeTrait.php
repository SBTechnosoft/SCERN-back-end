<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ConversationTypeTrait
{
	/**
     * @var conversationType
     */
    private $conversationType;
	/**
	 * @param string $conversationType
	 */
	public function setConversationType($conversationType)
	{
		$this->conversationType = $conversationType;
	}
	/**
	 * @return conversationType
	 */
	public function getConversationType()
	{
		return $this->conversationType;
	}
}