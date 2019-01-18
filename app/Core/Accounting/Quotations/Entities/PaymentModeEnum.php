<?php
namespace ERP\Core\Accounting\Bills\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PaymentModeEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['cashPayment'] = "cash";
		$enumArray['bankPayment'] = "bank";
		$enumArray['cardPayment'] = "card";
		return $enumArray;
	}
}