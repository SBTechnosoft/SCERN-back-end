<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientEmailIdStatusTrait
{
	/**
     * @var emailId
     */
    private $emailId;
	/**
	 * @param int $emailId
	 */
	public function setClientEmailIdStatus($emailId)
	{
		$this->emailId = $emailId;
	}
	/**
	 * @return emailId
	 */
	public function getClientEmailIdStatus()
	{
		return $this->emailId;
	}
}