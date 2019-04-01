<?php
namespace ERP\Core\Accounting\Bills\Entities;

use ERP\Core\Accounting\Bills\Entities\Bill;
use ERP\Core\Clients\Services\ClientService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\BranchDetail;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Users\Services\UserService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends ClientService
{
	public function getEncodedAllData($status)
	{
		$constantClass = new ConstantClass();		
		$constantArray = $constantClass->constantVariable();
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$deocodedJsonData = json_decode($status);
		// $deocodedJsonData = json_decode($decodedJson);
		$bill = new Bill();
		$encodeAllData = new EncodeAllData();
		$companyDetail  = new CompanyDetail();
		$branchDetail  = new BranchDetail();
		$userService = new UserService();

		$clientArray = array();
		$companyArray = array();
		$branchArray = array();
		$userArray = array();
		$arrayData = array();
		$data = array();

		for($decodedData=0;$decodedData<count($deocodedJsonData);$decodedData++)
		{
			$saleId = $deocodedJsonData[$decodedData]->sale_id;
			$productArray = $deocodedJsonData[$decodedData]->product_array;
			$paymentMode = $deocodedJsonData[$decodedData]->payment_mode;
			$bankLedgerId = $deocodedJsonData[$decodedData]->bank_ledger_id;
			$bankName = $deocodedJsonData[$decodedData]->bank_name;
			$invoiceNumber = $deocodedJsonData[$decodedData]->invoice_number;
			$jobCardNumber = $deocodedJsonData[$decodedData]->job_card_number;
			$checkNumber = $deocodedJsonData[$decodedData]->check_number;
			$total = $deocodedJsonData[$decodedData]->total;
			$totalDiscounttype = $deocodedJsonData[$decodedData]->total_discounttype;
			$totalDiscount = $deocodedJsonData[$decodedData]->total_discount;
			$totalCgstPercentage = isset($deocodedJsonData[$decodedData]->total_cgst_percentage) ? $deocodedJsonData[$decodedData]->total_cgst_percentage : 0;
			$totalSgstPercentage = isset($deocodedJsonData[$decodedData]->total_sgst_percentage) ? $deocodedJsonData[$decodedData]->total_sgst_percentage : 0;
			$totalIgstPercentage = isset($deocodedJsonData[$decodedData]->total_igst_percentage) ? $deocodedJsonData[$decodedData]->total_igst_percentage : 0;
			$extraCharge = $deocodedJsonData[$decodedData]->extra_charge;
			$tax = $deocodedJsonData[$decodedData]->tax;
			$grandTotal = $deocodedJsonData[$decodedData]->grand_total;
			$advance = $deocodedJsonData[$decodedData]->advance;
			$balance = $deocodedJsonData[$decodedData]->balance;
			$poNumber = $deocodedJsonData[$decodedData]->po_number;
			$userId = $deocodedJsonData[$decodedData]->user_id;
			$remark = $deocodedJsonData[$decodedData]->remark;
			$refund = $deocodedJsonData[$decodedData]->refund;
			$entryDate = $deocodedJsonData[$decodedData]->entry_date;
			$serviceDate = $deocodedJsonData[$decodedData]->service_date;
			$clientId = $deocodedJsonData[$decodedData]->client_id;
			$jfId = $deocodedJsonData[$decodedData]->jf_id;
			$expense = json_decode($deocodedJsonData[$decodedData]->expense);
			$salesType = $deocodedJsonData[$decodedData]->sales_type;
			$companyId = $deocodedJsonData[$decodedData]->company_id;
			$branchId = $deocodedJsonData[$decodedData]->branch_id;
			$createdAt = $deocodedJsonData[$decodedData]->created_at;
			$updatedAt = $deocodedJsonData[$decodedData]->updated_at;
			$decodedDocumentData = json_decode($deocodedJsonData[$decodedData]->file,true);
			//get the client detail from database
			if (!isset($clientArray[$clientId])) {
				$clientArray[$clientId] =  $encodeAllData->getClientData($clientId);
			}
			$getClientDetails = $clientArray[$clientId];

			//get the company detail from database
			if (!isset($companyArray[$companyId])) {
				$companyArray[$companyId] = $companyDetail->getCompanyDetails($companyId);
			}
			$getCompanyDetails = $companyArray[$companyId];

			//get the Branch detail from database
			if (!isset($branchArray[$branchId])) {
				$branchArray[$branchId] = $branchDetail->getBranchDetails($branchId);
			}
			$getBranchDetails = $branchArray[$branchId];
			
			//get the user detail from database
			if (!isset($userArray[$userId])) {
				$userArray[$userId] = $userService->getUserData($userId);
			}
			$userData = $userArray[$userId];

			$decodedUserData = json_decode($userData);

			//convert amount(round) into their company's selected decimal points
			$total = number_format($total,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$totalDiscount = number_format($totalDiscount,$getCompanyDetails['noOfDecimalPoints'],'.','');

			$totalCgstPercentage = number_format($totalCgstPercentage,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$totalSgstPercentage = number_format($totalSgstPercentage,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$totalIgstPercentage = number_format($totalIgstPercentage,$getCompanyDetails['noOfDecimalPoints'],'.','');

			$tax = number_format($tax,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$grandTotal = number_format($grandTotal,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$advance = number_format($advance,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$balance = number_format($balance,$getCompanyDetails['noOfDecimalPoints'],'.','');
			$refund = number_format($refund,$getCompanyDetails['noOfDecimalPoints'],'.','');
			
			//date format conversion
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
			// print_r($decodedDocumentData);exit();
			$arrayData = $decodedDocumentData;
			if ($arrayData != '') {
				$arrayData = array_map(function($ar) use ($constantArray){
								$ar['documentUrl'] = strcmp($ar['documentFormat'], 'pdf')==0 ? $constantArray['billUrl'] : $constantArray['billDocumentUrl'];
								return $ar;
						}, $decodedDocumentData);
			}
			
			//get document data
			
			$clientData = json_decode($getClientDetails);
			$data[$decodedData]= array(
				'saleId'=>$saleId,
				'productArray'=>$productArray,
				'paymentMode'=>$paymentMode,
				'bankLedgerId'=>$bankLedgerId,
				'bankName'=>$bankName,
				'invoiceNumber'=>$invoiceNumber,
				'jobCardNumber'=>$jobCardNumber,
				'checkNumber'=>$checkNumber,
				'total'=>$total,
				'totalDiscounttype'=>$totalDiscounttype,
				'totalDiscount'=>$totalDiscount,
				'totalCgstPercentage'=>$totalCgstPercentage,
				'totalSgstPercentage'=>$totalSgstPercentage,
				'totalIgstPercentage'=>$totalIgstPercentage,
				'extraCharge'=>$extraCharge,
				'tax'=>$tax,
				'grandTotal'=>$grandTotal,
				'advance'=>$advance,
				'balance'=>$balance,
				'poNumber'=>$poNumber,
				'user'=>$decodedUserData,
				'remark'=>$remark,
				'salesType'=>$salesType,
				'refund'=>$refund,
				'jfId'=>$jfId,
				'createdAt'=>$getCreatedDate,
				'updatedAt'=>$getUpdatedDate,
				'entryDate'=>$getEntryDate,
				'serviceDate'=>$serviceDate,
				'expense'=>$expense,
				'client' => array(
					'clientId'=>$clientData->clientId,
					'clientName'=>$clientData->clientName,
					'companyName'=>$clientData->companyName,
					'contactNo'=>$clientData->contactNo,
					'contactNo1'=>$clientData->contactNo1,
					'emailId'=>$clientData->emailId,
					'gst'=>$clientData->gst,
					'address1'=>$clientData->address1,
					'isDisplay'=>$clientData->isDisplay,
					'createdAt'=>$clientData->createdAt,
					'updatedAt'=>$clientData->updatedAt,
					'professionId'=>$clientData->professionId,
					'stateAbb'=>$clientData->state->stateAbb,
					'cityId'=>$clientData->city->cityId
				),
				'company' => $getCompanyDetails,	
				'branch' => $getBranchDetails	
			);
			$data[$decodedData]['file'] = $arrayData;
		}
		
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}