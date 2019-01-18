<?php
namespace ERP\Core\Users\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait UserTypePropertyTrait
{
	/**
     * @var userType
     */
    private $userType;
	/**
	 * @param int $userType
	 */
	public function setUserType($userType)
	{
		$this->userType = $userType;
	}
	/**
	 * @return userType
	 */
	public function getUserType()
	{
		return $this->userType;
	}
}