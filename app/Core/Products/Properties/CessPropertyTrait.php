<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CessPropertyTrait
{
	/**
     * @var cess
     */
    private $cess;
	/**
	 * @param float $cess
	 */
	public function setCess($cess)
	{
		$this->cess = $cess;
	}
	/**
	 * @return cess
	 */
	public function getCess()
	{
		return $this->cess;
	}
}