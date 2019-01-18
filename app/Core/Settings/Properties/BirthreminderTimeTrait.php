<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BirthreminderTimeTrait
{
	/**
     * @var birthTime
     */
    private $birthTime;
	/**
	 * @param string $birthTime
	 */
	public function setBirthreminderTime($birthTime)
	{
		$this->birthTime = $birthTime;
	}
	/**
	 * @return birthTime
	 */
	public function getBirthreminderTime()
	{
		return $this->birthTime;
	}
}