<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait bankDtlIdTrait
{
	/**
     * @var bankDtlId
     */
    private $bankDtlId;
	/**
	 * @param int $bankDtlId
	 */
	public function setBankDtlId($bankDtlId)
	{
		$this->bankDtlId = $bankDtlId;
	}
	/**
	 * @return bankDtlId
	 */
	public function getBankDtlId()
	{
		return $this->bankDtlId;
	}
}