<?php
namespace ERP\Core\Shared\Properties;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TransactionDatePropertyTrait
{
    /**
     * @var Date $transactionDate
     */
    private $transactionDate;

    /**
     * @param Date $transactionDate
     */
    public function setTransactionDate($transactionDate)
    {
        $this->transactionDate = $transactionDate;
    }

    /**
     * @return Date
     */
    public function getTransactionDate()
    {
        return $this->transactionDate;
    }
}