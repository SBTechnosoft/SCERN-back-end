<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait WebintegrationPushUrlTrait
{
	/**
     * @var webintegrationPushUrl
     */
    private $webintegrationPushUrl;
	/**
	 * @param string $webintegrationPushUrl
	 */
	public function setWebintegrationPushUrl($webintegrationPushUrl)
	{
		$this->webintegrationPushUrl = $webintegrationPushUrl;
	}
	/**
	 * @return webintegrationPushUrl
	 */
	public function getWebintegrationPushUrl()
	{
		return $this->webintegrationPushUrl;
	}
}