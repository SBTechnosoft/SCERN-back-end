<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BirthreminderNotifyByTrait
{
	/**
     * @var birthNotifyBy
     */
    private $birthNotifyBy;
	/**
	 * @param string $birthNotifyBy
	 */
	public function setBirthreminderNotifyBy($birthNotifyBy)
	{
		$this->birthNotifyBy = $birthNotifyBy;
	}
	/**
	 * @return birthNotifyBy
	 */
	public function getBirthreminderNotifyBy()
	{
		return $this->birthNotifyBy;
	}
}