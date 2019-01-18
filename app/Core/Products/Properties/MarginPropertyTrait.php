<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait MarginPropertyTrait
{
	/**
     * @var margin
     */
    private $margin;
	/**
	 * @param float $margin
	 */
	public function setMargin($margin)
	{
		$this->margin = $margin;
	}
	/**
	 * @return margin
	 */
	public function getMargin()
	{
		return $this->margin;
	}
}