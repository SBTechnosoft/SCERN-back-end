<?php
namespace ERP\Core\Settings\Expenses\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ExpenseIdTrait
{
	/**
     * @var expenseId
     */
    private $expenseId;
	/**
	 * @param int $expenseId
	 */
	public function setExpenseId($expenseId)
	{
		$this->expenseId = $expenseId;
	}
	/**
	 * @return expenseId
	 */
	public function getExpenseId()
	{
		return $this->expenseId;
	}
}