<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait HighestMouConvTrait
{
	/**
     * @var highestMouConv
     */
    private $highestMouConv;
	/**
	 * @param Integer $highestMouConv
	 */
	public function setHighestMouConv($highestMouConv)
	{
		$this->highestMouConv = $highestMouConv;
	}
	/**
	 * @return highestMouConv
	 */
	public function getHighestMouConv()
	{
		return $this->highestMouConv;
	}
}