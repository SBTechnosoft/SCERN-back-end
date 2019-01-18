<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait MrpPropertyTrait
{
	/**
     * @var mrp
     */
    private $mrp;
	/**
	 * @param float $mrp
	 */
	public function setMrp($mrp)
	{
		$this->mrp = $mrp;
	}
	/**
	 * @return mrp
	 */
	public function getMrp()
	{
		return $this->mrp;
	}
}