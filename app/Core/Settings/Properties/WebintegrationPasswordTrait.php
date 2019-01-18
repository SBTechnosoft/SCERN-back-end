<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait WebintegrationPasswordTrait
{
	/**
     * @var webintegrationPassword
     */
    private $webintegrationPassword;
	/**
	 * @param string $webintegrationPassword
	 */
	public function setWebintegrationPassword($webintegrationPassword)
	{
		$this->webintegrationPassword = $webintegrationPassword;
	}
	/**
	 * @return webintegrationPassword
	 */
	public function getWebintegrationPassword()
	{
		return $this->webintegrationPassword;
	}
}