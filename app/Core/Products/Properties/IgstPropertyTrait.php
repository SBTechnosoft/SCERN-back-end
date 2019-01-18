<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait IgstPropertyTrait
{
	/**
     * @var igst
     */
    private $igst;
	/**
	 * @param float $igst
	 */
	public function setIgst($igst)
	{
		$this->igst = $igst;
	}
	/**
	 * @return igst
	 */
	public function getIgst()
	{
		return $this->igst;
	}
}