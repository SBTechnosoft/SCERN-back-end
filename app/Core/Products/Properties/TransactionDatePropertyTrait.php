<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TransactionDatePropertyTrait
{
	/**
     * @var tranDate
     */
    private $tranDate;
	/**
	 * @param Date $tranDate
	 */
	public function setTransactionDate($tranDate)
	{
		$this->tranDate = $tranDate;
	}
	/**
	 * @return tranDate
	 */
	public function getTransactionDate()
	{
		return $this->tranDate;
	}
}