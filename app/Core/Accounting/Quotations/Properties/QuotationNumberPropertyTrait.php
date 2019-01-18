<?php
namespace ERP\Core\Accounting\Quotations\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait QuotationNumberPropertyTrait
{
	/**
     * @var quotationNumber
     */
    private $quotationNumber;
	/**
	 * @param string $quotationNumber
	 */
	public function setQuotationNumber($quotationNumber)
	{
		$this->quotationNumber = $quotationNumber;
	}
	/**
	 * @return quotationNumber
	 */
	public function getQuotationNumber()
	{
		return $this->quotationNumber;
	}
}