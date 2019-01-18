<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AnnireminderTypeTrait
{
	/**
     * @var anniType
     */
    private $anniType;
	/**
	 * @param string $anniType
	 */
	public function setAnnireminderType($anniType)
	{
		$this->anniType = $anniType;
	}
	/**
	 * @return anniType
	 */
	public function getAnnireminderType()
	{
		return $this->anniType;
	}
}