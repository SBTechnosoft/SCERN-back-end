<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AdditionalTaxPropertyTrait
{
	/**
     * @var additionalTax
     */
    private $additionalTax;
	/**
	 * @param float $additionalTax
	 */
	public function setAdditionalTax($additionalTax)
	{
		$this->additionalTax = $additionalTax;
	}
	/**
	 * @return additionalTax
	 */
	public function getAdditionalTax()
	{
		return $this->additionalTax;
	}
}