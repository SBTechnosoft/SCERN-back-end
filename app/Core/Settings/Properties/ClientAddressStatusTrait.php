<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ClientAddressStatusTrait
{
	/**
     * @var address
     */
    private $address;
	/**
	 * @param string $address
	 */
	public function setClientAddressStatus($address)
	{
		$this->address = $address;
	}
	/**
	 * @return address
	 */
	public function getClientAddressStatus()
	{
		return $this->address;
	}
}