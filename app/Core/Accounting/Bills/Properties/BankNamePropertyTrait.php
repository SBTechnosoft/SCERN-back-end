<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BankNamePropertyTrait
{
	/**
     * @var bankName
     */
    private $bankName;
	/**
	 * @param string $bankName
	 */
	public function setBankName($bankName)
	{
		$this->bankName = $bankName;
	}
	/**
	 * @return bankName
	 */
	public function getBankName()
	{
		return $this->bankName;
	}
}