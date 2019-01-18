<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait NoOfDaysTrait
{
	/**
     * @var noOfDays
     */
    private $noOfDays;
	/**
	 * @param int $noOfDays
	 */
	public function setServicedateNoOfDays($noOfDays)
	{
		$this->noOfDays = $noOfDays;
	}
	/**
	 * @return noOfDays
	 */
	public function getServicedateNoOfDays()
	{
		return $this->noOfDays;
	}
}