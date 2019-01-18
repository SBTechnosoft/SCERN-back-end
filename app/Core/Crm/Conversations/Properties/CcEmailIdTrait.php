<?php
namespace ERP\Core\Crm\Conversations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CcEmailIdTrait
{
	/**
     * @var ccEmailId
     */
    private $ccEmailId;
	/**
	 * @param string $ccEmailId
	 */
	public function setCcEmailId($ccEmailId)
	{
		$this->ccEmailId = $ccEmailId;
	}
	/**
	 * @return ccEmailId
	 */
	public function getCcEmailId()
	{
		return $this->ccEmailId;
	}
}