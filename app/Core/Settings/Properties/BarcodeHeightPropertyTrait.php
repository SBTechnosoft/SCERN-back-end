<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BarcodeHeightPropertyTrait
{
	/**
     * @var height
     */
    private $height;
	/**
	 * @param float $height
	 */
	public function setBarcodeHeight($height)
	{
		$this->height = $height;
	}
	/**
	 * @return height
	 */
	public function getBarcodeHeight()
	{
		return $this->height;
	}
}