<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait MarginFlatPropertyTrait
{
	/**
     * @var marginFlat
     */
    private $marginFlat;
	/**
	 * @param float $marginFlat
	 */
	public function setMarginFlat($marginFlat)
	{
		$this->marginFlat = $marginFlat;
	}
	/**
	 * @return marginFlat
	 */
	public function getMarginFlat()
	{
		return $this->marginFlat;
	}
}