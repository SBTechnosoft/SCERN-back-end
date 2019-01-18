<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TransactionDateTrait
{
	/**
     * @var transactionDate
     */
    private $transactionDate;
	/**
	 * @param string $transactionDate
	 */
	public function setTransactionDate($transactionDate)
	{
		$this->transactionDate = $transactionDate;
	}
	/**
	 * @return transactionDate
	 */
	public function getTransactionDate()
	{
		return $this->transactionDate;
	}
}