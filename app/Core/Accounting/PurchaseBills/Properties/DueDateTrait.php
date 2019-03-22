<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait DueDateTrait
{
	/**
     * @var dueDate
     */
    private $dueDate;
	/**
	 * @param string $dueDate
	 */
	public function setDueDate($dueDate)
	{
		$this->dueDate = $dueDate;
	}
	/**
	 * @return dueDate
	 */
	public function getDueDate()
	{
		return $this->dueDate;
	}
}