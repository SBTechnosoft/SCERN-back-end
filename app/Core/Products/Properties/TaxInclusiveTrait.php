<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait TaxInclusiveTrait
{
	/**
     * @var taxInclusive
     */
    private $taxInclusive;
	/**
	 * @param Enum $taxInclusive
	 */
	public function setTaxInclusive($taxInclusive)
	{
		$this->taxInclusive = $taxInclusive;
	}
	/**
	 * @return taxInclusive
	 */
	public function getTaxInclusive()
	{
		return $this->taxInclusive;
	}
}