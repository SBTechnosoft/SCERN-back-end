<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductColorStatusTrait
{
	/**
     * @var color
     */
    private $color;
	/**
	 * @param string $color
	 */
	public function setProductColorStatus($color)
	{
		$this->color = $color;
	}
	/**
	 * @return color
	 */
	public function getProductColorStatus()
	{
		return $this->color;
	}
}