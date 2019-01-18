<?php
namespace ERP\Core\Clients\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ClientArray
{
	public function getClientArrayData()
	{
		$clientArray = array();
		$clientArray['client_name'] = 'clientName';
		$clientArray['company_name'] = 'companyName';
		$clientArray['contact_no'] = 'contactNo';
		$clientArray['contact_no1'] = 'contactNo1';
		$clientArray['email_id'] = 'emailId';
		$clientArray['gst'] = 'gst';
		$clientArray['address1'] = 'address1';
		$clientArray['is_display'] = 'isDisplay';
		$clientArray['profession_id'] = 'professionId';
		$clientArray['city_id'] = 'cityId';
		$clientArray['state_abb'] = 'stateAbb';
		$clientArray['credit_limit'] = 'creditLimit';
		$clientArray['credit_days'] = 'creditDays';
		return $clientArray;
	}
	
	public function getClientArrayDataForBill()
	{
		$clientArray = array();
		$clientArray['client_name'] = 'client_name';
		$clientArray['company_name'] = 'company_name';
		$clientArray['contact_no'] = 'contact_no';
		$clientArray['contact_no1'] = 'contact_no1';
		$clientArray['email_id'] = 'email_id';
		$clientArray['gst'] = 'gst';
		$clientArray['address1'] = 'address1';
		$clientArray['is_display'] = 'is_display';
		$clientArray['profession_id'] = 'profession_id';
		$clientArray['city_id'] = 'city_id';
		$clientArray['state_abb'] = 'state_abb';
		return $clientArray;
	}
	
	public function searchClientData()
	{
		$clientArray = array();
		$clientArray['client_name'] = 'clientname';
		$clientArray['contact_no'] = 'contactno';
		$clientArray['contact_no1'] = 'contactno1';
		$clientArray['address1'] = 'address';
		$clientArray['email_id'] = 'emailid';
		$clientArray['profession_id'] = 'professionid';
		return $clientArray;
	}
	
	public function getBillClientArrayData()
	{
		$clientArray = array();
		$clientArray['client_name'] = 'clientName';
		$clientArray['company_name'] = 'companyName';
		$clientArray['contact_no'] = 'contactNo';
		$clientArray['contact_no1'] = 'contactNo1';
		$clientArray['email_id'] = 'emailId';
		$clientArray['gst'] = 'gst';
		$clientArray['address1'] = 'address1';
		$clientArray['is_display'] = 'isDisplay';
		$clientArray['profession_id'] = 'professionId';
		$clientArray['city_id'] = 'cityId';
		$clientArray['state_abb'] = 'stateAbb';
		$clientArray['transaction_date'] = 'transactionDate';
		return $clientArray;
	}
}