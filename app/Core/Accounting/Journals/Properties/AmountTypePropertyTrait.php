<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AmountTypePropertyTrait
{
	/**
     * @var $amountType
     */
    public $amountType;
	
	/**
	 * @param int $amountType
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