<?php
namespace ERP\Core\Accounting\Journals\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class AmountTypeEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['creditType'] = "credit";
		$enumArray['debitType'] = "debit";
		return $enumArray;
	}
}