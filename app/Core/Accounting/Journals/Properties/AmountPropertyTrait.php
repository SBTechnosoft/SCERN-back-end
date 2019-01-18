<?php
namespace ERP\Core\Accounting\Journals\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AmountPropertyTrait
{
	/**
     * @var $amount
     */
    public $amount;
	
	/**
	 * @param int $amount
	 */
	public function setAmount($amount)
	{
		$this->amount = $amount;
	}
	/**
	 * @return $amount
	 */
	public function getAmount()
	{
		return $this->amount;
	}
}