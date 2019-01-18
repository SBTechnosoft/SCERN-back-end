<?php
namespace ERP\Core\Settings\Expenses\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ExpenseValueTrait
{
	/**
     * @var expenseValue
     */
    private $expenseValue;
	/**
	 * @param float $expenseValue
	 */
	public function setExpenseValue($expenseValue)
	{
		$this->expenseValue = $expenseValue;
	}
	/**
	 * @return expenseValue
	 */
	public function getExpenseValue()
	{
		return $this->expenseValue;
	}
}