<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait bankIdTrait
{
	/**
     * @var bankId
     */
    private $bankId;
	/**
	 * @param int $bankId
	 */
	public function setBankId($bankId)
	{
		$this->bankId = $bankId;
	}
	/**
	 * @return bankId
	 */
	public function getBankId()
	{
		return $this->bankId;
	}
}