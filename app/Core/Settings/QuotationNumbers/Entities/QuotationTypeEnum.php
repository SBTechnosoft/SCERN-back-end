<?php
namespace ERP\Core\Settings\QuotationNumbers\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationTypeEnum
{
    public function enumArrays()
	{
		$enumArray = array();
		$enumArray['beforeQuotation'] = "prefix";
		$enumArray['afterQuotation'] = "postfix";
		return $enumArray;
	}
}