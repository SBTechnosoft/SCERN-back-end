<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait FormalNamePropertyTrait
{
	/**
     * @var formalName
     */
    private $formalName;
	/**
	 * @param int $formalName
	 */
	public function setFormalName($formalName)
	{
		$this->formalName = $formalName;
	}
	/**
	 * @return formalName
	 */
	public function getFormalName()
	{
		return $this->formalName;
	}
}