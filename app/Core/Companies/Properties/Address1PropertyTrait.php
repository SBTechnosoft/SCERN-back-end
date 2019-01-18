<?php
namespace ERP\Core\Companies\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait Address1PropertyTrait
{
	/**
     * @var address1
     */
    private $address1;
	/**
	 * @param int $address1
	 */
	public function setAddress1($address1)
	{
		$this->address1 = $address1;
	}
	/**
	 * @return address1
	 */
	public function getAddress1()
	{
		return $this->address1;
	}
}