<?php
namespace ERP\Core\Accounting\Bills\Properties;

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
	 * @param array $userId
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