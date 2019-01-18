<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait InvoiceNumberPropertyTrait
{
	/**
     * @var invoiceNumber
     */
    private $invoiceNumber;
	/**
	 * @param String $invoiceNumber
	 */
	public function setInvoiceNumber($invoiceNumber)
	{
		$this->invoiceNumber = $invoiceNumber;
	}
	/**
	 * @return invoiceNumber
	 */
	public function getInvoiceNumber()
	{
		return $this->invoiceNumber;
	}
}