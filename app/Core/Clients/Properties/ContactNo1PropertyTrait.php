<?php
namespace ERP\Core\Clients\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait ContactNo1PropertyTrait
{
	/**
     * @var contactNo1
     */
    private $contactNo1;
	/**
	 * @param int $contactNo1
	 */
	public function setContactNo1($contactNo1)
	{
		$this->contactNo1 = $contactNo1;
	}
	/**
	 * @return contactNo1
	 */
	public function getContactNo1()
	{
		return $this->contactNo1;
	}
}