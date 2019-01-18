<?php
namespace ERP\Core\Companies\Properties;

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
	 * @param int $cess
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