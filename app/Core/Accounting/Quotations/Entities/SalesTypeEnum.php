<?php
namespace ERP\Core\Accounting\Bills\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class SalesTypeEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['retailSales'] = "retail_sales";
		$enumArray['wholesales'] = "whole_sales";
		$enumArray['jobCard'] = "job_card";
		return $enumArray;
	}
}