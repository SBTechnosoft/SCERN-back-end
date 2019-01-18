<?php
namespace ERP\Core\Accounting\Bills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ServiceDateTrait
{
	/**
     * @var serviceDate
     */
    private $serviceDate;
	/**
	 * @param date $serviceDate
	 */
	public function setServiceDate($serviceDate)
	{
		$this->serviceDate = $serviceDate;
	}
	/**
	 * @return serviceDate
	 */
	public function getServiceDate()
	{
		return $this->serviceDate;
	}
}