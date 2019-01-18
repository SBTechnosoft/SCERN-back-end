<?php
namespace ERP\Core\Accounting\Bills\Properties;

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
	 * @param string $paymentMode
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