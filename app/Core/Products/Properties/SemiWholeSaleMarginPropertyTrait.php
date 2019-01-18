<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait SemiWholeSaleMarginPropertyTrait
{
	/**
     * @var semiWholesaleMargin
     */
    private $semiWholesaleMargin;
	/**
	 * @param float $semiWholesaleMargin
	 */
	public function setSemiWholesaleMargin($semiWholesaleMargin)
	{
		$this->semiWholesaleMargin = $semiWholesaleMargin;
	}
	/**
	 * @return semiWholesaleMargin
	 */
	public function getSemiWholesaleMargin()
	{
		return $this->semiWholesaleMargin;
	}
}