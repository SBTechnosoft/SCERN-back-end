<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ProductDescriptionPropertyTrait
{
	/**
     * @var desc
     */
    private $desc;
	/**
	 * @param string $desc
	 */
	public function setProductDescription($desc)
	{
		$this->desc = $desc;
	}
	/**
	 * @return desc
	 */
	public function getProductDescription()
	{
		return $this->desc;
	}
}