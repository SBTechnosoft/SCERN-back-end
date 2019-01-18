<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait LedgerGrpIdPropertyTrait
{
	/**
     * @var ledgerGrpId
     */
    private $ledgerGrpId;
	/**
	 * @param int $ledgerGrpId
	 */
	public function setLedgerGroupId($ledgerGrpId)
	{
		$this->ledgerGrpId = $ledgerGrpId;
	}
	/**
	 * @return ledgerGrpId
	 */
	public function getLedgerGroupId()
	{
		return $this->ledgerGrpId;
	}
}