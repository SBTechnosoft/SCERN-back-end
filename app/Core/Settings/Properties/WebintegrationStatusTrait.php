<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait WebintegrationStatusTrait
{
	/**
     * @var webintegrationStatus
     */
    private $webintegrationStatus;
	/**
	 * @param string $webintegrationStatus
	 */
	public function setWebintegrationStatus($webintegrationStatus)
	{
		$this->webintegrationStatus = $webintegrationStatus;
	}
	/**
	 * @return webintegrationStatus
	 */
	public function getWebintegrationStatus()
	{
		return $this->webintegrationStatus;
	}
}