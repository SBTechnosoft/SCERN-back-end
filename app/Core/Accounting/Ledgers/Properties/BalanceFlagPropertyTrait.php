<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BalanceFlagPropertyTrait
{
	/**
     * @var balanceFlag
     */
    private $balanceFlag;
	/**
	 * @param string $balanceFlag
	 */
	public function setBalanceFlag($balanceFlag)
	{
		$this->balanceFlag = $balanceFlag;
	}
	/**
	 * @return balanceFlag
	 */
	public function getBalanceFlag()
	{
		return $this->balanceFlag;
	}
}