<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CurrencySymbolPropertyTrait
{
	/**
     * @var currencySymbol
     */
    private $currencySymbol;
	/**
	 * @param int $currencySymbol
	 */
	public function setCurrencySymbol($currencySymbol)
	{
		$this->currencySymbol = $currencySymbol;
	}
	/**
	 * @return currencySymbol
	 */
	public function getCurrencySymbol()
	{
		return $this->currencySymbol;
	}
}