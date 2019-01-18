<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

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
	 * @param int $invoiceNumber
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