<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait VariantPropertyTrait
{
	/**
     * @var variant
     */
    private $variant;
	/**
	 * @param string $variant
	 */
	public function setVariant($variant)
	{
		$this->variant = $variant;
	}
	/**
	 * @return variant
	 */
	public function getVariant()
	{
		return $this->variant;
	}
}