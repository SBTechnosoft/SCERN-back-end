<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait OpeningTrait
{
	/**
     * @var opening
     */
    private $opening;
	/**
	 * @param float $opening
	 */
	public function setOpening($opening)
	{
		$this->opening = $opening;
	}
	/**
	 * @return opening
	 */
	public function getOpening()
	{
		return $this->opening;
	}
}