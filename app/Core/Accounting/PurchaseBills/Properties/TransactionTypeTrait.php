<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TransactionTypeTrait
{
	/**
     * @var transactionType
     */
    private $transactionType;
	/**
	 * @param int $transactionType
	 */
	public function setTransactionType($transactionType)
	{
		$this->transactionType = $transactionType;
	}
	/**
	 * @return transactionType
	 */
	public function getTransactionType()
	{
		return $this->transactionType;
	}
}