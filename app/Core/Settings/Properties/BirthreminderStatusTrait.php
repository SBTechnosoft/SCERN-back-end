<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BirthreminderStatusTrait
{
	/**
     * @var birthStatus
     */
    private $birthStatus;
	/**
	 * @param int $birthStatus
	 */
	public function setBirthreminderStatus($birthStatus)
	{
		$this->birthStatus = $birthStatus;
	}
	/**
	 * @return birthStatus
	 */
	public function getBirthreminderStatus()
	{
		return $this->birthStatus;
	}
}