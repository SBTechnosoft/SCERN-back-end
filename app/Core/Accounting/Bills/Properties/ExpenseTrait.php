<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ExpenseTrait
{
	/**
     * @var expense
     */
    private $expense;
	/**
	 * @param array $expense
	 */
	public function setExpense($expense)
	{
		$this->expense = $expense;
	}
	/**
	 * @return expense
	 */
	public function getExpense()
	{
		return $this->expense;
	}
}