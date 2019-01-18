<?php
namespace ERP\Core\Accounting\Quotations\Entities;

use ERP\Core\Accounting\Quotations\Entities\Quotation;
use ERP\Core\Clients\Services\ClientService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\BranchDetail;
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
		$quotationBillId= $decodedJson[0]['quotation_bill_id'];
		$productArray= $decodedJson[0]['product_array'];
		$quotationNumber= $decodedJson[0]['quotation_number'];
		$total= $decodedJson[0]['total'];
		$totalDiscounttype = $decodedJson[0]['total_discounttype'];
		$totalDiscount = $decodedJson[0]['total_discount'];
		$totalCgstPercentage = $decodedJson[0]['total_cgst_percentage'];
		$totalSgstPercentage = $decodedJson[0]['total_sgst_percentage'];
		$totalIgstPercentage = $decodedJson[0]['total_igst_percentage'];
		$extraCharge= $decodedJson[0]['extra_charge'];
		$tax= $decodedJson[0]['tax'];
		$grandTotal= $decodedJson[0]['grand_total'];
		$remark= $decodedJson[0]['remark'];
		$entryDate= $decodedJson[0]['entry_date'];
		$clientId= $decodedJson[0]['client_id'];
		$jfId= $decodedJson[0]['jf_id'];
		$companyId= $decodedJson[0]['company_id'];
		$branchId= $decodedJson[0]['branch_id'];
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

		//convert amount(round) into their company's selected decimal points
		$total = number_format($total,$companyDetails['noOfDecimalPoints'],'.','');
		$totalDiscount = number_format($totalDiscount,$companyDetails['noOfDecimalPoints'],'.','');
		$totalCgstPercentage = number_format($totalCgstPercentage,$companyDetails['noOfDecimalPoints'],'.','');
		$totalSgstPercentage = number_format($totalSgstPercentage,$companyDetails['noOfDecimalPoints'],'.','');
		$totalIgstPercentage = number_format($totalIgstPercentage,$companyDetails['noOfDecimalPoints'],'.','');
		$tax = number_format($tax,$companyDetails['noOfDecimalPoints'],'.','');
		$grandTotal = number_format($grandTotal,$companyDetails['noOfDecimalPoints'],'.','');
		
		//date format conversion
		$quotation = new Quotation();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$quotation->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $quotation->getCreated_at();
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$quotation->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $quotation->getUpdated_at();
		}
		if(strcmp($entryDate,'0000-00-00 00:00:00')==0)
		{
			$getEntryDate = "00-00-0000";
		}
		else
		{
			$convertedEntryDate = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate)->format('d-m-Y');
			$quotation->setEntryDate($convertedEntryDate);
			$getEntryDate = $quotation->getEntryDate();
		}
		//set all data into json array
		$data = array();
		$data['quotationBillId'] = $quotationBillId;
		$data['productArray'] = $productArray;
		$data['quotationNumber'] = $quotationNumber;
		$data['total'] = $total;
		$data['totalDiscounttype'] = $totalDiscounttype;
		$data['totalDiscount'] = $totalDiscount;
		$data['totalCgstPercentage'] = $totalCgstPercentage;
		$data['totalSgstPercentage'] = $totalSgstPercentage;
		$data['totalIgstPercentage'] = $totalIgstPercentage;
		$data['extraCharge'] = $extraCharge;
		$data['tax'] = $tax;
		$data['grandTotal'] = $grandTotal;
		$data['createdAt'] = $getCreatedDate;
		$data['remark'] = $remark;
		$data['entryDate'] = $getEntryDate;
		$data['clientId'] = $clientId;
		$data['jfId'] = $jfId;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['client']= array(
			'clientId' => $clientDecodedJson['clientId'],	
			'clientName' => $clientDecodedJson['clientName'],	
			'companyName' => $clientDecodedJson['companyName'],	
			'contactNo' => $clientDecodedJson['contactNo'],	
			'contactNo1' => $clientDecodedJson['contactNo1'],	
			'emailId' => $clientDecodedJson['emailId'],	
			'professionId' => $clientDecodedJson['professionId'],	
			'address1' => $clientDecodedJson['address1'],		
			'gst' => $clientDecodedJson['gst'],		
			'isDisplay' => $clientDecodedJson['isDisplay'],	
			'createdAt' => $clientDecodedJson['createdAt'],	
			'updatedAt' => $clientDecodedJson['updatedAt'],	
			'stateAbb' => $clientDecodedJson['state']['stateAbb'],	
			'cityId' => $clientDecodedJson['city']['cityId']
		);
		$data['company']= $companyDetails;
		$data['branch']= $branchDetails;
		$encodeData = json_encode($data);
		return $encodeData;
	}
}