<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $bankName
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