<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ContactNoPropertyTrait
{
	/**
     * @var contactNo
     */
    private $contactNo;
	/**
	 * @param int $contactNo
	 */
	public function setContactNo($contactNo)
	{
		$this->contactNo = $contactNo;
	}
	/**
	 * @return contactNo
	 */
	public function getContactNo()
	{
		return $this->contactNo;
	}
}