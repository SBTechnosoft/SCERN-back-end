<?php
namespace ERP\Core\Settings\QuotationNumbers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait QuotationIdPropertyTrait
{
	/**
     * @var quotationId
     */
    private $quotationId;
	/**
	 * @param int $quotationId
	 */
	public function setQuotationId($quotationId)
	{
		$this->quotationId = $quotationId;
	}
	/**
	 * @return quotationId
	 */
	public function getQuotationId()
	{
		return $this->quotationId;
	}
}