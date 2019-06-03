<?php
namespace ERP\Core\Accounting\Ledgers\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerArray
{
	public function ledgerArrays()
	{
		$ledgerGeneralArray = array();
		$ledgerGeneralArray[0] = "capital";
		$ledgerGeneralArray[1] = "salary";
		$ledgerGeneralArray[2] = "machine";
		$ledgerGeneralArray[3] = "labour";
		// $ledgerGeneralArray[4] = "sales";
		$ledgerGeneralArray[4] = "purchase";
		$ledgerGeneralArray[5] = "bank";
		$ledgerGeneralArray[6] = "tax(output)";
		$ledgerGeneralArray[7] = "tax(input)";
		$ledgerGeneralArray[8] = "discount(income)";
		$ledgerGeneralArray[9] = "discount(expense)";
		$ledgerGeneralArray[10] = "cash";
		$ledgerGeneralArray[11] = "sales_return";
		$ledgerGeneralArray[12] = "purchase_return";
		$ledgerGeneralArray[13] = "retail_sales";
		$ledgerGeneralArray[14] = "whole_sales";
		// $ledgerGeneralArray[15] = "card";
		$ledgerGeneralArray[15] = "purchase_tax";
		$ledgerGeneralArray[16] = "commission(income)";
		$ledgerGeneralArray[17] = "commission(expense)";
		$ledgerGeneralArray[18] = "purchase_expense";
		$ledgerGeneralArray[19] = "sales_expense";
		// $ledgerGeneralArray[17] = "neft";
		// $ledgerGeneralArray[18] = "rtgs";
		// $ledgerGeneralArray[19] = "imps";
		// $ledgerGeneralArray[20] = "nach";
		// $ledgerGeneralArray[21] = "ach";
		return $ledgerGeneralArray;
	}
	
	public function billLedgerArray()
	{
		$ledgerGeneralArray = array();
		// $ledgerGeneralArray[0] = "sales";
		$ledgerGeneralArray[0] = "tax(output)";
		$ledgerGeneralArray[1] = "discount(expense)";
		return $ledgerGeneralArray;
	}
	public function commissionLedgerArray()
	{
		$ledgerGeneralArray = array();
		// $ledgerGeneralArray[16] = "commission(income)";
		$ledgerGeneralArray[0] = "commission(expense)";
		return $ledgerGeneralArray;
	}
	public function expenseLedgerArray()
	{
		$ledgerGeneralArray = array();
		// $ledgerGeneralArray[16] = "commission(income)";
		$ledgerGeneralArray[0] = "purchase_expense";
		$ledgerGeneralArray[1] = "sales_expense";
		return $ledgerGeneralArray;
	}
	public function ledgerGrpArray()
	{
		$ledgerGeneralArray = array();
		$ledgerGeneralArray[0] = 11;
		$ledgerGeneralArray[1] = 19;
		$ledgerGeneralArray[2] = 13;
		$ledgerGeneralArray[3] = 19;
		// $ledgerGeneralArray[4] = 28;
		$ledgerGeneralArray[4] = 26;
		$ledgerGeneralArray[5] = 9;
		$ledgerGeneralArray[6] = 18;
		$ledgerGeneralArray[7] = 18;
		$ledgerGeneralArray[8] = 17;
		$ledgerGeneralArray[9] = 16;
		$ledgerGeneralArray[10] = 12;
		$ledgerGeneralArray[11] = 28;
		$ledgerGeneralArray[12] = 26;
		$ledgerGeneralArray[13] = 28;
		$ledgerGeneralArray[14] = 28;
		$ledgerGeneralArray[15] = 26;
		$ledgerGeneralArray[16] = 17;
		$ledgerGeneralArray[17] = 16;

		$ledgerGeneralArray[18] = 19;
		$ledgerGeneralArray[19] = 20;
		// $ledgerGeneralArray[15] = 9;
		// $ledgerGeneralArray[16] = 26;
		// $ledgerGeneralArray[17] = 9;
		// $ledgerGeneralArray[18] = 9;
		// $ledgerGeneralArray[19] = 9;
		// $ledgerGeneralArray[20] = 9;
		// $ledgerGeneralArray[21] = 9;
		return $ledgerGeneralArray;
	}
	
	public function getLedgerArrayData()
	{
		$ledgerArray = array();
		$ledgerArray['ledgerName'] = 'client_name';
		$ledgerArray['contactNo'] = 'contact_no';
		$ledgerArray['emailId'] = 'email_id';
		$ledgerArray['address1'] = 'address1';
		$ledgerArray['address2'] = 'address2';
		$ledgerArray['cityId'] = 'city_id';
		$ledgerArray['stateAbb'] = 'state_abb';
		return $ledgerArray;
	}
}