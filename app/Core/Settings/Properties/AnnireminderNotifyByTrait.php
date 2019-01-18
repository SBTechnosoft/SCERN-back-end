<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AnnireminderNotifyByTrait
{
	/**
     * @var anniNotifyBy
     */
    private $anniNotifyBy;
	/**
	 * @param string $anniNotifyBy
	 */
	public function setAnnireminderNotifyBy($anniNotifyBy)
	{
		$this->anniNotifyBy = $anniNotifyBy;
	}
	/**
	 * @return anniNotifyBy
	 */
	public function getAnnireminderNotifyBy()
	{
		return $this->anniNotifyBy;
	}
}