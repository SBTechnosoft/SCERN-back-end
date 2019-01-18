<?php
namespace ERP\Core\Settings\Expenses\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ExpenseTypeTrait
{
	/**
     * @var expenseType
     */
    private $expenseType;
	/**
	 * @param string $expenseType
	 */
	public function setExpenseType($expenseType)
	{
		$this->expenseType = $expenseType;
	}
	/**
	 * @return expenseType
	 */
	public function getExpenseType()
	{
		return $this->expenseType;
	}
}