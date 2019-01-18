<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BarcodeWidthPropertyTrait
{
	/**
     * @var width
     */
    private $width;
	/**
	 * @param float $width
	 */
	public function setBarcodeWidth($width)
	{
		$this->width = $width;
	}
	/**
	 * @return width
	 */
	public function getBarcodeWidth()
	{
		return $this->width;
	}
}