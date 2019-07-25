<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait AdvanceTaxReadOnlyStatusTrait
{
	/**
     * @var taxReadOnly
     */
    private $taxReadOnly;
	/**
	 * @param string $taxReadOnly
	 */
	public function setAdvanceTaxReadOnlyStatus($taxReadOnly)
	{
		$this->taxReadOnly = $taxReadOnly;
	}
	/**
	 * @return taxReadOnly
	 */
	public function getAdvanceTaxReadOnlyStatus()
	{
		return $this->taxReadOnly;
	}
}