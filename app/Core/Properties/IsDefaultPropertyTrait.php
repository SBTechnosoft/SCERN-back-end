<?php
namespace ERP\Core\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait IsDefaultPropertyTrait
{
	/**
     * @var isDefault
     */
    private $isDefault;
	/**
	 * @param int $isDefault
	 */
	public function setIsDefault($isDefault)
	{
		$this->isDefault = $isDefault;
	}
	/**
	 * @return isDefault
	 */
	public function getIsDefault()
	{
		return $this->isDefault;
	}
}