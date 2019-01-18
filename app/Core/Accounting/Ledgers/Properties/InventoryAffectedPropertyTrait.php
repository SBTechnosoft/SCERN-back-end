<?php
namespace ERP\Core\Accounting\Ledgers\Properties;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
trait InventoryAffectedPropertyTrait
{
	/**
     * @var inventoryAffected
     */
    private $inventoryAffected;
	/**
	 * @param int $inventoryAffected
	 */
	public function setInventoryAffected($inventoryAffected)
	{
		$this->inventoryAffected = $inventoryAffected;
	}
	/**
	 * @return inventoryAffected
	 */
	public function getInventoryAffected()
	{
		return $this->inventoryAffected;
	}
}