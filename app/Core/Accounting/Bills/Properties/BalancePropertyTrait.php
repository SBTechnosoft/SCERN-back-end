<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BalancePropertyTrait
{
	/**
     * @var balance
     */
    private $balance;
	/**
	 * @param float $balance
	 */
	public function setBalance($balance)
	{
		$this->balance = $balance;
	}
	/**
	 * @return balance
	 */
	public function getBalance()
	{
		return $this->balance;
	}
}