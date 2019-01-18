<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait WholeSaleMarginPropertyTrait
{
	/**
     * @var wholeSaleMargin
     */
    private $wholeSaleMargin;
	/**
	 * @param float $wholeSaleMargin
	 */
	public function setWholesaleMargin($wholeSaleMargin)
	{
		$this->wholeSaleMargin = $wholeSaleMargin;
	}
	/**
	 * @return wholeSaleMargin
	 */
	public function getWholesaleMargin()
	{
		return $this->wholeSaleMargin;
	}
}