<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CreditDaysTrait
{
	/**
     * @var creditDays
     */
    private $creditDays;
	/**
	 * @param float $creditDays
	 */
	public function setCreditDays($creditDays)
	{
		$this->creditDays = $creditDays;
	}
	/**
	 * @return creditDays
	 */
	public function getCreditDays()
	{
		return $this->creditDays;
	}
}