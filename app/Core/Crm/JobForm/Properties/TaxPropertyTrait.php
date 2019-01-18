<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TaxPropertyTrait
{
	/**
     * @var tax
     */
    private $tax;
	/**
	 * @param int $tax
	 */
	public function setTax($tax)
	{
		$this->tax = $tax;
	}
	/**
	 * @return tax
	 */
	public function getTax()
	{
		return $this->tax;
	}
}