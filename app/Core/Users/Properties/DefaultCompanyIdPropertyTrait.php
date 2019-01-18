<?php
namespace ERP\Core\Users\Properties;

/**
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
trait DefaultCompanyIdPropertyTrait
{
	/**
     * @var defaultCompanyId
     */
    private $defaultCompanyId;
	/**
	 * @param string $defaultCompanyId
	 */
	public function setDefaultCompanyId($defaultCompanyId)
	{
		$this->defaultCompanyId = $defaultCompanyId;
	}
	/**
	 * @return defaultCompanyId
	 */
	public function getDefaultCompanyId()
	{
		return $this->defaultCompanyId;
	}
}