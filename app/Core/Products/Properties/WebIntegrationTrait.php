<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait WebIntegrationTrait
{
	/**
     * @var webIntegration
     */
    private $webIntegration;
	/**
	 * @param Enum $webIntegration
	 */
	public function setWebIntegration($webIntegration)
	{
		$this->webIntegration = $webIntegration;
	}
	/**
	 * @return webIntegration
	 */
	public function getWebIntegration()
	{
		return $this->webIntegration;
	}
}