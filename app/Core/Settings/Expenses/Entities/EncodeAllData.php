<?php
namespace ERP\Core\Settings\Expenses\Entities;

use ERP\Core\Settings\Expenses\Entities\Expense;
use ERP\Core\Companies\Services\CompanyService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends CompanyService
{
	public function getEncodedAllData($status)
	{
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$expense = new Expense();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			//get the company detail from database
			$encodeDataClass = new EncodeAllData();
			$companyStatus[$decodedData] = $encodeDataClass->getCompanyData($companyId[$decodedData]);
			$companyDecodedJson[$decodedData] = json_decode($companyStatus[$decodedData],true);
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$expense->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $expense->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$expense->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $expense->getUpdated_at();
			}
			$data[$decodedData]= array(
				'expenseId'=> $decodedJson[$decodedData]['expense_id'],
				'expenseName' => $decodedJson[$decodedData]['expense_name'],
				'expenseType' => $decodedJson[$decodedData]['expense_type'],
				'expenseValue' => $decodedJson[$decodedData]['expense_value'],
				'company' => $companyDecodedJson[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData]
			);
		}
		return json_encode($data);
	}
}