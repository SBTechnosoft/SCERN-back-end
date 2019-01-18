<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait VatPropertyTrait
{
	/**
     * @var vat
     */
    private $vat;
	/**
	 * @param float $vat
	 */
	public function setVat($vat)
	{
		$this->vat = $vat;
	}
	/**
	 * @return vat
	 */
	public function getVat()
	{
		return $this->vat;
	}
}