<?php
namespace ERP\Core\Authenticate\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TokenPropertyTrait
{
	/**
     * @var token
     */
    private $token;
	/**
	 * @param int $token
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}
	/**
	 * @return token
	 */
	public function getToken()
	{
		return $this->token;
	}
}