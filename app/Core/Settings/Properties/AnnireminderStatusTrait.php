<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AnnireminderStatusTrait
{
	/**
     * @var anniStatus
     */
    private $anniStatus;
	/**
	 * @param string $anniStatus
	 */
	public function setAnnireminderStatus($anniStatus)
	{
		$this->anniStatus = $anniStatus;
	}
	/**
	 * @return anniStatus
	 */
	public function getAnnireminderStatus()
	{
		return $this->anniStatus;
	}
}