<?php
namespace ERP\Core\Accounting\Bills\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PaymentTransactionEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['payment'] = "payment";
		$enumArray['refund'] = "refund";
		return $enumArray;
	}
}