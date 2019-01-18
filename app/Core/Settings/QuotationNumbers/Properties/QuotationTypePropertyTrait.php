<?php
namespace ERP\Core\Settings\QuotationNumbers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait QuotationTypePropertyTrait
{
	/**
     * @var quotationType
     */
    private $quotationType;
	/**
	 * @param String $quotationType
	 */
	public function setQuotationType($quotationType)
	{
		$this->quotationType = $quotationType;
	}
	/**
	 * @return quotationType
	 */
	public function getQuotationType()
	{
		return $this->quotationType;
	}
}