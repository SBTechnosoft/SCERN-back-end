<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait BestBeforeTimeTrait
{
	/**
     * @var bestBeforeTime
     */
    private $bestBeforeTime;
	/**
	 * @param float $bestBeforeTime
	 */
	public function setBestBeforeTime($bestBeforeTime)
	{
		$this->bestBeforeTime = $bestBeforeTime;
	}
	/**
	 * @return bestBeforeTime
	 */
	public function getBestBeforeTime()
	{
		return $this->bestBeforeTime;
	}
}