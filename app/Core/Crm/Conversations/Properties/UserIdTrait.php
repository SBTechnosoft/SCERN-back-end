<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait UserIdTrait
{
	/**
     * @var userId
     */
    private $userId;
	/**
	 * @param int $userId
	 */
	public function setUserId($userId)
	{
		$this->userId = $userId;
	}
	/**
	 * @return userId
	 */
	public function getUserId()
	{
		return $this->userId;
	}
}