<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AmountPropertyTrait
{
	/**
     * @var amount
     */
    private $amount;
	/**
	 * @param float $amount
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;
	}
	/**
	 * @return amount
	 */
	public function getAmount()
	{
		return $this->amount;
	}
}