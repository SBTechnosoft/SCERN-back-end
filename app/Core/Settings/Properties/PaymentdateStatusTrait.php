<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PaymentdateStatusTrait
{
	/**
     * @var paymentStatus
     */
    private $paymentStatus;
	/**
	 * @param int $paymentStatus
	 */
	public function setPaymentdateStatus($paymentStatus)
	{
		$this->paymentStatus = $paymentStatus;
	}
	/**
	 * @return paymentStatus
	 */
	public function getPaymentdateStatus()
	{
		return $this->paymentStatus;
	}
}