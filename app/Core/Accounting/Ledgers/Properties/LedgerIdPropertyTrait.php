<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait LedgerIdPropertyTrait
{
	/**
     * @var ledgerId
     */
    private $ledgerId;
	/**
	 * @param int $ledgerId
	 */
	public function setLedgerId($ledgerId)
	{
		$this->ledgerId = $ledgerId;
	}
	/**
	 * @return ledgerId
	 */
	public function getLedgerId()
	{
		return $this->ledgerId;
	}
}