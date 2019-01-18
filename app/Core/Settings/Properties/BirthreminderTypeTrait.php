<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BirthreminderTypeTrait
{
	/**
     * @var birthType
     */
    private $birthType;
	/**
	 * @param string $birthType
	 */
	public function setBirthreminderType($birthType)
	{
		$this->birthType = $birthType;
	}
	/**
	 * @return birthType
	 */
	public function getBirthreminderType()
	{
		return $this->birthType;
	}
}