<?php
namespace ERP\Core\Settings\Expenses\Entities;

use ERP\Core\Settings\Expenses\Entities\Expense;
use ERP\Core\Companies\Services\CompanyService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends CompanyService
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt= $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$companyId= $decodedJson[0]['company_id'];
		
		//get the company details from database
		$encodeCompanyDataClass = new EncodeData();
		$companyStatus = $encodeCompanyDataClass->getCompanyData($companyId);
		$companyDecodedJson = json_decode($companyStatus,true);

		//date format conversion
		$expense = new Expense();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$expense->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $expense->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$expense->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $expense->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['expenseId'] = $decodedJson[0]['expense_id'];
		$data['expenseName'] = $decodedJson[0]['expense_name'];
		$data['expenseType'] = $decodedJson[0]['expense_type'];
		$data['expenseValue'] = $decodedJson[0]['expense_value'];
		$data['company'] = $companyDecodedJson;
		$data['createdAt'] = $getCreatedDate;	
		$data['updatedAt'] = $getUpdatedDate;	
		$encodeData = json_encode($data);
		return $encodeData;
	}
}