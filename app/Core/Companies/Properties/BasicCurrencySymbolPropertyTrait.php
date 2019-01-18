<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BasicCurrencySymbolPropertyTrait
{
	/**
     * @var basicCurrencySymbol
     */
    private $basicCurrencySymbol;
	/**
	 * @param int $basicCurrencySymbol
	 */
	public function setBasicCurrencySymbol($basicCurrencySymbol)
	{
		$this->basicCurrencySymbol = $basicCurrencySymbol;
	}
	/**
	 * @return basicCurrencySymbol
	 */
	public function getBasicCurrencySymbol()
	{
		return $this->basicCurrencySymbol;
	}
}