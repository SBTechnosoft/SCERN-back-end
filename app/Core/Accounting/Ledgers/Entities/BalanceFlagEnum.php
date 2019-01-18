<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BalanceFlagEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['openingBalance'] = "opening";
		$enumArray['closingBalance'] = "closing";
		return $enumArray;
	}
}