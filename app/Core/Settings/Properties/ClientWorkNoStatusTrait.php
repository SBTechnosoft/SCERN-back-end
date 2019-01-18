<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientWorkNoStatusTrait
{
	/**
     * @var workNo
     */
    private $workNo;
	/**
	 * @param int $workNo
	 */
	public function setClientWorkNoStatus($workNo)
	{
		$this->workNo = $workNo;
	}
	/**
	 * @return workNo
	 */
	public function getClientWorkNoStatus()
	{
		return $this->workNo;
	}
}