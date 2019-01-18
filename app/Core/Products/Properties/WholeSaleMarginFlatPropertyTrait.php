<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait WholeSaleMarginFlatPropertyTrait
{
	/**
     * @var wholesaleFlat
     */
    private $wholesaleFlat;
	/**
	 * @param float $wholesaleFlat
	 */
	public function setWholeSaleMarginFlat($wholesaleFlat)
	{
		$this->wholesaleFlat = $wholesaleFlat;
	}
	/**
	 * @return wholesaleFlat
	 */
	public function getWholeSaleMarginFlat()
	{
		return $this->wholesaleFlat;
	}
}