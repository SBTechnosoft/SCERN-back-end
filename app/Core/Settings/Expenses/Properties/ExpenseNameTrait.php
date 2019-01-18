<?php
namespace ERP\Core\Settings\Expenses\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ExpenseNameTrait
{
	/**
     * @var expenseName
     */
    private $expenseName;
	/**
	 * @param string $expenseName
	 */
	public function setExpenseName($expenseName)
	{
		$this->expenseName = $expenseName;
	}
	/**
	 * @return expenseName
	 */
	public function getExpenseName()
	{
		return $this->expenseName;
	}
}