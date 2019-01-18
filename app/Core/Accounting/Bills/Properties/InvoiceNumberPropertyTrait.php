<?php
namespace ERP\Core\Accounting\Bills\Properties;

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
	 * @param string $invoiceNumber
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