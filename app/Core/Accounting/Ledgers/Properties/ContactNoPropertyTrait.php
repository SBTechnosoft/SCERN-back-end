<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

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
	 * @param string $contactNo
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