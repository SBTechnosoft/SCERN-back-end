<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ToDatePropertyTrait
{
	/**
     * @var $toDate
     */
    private $toDate;
	
	/**
	 * @param int $toDate
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