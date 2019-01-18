<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ColorPropertyTrait
{
	/**
     * @var color
     */
    private $color;
	/**
	 * @param string $color
	 */
	public function setColor($color)
	{
		$this->color = $color;
	}
	/**
	 * @return color
	 */
	public function getColor()
	{
		return $this->color;
	}
}