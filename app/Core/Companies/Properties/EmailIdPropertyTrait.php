<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait EmailIdPropertyTrait
{
	/**
     * @var emailId
     */
    private $emailId;
	/**
	 * @param int $emailId
	 */
	public function setEmailId($emailId)
	{
		$this->emailId = $emailId;
	}
	/**
	 * @return emailId
	 */
	public function getEmailId()
	{
		return $this->emailId;
	}
}