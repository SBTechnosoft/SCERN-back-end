<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Shaikh Farhan<farhan.s@siliconbrain.in>
 */
trait BankLedgerIdPropertyTrait
{
	/**
     * @var bankLedgerId
     */
    private $bankLedgerId;
	/**
	 * @param string $bankLedgerId
	 */
	public function setBankLedgerId($bankLedgerId)
	{
		$this->bankLedgerId = $bankLedgerId;
	}
	/**
	 * @return bankLedgerId
	 */
	public function getBankLedgerId()
	{
		return $this->bankLedgerId;
	}
}