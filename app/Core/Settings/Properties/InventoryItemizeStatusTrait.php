<?php
namespace ERP\Core\Settings\Properties;

/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
trait InventoryItemizeStatusTrait
{
	/**
     * @var inventoryItemizeStatus
     */
    private $inventoryItemizeStatus;
	/**
	 * @param string $inventoryItemizeStatus
	 */
	public function setInventoryItemizeStatus($inventoryItemizeStatus)
	{
		$this->inventoryItemizeStatus = $inventoryItemizeStatus;
	}
	/**
	 * @return inventoryItemizeStatus
	 */
	public function getInventoryItemizeStatus()
	{
		return $this->inventoryItemizeStatus;
	}
}