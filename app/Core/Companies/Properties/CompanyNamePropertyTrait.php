<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CompanyNamePropertyTrait
{
	/**
     * @var companyName
     */
    private $companyName;
	/**
	 * @param int $companyName
	 */
	public function setCompanyName($companyName)
	{
		$this->companyName = $companyName;
	}
	/**
	 * @return companyName
	 */
	public function getCompanyName()
	{
		return $this->companyName;
	}
}