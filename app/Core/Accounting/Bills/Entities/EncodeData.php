<?php
namespace ERP\Core\Accounting\Bills\Entities;

use ERP\Core\Accounting\Bills\Entities\Bill;
use ERP\Core\Clients\Services\ClientService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\BranchDetail;
use ERP\Core\Users\Services\UserService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends ClientService 
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$saleId= $decodedJson[0]['sale_id'];
		$productArray= $decodedJson[0]['product_array'];
		$paymentMode= $decodedJson[0]['payment_mode'];
		$bankLedgerId= $decodedJson[0]['bank_ledger_id'];
		$bankName= $decodedJson[0]['bank_name'];
		$invoiceNumber= $decodedJson[0]['invoice_number'];
		$jobCardNumber= $decodedJson[0]['job_card_number'];
		$checkNumber= $decodedJson[0]['check_number'];
		$total= $decodedJson[0]['total'];
		$totalDiscounttype= $decodedJson[0]['total_discounttype'];
		$totalDiscount= $decodedJson[0]['total_discount'];
		$totalCgstPercentage = $decodedJson[0]['total_cgst_percentage'];
		$totalSgstPercentage = $decodedJson[0]['total_sgst_percentage'];
		$totalIgstPercentage = $decodedJson[0]['total_igst_percentage'];
		$extraCharge= $decodedJson[0]['extra_charge'];
		$tax= $decodedJson[0]['tax'];
		$grandTotal= $decodedJson[0]['grand_total'];
		$advance = $decodedJson[0]['advance'];
		$balance = $decodedJson[0]['balance'];
		$poNumber = $decodedJson[0]['po_number'];
		$userId = $decodedJson[0]['user_id'];
		$remark= $decodedJson[0]['remark'];
		$entryDate= $decodedJson[0]['entry_date'];
		$serviceDate= $decodedJson[0]['service_date'];
		$clientId= $decodedJson[0]['client_id'];
		$jfId= $decodedJson[0]['jf_id'];
		$companyId= $decodedJson[0]['company_id'];
		$branchId= $decodedJson[0]['branch_id'];
		$salesType= $decodedJson[0]['sales_type'];
		$expense= $decodedJson[0]['expense'];
		$printCount= $decodedJson[0]['print_count'];
		//get the client details from database
		$encodeStateDataClass = new EncodeData();
		$clientStatus = $encodeStateDataClass->getClientData($clientId);
		$clientDecodedJson = json_decode($clientStatus,true);
		
		//get the company details from database
		$companyDetail = new CompanyDetail();
		$companyDetails = $companyDetail->getCompanyDetails($companyId);

		//get the Branch details from database
		$branchDetail = new BranchDetail();
		$branchDetails = $branchDetail->getBranchDetails($branchId);
		
		//get the user detail from database
		$userService = new UserService();
		$userData = $userService->getUserData($userId);
		$decodedUserData = json_decode($userData);
		
		//convert amount(round) into their company's selected decimal points
		$total = number_format($total,$companyDetails['noOfDecimalPoints'],'.','');
		$totalDiscount = number_format($totalDiscount,$companyDetails['noOfDecimalPoints'],'.','');
		$totalCgstPercentage = number_format($totalCgstPercentage,$companyDetails['noOfDecimalPoints'],'.','');
		$totalSgstPercentage = number_format($totalSgstPercentage,$companyDetails['noOfDecimalPoints'],'.','');
		$totalIgstPercentage = number_format($totalIgstPercentage,$companyDetails['noOfDecimalPoints'],'.','');
		$tax = number_format($tax,$companyDetails['noOfDecimalPoints'],'.','');
		$grandTotal = number_format($grandTotal,$companyDetails['noOfDecimalPoints'],'.','');
		$advance = number_format($advance,$companyDetails['noOfDecimalPoints'],'.','');
		$balance = number_format($balance,$companyDetails['noOfDecimalPoints'],'.','');
		
		//date format conversion
		$bill = new Bill();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$bill->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $bill->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$bill->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $bill->getUpdated_at();
		}
		if(strcmp($entryDate,'0000-00-00')==0)
		{
			$getEntryDate = "00-00-0000";
		}
		else
		{
			$convertedEntryDate = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate)->format('d-m-Y');
			$bill->setEntryDate($convertedEntryDate);
			$getEntryDate = $bill->getEntryDate();
		}
		if(strcmp($serviceDate,'0000-00-00')==0)
		{
			$serviceDate = "00-00-0000";
		}
		else
		{
			$serviceDate = Carbon\Carbon::createFromFormat('Y-m-d', $serviceDate)->format('d-m-Y');
		}
		//set all data into json array
		$data = array();
		$data['saleId'] = $saleId;
		$data['productArray'] = $productArray;
		$data['paymentMode'] = $paymentMode;
		$data['bankLedgerId'] = $bankLedgerId;
		$data['bankName'] = $bankName;
		$data['invoiceNumber'] = $invoiceNumber;
		$data['jobCardNumber'] = $jobCardNumber;
		$data['checkNumber'] = $checkNumber;
		$data['total'] = $total;$totalDiscounttype= $decodedJson[0]['total_discounttype'];
		$data['totalDiscounttype'] = $totalDiscounttype;
		$data['totalDiscount'] = $totalDiscount;
		$data['totalCgstPercentage'] = $totalCgstPercentage;
		$data['totalSgstPercentage'] = $totalSgstPercentage;
		$data['totalIgstPercentage'] = $totalIgstPercentage;
		$data['extraCharge'] = $extraCharge;
		$data['tax'] = $tax;
		$data['grandTotal'] = $grandTotal;
		$data['advance'] = $advance;
		$data['balance'] = $balance;
		$data['poNumber'] = $poNumber;
		$data['user'] = $decodedUserData;
		$data['createdAt'] = $getCreatedDate;
		$data['remark'] = $remark;
		$data['entryDate'] = $getEntryDate;
		$data['serviceDate'] = $serviceDate;
		$data['clientId'] = $clientId;
		$data['jfId'] = $jfId;
		$data['expense'] = $expense;
		$data['salesType'] = $salesType;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['client']= $clientDecodedJson;
		$data['company']= $companyDetails;
		$data['branch']= $branchDetails;
		$data['printCount']= $printCount;
		$encodeData = json_encode($data);
		return $encodeData;
	}
}