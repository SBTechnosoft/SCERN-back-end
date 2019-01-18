<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CreditLimitTrait
{
	/**
     * @var creditLimit
     */
    private $creditLimit;
	/**
	 * @param float $creditLimit
	 */
	public function setCreditLimit($creditLimit)
	{
		$this->creditLimit = $creditLimit;
	}
	/**
	 * @return creditLimit
	 */
	public function getCreditLimit()
	{
		return $this->creditLimit;
	}
}