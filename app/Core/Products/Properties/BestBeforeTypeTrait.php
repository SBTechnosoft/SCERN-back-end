<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BestBeforeTypeTrait
{
	/**
     * @var bestBeforeType
     */
    private $bestBeforeType;
	/**
	 * @param float $bestBeforeType
	 */
	public function setBestBeforeType($bestBeforeType)
	{
		$this->bestBeforeType = $bestBeforeType;
	}
	/**
	 * @return bestBeforeType
	 */
	public function getBestBeforeType()
	{
		return $this->bestBeforeType;
	}
}