<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductSizeStatusTrait
{
	/**
     * @var size
     */
    private $size;
	/**
	 * @param int $size
	 */
	public function setProductSizeStatus($size)
	{
		$this->size = $size;
	}
	/**
	 * @return size
	 */
	public function getProductSizeStatus()
	{
		return $this->size;
	}
}