<?php
namespace ERP\Core\Accounting\PurchaseBills\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait VendorIdTrait
{
	/**
     * @var vendorId
     */
    private $vendorId;
	/**
	 * @param int $vendorId
	 */
	public function setVendorId($vendorId)
	{
		$this->vendorId = $vendorId;
	}
	/**
	 * @return vendorId
	 */
	public function getVendorId()
	{
		return $this->vendorId;
	}
}