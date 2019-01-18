<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PaymentModePropertyTrait
{
	/**
     * @var paymentMode
     */
    private $paymentMode;
	/**
	 * @param int $paymentMode
	 */
	public function setPaymentMode($paymentMode)
	{
		$this->paymentMode = $paymentMode;
	}
	/**
	 * @return paymentMode
	 */
	public function getPaymentMode()
	{
		return $this->paymentMode;
	}
}