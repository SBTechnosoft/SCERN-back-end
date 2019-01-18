<?php
namespace ERP\Core\Crm\JobForm\Properties;

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
	 * @param int $additionalTax
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