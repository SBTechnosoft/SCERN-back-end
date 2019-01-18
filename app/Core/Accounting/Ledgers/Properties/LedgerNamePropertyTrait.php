<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait LedgerNamePropertyTrait
{
	/**
     * @var ledgerName
     */
    private $ledgerName;
	/**
	 * @param int $ledgerName
	 */
	public function setLedgerName($ledgerName)
	{
		$this->ledgerName = $ledgerName;
	}
	/**
	 * @return ledgerName
	 */
	public function getLedgerName()
	{
		return $this->ledgerName;
	}
}