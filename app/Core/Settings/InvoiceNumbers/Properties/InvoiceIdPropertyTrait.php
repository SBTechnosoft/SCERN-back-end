<?php
namespace ERP\Core\Settings\InvoiceNumbers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait InvoiceIdPropertyTrait
{
	/**
     * @var invoiceId
     */
    private $invoiceId;
	/**
	 * @param int $invoiceId
	 */
	public function setInvoiceId($invoiceId)
	{
		$this->invoiceId = $invoiceId;
	}
	/**
	 * @return invoiceId
	 */
	public function getInvoiceId()
	{
		return $this->invoiceId;
	}
}