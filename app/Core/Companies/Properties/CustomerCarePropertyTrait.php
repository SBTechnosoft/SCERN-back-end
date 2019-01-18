<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait CustomerCarePropertyTrait
{
	/**
     * @var customerCare
     */
    private $customerCare;
	/**
	 * @param int $customerCare
	 */
	public function setCustomerCare($customerCare)
	{
		$this->customerCare = $customerCare;
	}
	/**
	 * @return customerCare
	 */
	public function getCustomerCare()
	{
		return $this->customerCare;
	}
}