<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ServiceTaxNoPropertyTrait
{
	/**
     * @var serviceTaxNo
     */
    private $serviceTaxNo;
	/**
	 * @param int $serviceTaxNo
	 */
	public function setServiceTaxNo($serviceTaxNo)
	{
		$this->serviceTaxNo = $serviceTaxNo;
	}
	/**
	 * @return serviceTaxNo
	 */
	public function getServiceTaxNo()
	{
		return $this->serviceTaxNo;
	}
}