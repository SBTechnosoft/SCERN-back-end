<?php
namespace ERP\Core\Clients\Properties;

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
	 * @param string $companyName
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