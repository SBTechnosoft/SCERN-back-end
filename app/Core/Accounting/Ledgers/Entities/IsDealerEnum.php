<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class IsDealerEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['isDealer'] = "y";
		$enumArray['isNotDealer'] = "n";
		return $enumArray;
	}
}