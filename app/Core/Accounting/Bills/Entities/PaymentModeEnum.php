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
		$enumArray['creditPayment'] = "credit";
		$enumArray['neftPayment'] = "neft";
		$enumArray['rtgsPayment'] = "rtgs";
		$enumArray['impsPayment'] = "imps";
		$enumArray['nachPayment'] = "nach";
		$enumArray['achPayment'] = "ach";
		return $enumArray;
	}
}