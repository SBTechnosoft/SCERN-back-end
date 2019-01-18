<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait HigherMouConvTrait
{
	/**
     * @var higherMouConv
     */
    private $higherMouConv;
	/**
	 * @param Integer $higherMouConv
	 */
	public function setHigherMouConv($higherMouConv)
	{
		$this->higherMouConv = $higherMouConv;
	}
	/**
	 * @return higherMouConv
	 */
	public function getHigherMouConv()
	{
		return $this->higherMouConv;
	}
}