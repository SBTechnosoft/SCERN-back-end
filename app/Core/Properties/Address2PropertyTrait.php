<?php
namespace ERP\Core\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait Address2PropertyTrait
{
	/**
     * @var address2
     */
    private $address2;
	/**
	 * @param int $address2
	 */
	public function setAddress2($address2)
	{
		$this->address2 = $address2;
	}
	/**
	 * @return address2
	 */
	public function getAddress2()
	{
		return $this->address2;
	}
}