<?php
namespace ERP\Core\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait PincodePropertyTrait
{
	/**
     * @var pincode
     */
    private $pincode;
	/**
	 * @param int $pincode
	 */
	public function setPincode($pincode)
	{
		$this->pincode = $pincode;
	}
	/**
	 * @return pincode
	 */
	public function getPincode()
	{
		return $this->pincode;
	}
}