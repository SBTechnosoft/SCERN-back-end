<?php
namespace ERP\Core\Products\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait AltProductNamePropertyTrait
{
	/**
     * @var altProductName
     */
    private $altProductName;
	/**
	 * @param int $altProductName
	 */
	public function setAltProductName($altProductName)
	{
		$this->altProductName = $altProductName;
	}
	/**
	 * @return altProductName
	 */
	public function getAltProductName()
	{
		return $this->altProductName;
	}
}