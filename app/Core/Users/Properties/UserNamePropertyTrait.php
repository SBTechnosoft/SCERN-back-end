<?php
namespace ERP\Core\Users\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait UserNamePropertyTrait
{
	/**
     * @var userName
     */
    private $userName;
	/**
	 * @param int $userName
	 */
	public function setUserName($userName)
	{
		$this->userName = $userName;
	}
	/**
	 * @return userName
	 */
	public function getUserName()
	{
		return $this->userName;
	}
}