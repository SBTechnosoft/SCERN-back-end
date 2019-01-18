<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ToDatePropertyTrait
{
	/**
     * @var toDate
     */
    private $toDate;
	/**
	 * @param float $toDate
	 */
	public function setToDate($toDate)
	{
		$this->toDate = $toDate;
	}
	/**
	 * @return toDate
	 */
	public function getToDate()
	{
		return $this->toDate;
	}
}