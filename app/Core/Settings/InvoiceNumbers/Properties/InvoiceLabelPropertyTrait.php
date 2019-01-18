<?php
namespace ERP\Core\Settings\InvoiceNumbers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait InvoiceLabelPropertyTrait
{
	/**
     * @var invoiceLabel
     */
    private $invoiceLabel;
	/**
	 * @param String $invoiceLabel
	 */
	public function setInvoiceLabel($invoiceLabel)
	{
		$this->invoiceLabel = $invoiceLabel;
	}
	/**
	 * @return invoiceLabel
	 */
	public function getInvoiceLabel()
	{
		return $this->invoiceLabel;
	}
}