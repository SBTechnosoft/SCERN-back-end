<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CompanyIdPropertyTrait
{
	/**
     * @var companyId
     */
    private $companyId;
	/**
	 * @param int $companyId
	 */
	public function setCompanyId($companyId)
	{
		$this->companyId = $companyId;
	}
	/**
	 * @return companyId
	 */
	public function getCompanyId()
	{
		return $this->companyId;
	}
}