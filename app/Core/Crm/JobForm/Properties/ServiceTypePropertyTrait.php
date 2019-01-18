<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ServiceTypePropertyTrait
{
	/**
     * @var serviceType
     */
    private $serviceType;
	/**
	 * @param int $serviceType
	 */
	public function setServiceType($serviceType)
	{
		$this->serviceType = $serviceType;
	}
	/**
	 * @return serviceType
	 */
	public function getServiceType()
	{
		return $this->serviceType;
	}
}