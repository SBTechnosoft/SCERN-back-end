<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TransationDatePropertyTrait
{
	/**
     * @var transactionDate
     */
    private $transactionDate;
	/**
	 * @param int $transactionDate
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