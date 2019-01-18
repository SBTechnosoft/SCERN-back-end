<?php
namespace ERP\Core\Users\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PasswordPropertyTrait
{
	/**
     * @var password
     */
    private $password;
	/**
	 * @param int $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}
	/**
	 * @return password
	 */
	public function getPassword()
	{
		return $this->password;
	}
}