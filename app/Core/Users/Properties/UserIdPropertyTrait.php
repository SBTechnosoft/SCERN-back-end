<?php
namespace ERP\Core\Users\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait UserIdPropertyTrait
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