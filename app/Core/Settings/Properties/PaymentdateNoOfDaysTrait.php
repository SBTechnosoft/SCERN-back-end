<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PaymentdateNoOfDaysTrait
{
	/**
     * @var paymentNoOfDays
     */
    private $paymentNoOfDays;
	/**
	 * @param int $paymentNoOfDays
	 */
	public function setPaymentdateNoOfDays($paymentNoOfDays)
	{
		$this->paymentNoOfDays = $paymentNoOfDays;
	}
	/**
	 * @return paymentNoOfDays
	 */
	public function getPaymentdateNoOfDays()
	{
		return $this->paymentNoOfDays;
	}
}