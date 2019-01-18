<?php
namespace ERP\Core\Settings\InvoiceNumbers\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InvoiceTypeEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['beforeInvoice'] = "prefix";
		$enumArray['afterInvoice'] = "postfix";
		return $enumArray;
	}
}