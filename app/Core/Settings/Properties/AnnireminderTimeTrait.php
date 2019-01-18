<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AnnireminderTimeTrait
{
	/**
     * @var anniTime
     */
    private $anniTime;
	/**
	 * @param string $anniTime
	 */
	public function setAnnireminderTime($anniTime)
	{
		$this->anniTime = $anniTime;
	}
	/**
	 * @return anniTime
	 */
	public function getAnnireminderTime()
	{
		return $this->anniTime;
	}
}