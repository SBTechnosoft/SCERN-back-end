<?php
namespace ERP\Core\Accounting\PurchaseBills\Entities;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BillTypeEnum 
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['purchaseType'] = "purchase";
		$enumArray['purchaseBillType'] = "purchase_bill";
		return $enumArray;
	}
}