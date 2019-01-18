<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait FromDatePropertyTrait
{
	/**
     * @var $fromDate
     */
    private $fromDate;
	
	/**
	 * @param int $fromDate
	 */
	public function setFromDate($fromDate)
	{
		$this->fromDate = $fromDate;
	}
	/**
	 * @return fromDate
	 */
	public function getFromDate()
	{
		return $this->fromDate;
	}
}