<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CompanyDispNamePropertyTrait
{
	/**
     * @var companyDispName
     */
    private $companyDispName;
	/**
	 * @param int $companyDispName
	 */
	public function setCompanyDisplayName($companyDispName)
	{
		$this->companyDispName = $companyDispName;
	}
	/**
	 * @return companyDispName
	 */
	public function getCompanyDisplayName()
	{
		return $this->companyDispName;
	}
}