<?php
namespace ERP\Core\Crm\JobForm\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait AddressPropertyTrait
{
	/**
     * @var address
     */
    private $address;
	/**
	 * @param int $address
	 */
	public function setAddress($address)
	{
		$this->address = $address;
	}
	/**
	 * @return address
	 */
	public function getAddress()
	{
		return $this->address;
	}
}