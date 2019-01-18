<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait SizePropertyTrait
{
	/**
     * @var size
     */
    private $size;
	/**
	 * @param string $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}
	/**
	 * @return size
	 */
	public function getSize()
	{
		return $this->size;
	}
}