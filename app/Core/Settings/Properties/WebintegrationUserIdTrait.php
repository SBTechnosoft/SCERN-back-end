<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait WebintegrationUserIdTrait
{
	/**
     * @var webintegrationUserId
     */
    private $webintegrationUserId;
	/**
	 * @param string $webintegrationUserId
	 */
	public function setWebintegrationUserId($webintegrationUserId)
	{
		$this->webintegrationUserId = $webintegrationUserId;
	}
	/**
	 * @return webintegrationUserId
	 */
	public function getWebintegrationUserId()
	{
		return $this->webintegrationUserId;
	}
}