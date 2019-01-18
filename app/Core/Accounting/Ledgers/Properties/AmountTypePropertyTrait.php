<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AmountTypePropertyTrait
{
	/**
     * @var amountType
     */
    private $amountType;
	/**
	 * @param string $amountType
	 */
	public function setAmountType($amountType)
	{
		$this->amountType = $amountType;
	}
	/**
	 * @return amountType
	 */
	public function getAmountType()
	{
		return $this->amountType;
	}
}