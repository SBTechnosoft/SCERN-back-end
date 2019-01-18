<?php
namespace ERP\Core\Authenticate\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait WebTokenPropertyTrait
{
	/**
     * @var webIntegrationToken
     */
    private $webIntegrationToken;
    
	/**
	 * @param int $webIntegrationToken
	 */
	public function setWebIntegrationToken($webIntegrationToken)
	{
		$this->webIntegrationToken = $webIntegrationToken;
	}

	/**
	 * @return webIntegrationToken
	 */
	public function getWebIntegrationToken()
	{
		return $this->webIntegrationToken;
	}
}