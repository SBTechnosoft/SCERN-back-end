<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InventoryAffectedEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['inventoryAffected'] = "yes";
		$enumArray['inventoryNotAffected'] = "no";
		return $enumArray;
	}
}