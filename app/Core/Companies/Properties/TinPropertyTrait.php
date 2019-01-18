<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait TinPropertyTrait
{
	/**
     * @var tin
     */
    private $tin;
	/**
	 * @param int $tin
	 */
	public function setTin($tin)
	{
		$this->tin = $tin;
	}
	/**
	 * @return tin
	 */
	public function getTin()
	{
		return $this->tin;
	}
}