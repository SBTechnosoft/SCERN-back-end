<?php
namespace ERP\Core\Settings\Expenses\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait ExpenseGroupTypeTrait
{
	/**
     * @var expenseGroupType
     */
    private $expenseGroupType;
	/**
	 * @param float $expenseGroupType
	 */
	public function setExpenseGroupType($expenseGroupType)
	{
		$this->expenseGroupType = $expenseGroupType;
	}
	/**
	 * @return expenseGroupType
	 */
	public function getExpenseGroupType()
	{
		return $this->expenseGroupType;
	}
}