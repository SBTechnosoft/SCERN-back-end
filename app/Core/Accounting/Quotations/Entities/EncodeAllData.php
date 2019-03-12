<?php
namespace ERP\Core\Accounting\Quotations\Entities;

use ERP\Core\Accounting\Quotations\Entities\Quotation;
use ERP\Core\Clients\Services\ClientService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\BranchDetail;
use ERP\Entities\Constants\ConstantClass;
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
		$decodedJson = json_decode($status,true);
		$deocodedJsonData = json_decode($decodedJson['quotationData']);
		$decodedDocumentData = json_decode($decodedJson['documentData']);
		$quotation = new Quotation();
		for($decodedData=0;$decodedData<count($deocodedJsonData);$decodedData++)
		{
			$quotationBillId[$decodedData] = $deocodedJsonData[$decodedData]->quotation_bill_id;
			$productArray[$decodedData] = $deocodedJsonData[$decodedData]->product_array;
			$quotationNumber[$decodedData] = $deocodedJsonData[$decodedData]->quotation_number;
			$total[$decodedData] = $deocodedJsonData[$decodedData]->total;
			$totalDiscounttype[$decodedData] = $deocodedJsonData[$decodedData]->total_discounttype;
			$totalDiscount[$decodedData] = $deocodedJsonData[$decodedData]->total_discount;
			$totalCgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]->total_cgst_percentage;
			$totalSgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]->total_sgst_percentage;
			$totalIgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]->total_igst_percentage;
			$extraCharge[$decodedData] = $deocodedJsonData[$decodedData]->extra_charge;
			$tax[$decodedData] = $deocodedJsonData[$decodedData]->tax;
			$grandTotal[$decodedData] = $deocodedJsonData[$decodedData]->grand_total;
			$remark[$decodedData] = $deocodedJsonData[$decodedData]->remark;
			$entryDate[$decodedData] = $deocodedJsonData[$decodedData]->entry_date;
			$clientId[$decodedData] = $deocodedJsonData[$decodedData]->client_id;
			$jfId[$decodedData] = $deocodedJsonData[$decodedData]->jf_id;
			$companyId[$decodedData] = $deocodedJsonData[$decodedData]->company_id;
			$branchId[$decodedData] = $deocodedJsonData[$decodedData]->branch_id;
			$createdAt[$decodedData] = $deocodedJsonData[$decodedData]->created_at;
			$updatedAt[$decodedData] = $deocodedJsonData[$decodedData]->updated_at;
			

			//get the client detail from database
			$encodeAllData = new EncodeAllData();
			$getClientDetails[$decodedData] = $encodeAllData->getClientData($clientId[$decodedData]);

			//get the company detail from database
			$companyDetail  = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);

			//get the Branch detail from database
			$branchDetail  = new BranchDetail();
			$getBranchDetails[$decodedData] = $branchDetail->getBranchDetails($branchId[$decodedData]);

			//convert amount(round) into their company's selected decimal points
			$total[$decodedData] = number_format($total[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$totalDiscount[$decodedData] = number_format($totalDiscount[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			
			$totalCgstPercentage[$decodedData] = number_format($totalCgstPercentage[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$totalSgstPercentage[$decodedData] = number_format($totalSgstPercentage[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$totalIgstPercentage[$decodedData] = number_format($totalIgstPercentage[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');

			$tax[$decodedData] = number_format($tax[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$grandTotal[$decodedData] = number_format($grandTotal[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			
			//date format conversion
			$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$quotation->setCreated_at($convertedCreatedDate);
			$getCreatedDate[$decodedData] = $quotation->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$quotation->setUpdated_at($convertedUpdatedDate);
				$getUpdatedDate[$decodedData] = $quotation->getUpdated_at();
			}
			if(strcmp($entryDate[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getEntryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedEntryDate = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
				$quotation->setEntryDate($convertedEntryDate);
				$getEntryDate[$decodedData] = $quotation->getEntryDate();
			}
			$documentId[$decodedData] = array();
			$documentQuotationId[$decodedData] = array();
			$documentName[$decodedData] = array();
			$documentSize[$decodedData] = array();
			$documentFormat[$decodedData] = array();
			$documentType[$decodedData] = array();
			$documentCreatedAt[$decodedData] = array();
			$documentUpdatedAt[$decodedData] = array();
			$getDocumentCreatedDate[$decodedData] = array();
			$getDocumentUpdatedDate[$decodedData] = array();
			
			//get document data
			for($documentArray=0;$documentArray<count($decodedDocumentData[$decodedData]);$documentArray++)
			{
				$documentId[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_id;
				$documentQuotationId[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->quotation_bill_id;
				$documentName[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_name;
				$documentSize[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_size;
				$documentFormat[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_format;
				$documentType[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_type;
				$documentCreatedAt[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->created_at;
				$documentUpdatedAt[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->updated_at;
			
				//date format conversion
				if(strcmp($documentCreatedAt[$decodedData][$documentArray],'0000-00-00 00:00:00')==0)
				{
					$getDocumentCreatedDate[$decodedData][$documentArray] = "00-00-0000";
				}
				else
				{
					$documentCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $documentCreatedAt[$decodedData][$documentArray])->format('d-m-Y');
					$quotation->setCreated_at($documentCreatedDate);
					$getDocumentCreatedDate[$decodedData][$documentArray] = $quotation->getCreated_at();
				}
				if(strcmp($documentUpdatedAt[$decodedData][$documentArray],'0000-00-00 00:00:00')==0)
				{
					$getDocumentUpdatedDate[$decodedData][$documentArray] = "00-00-0000";
				}
				else
				{
					$documentUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $documentUpdatedAt[$decodedData][$documentArray])->format('d-m-Y');
					$quotation->setUpdated_at($documentUpdatedDate);
					$getDocumentUpdatedDate[$decodedData][$documentArray] = $quotation->getUpdated_at();
				}
			}
		}
		$documentData = array();
		$innerArrayData = array();
		$arrayData = array();
		$data = array();
		for($jsonData=0;$jsonData<count($deocodedJsonData);$jsonData++)
		{
			$arrayData[$jsonData] = array();
			for($innerArrayData=0;$innerArrayData<count($decodedDocumentData[$jsonData]);$innerArrayData++)
			{
				if(strcmp($documentFormat[$jsonData][$innerArrayData],"pdf")==0)
				{
					$arrayData[$jsonData][$innerArrayData] = array(
						'documentId'=>$documentId[$jsonData][$innerArrayData],
						'quotationBillId'=>$documentQuotationId[$jsonData][$innerArrayData],
						'documentName'=>$documentName[$jsonData][$innerArrayData],
						'documentSize'=>$documentSize[$jsonData][$innerArrayData],
						'documentFormat'=>$documentFormat[$jsonData][$innerArrayData],
						'documentType'=>$documentType[$jsonData][$innerArrayData],
						'documentUrl'=>$constantArray['quotationDocUrl'],
						'createdAt'=>$getDocumentCreatedDate[$jsonData][$innerArrayData],
						'updatedAt'=>$getDocumentUpdatedDate[$jsonData][$innerArrayData]
					);
				}	
				else
				{
					$arrayData[$jsonData][$innerArrayData] = array(
						'documentId'=>$documentId[$jsonData][$innerArrayData],
						'quotationBillId'=>$documentQuotationId[$jsonData][$innerArrayData],
						'documentName'=>$documentName[$jsonData][$innerArrayData],
						'documentSize'=>$documentSize[$jsonData][$innerArrayData],
						'documentFormat'=>$documentFormat[$jsonData][$innerArrayData],
						'documentType'=>$documentType[$jsonData][$innerArrayData],
						'documentUrl'=>$constantArray['billDocumentUrl'],
						'createdAt'=>$getDocumentCreatedDate[$jsonData][$innerArrayData],
						'updatedAt'=>$getDocumentUpdatedDate[$jsonData][$innerArrayData]
					);
				}
			}
			$clientData = json_decode($getClientDetails[$jsonData]);
			$data[$jsonData]= array(
				'quotationBillId'=>$quotationBillId[$jsonData],
				'productArray'=>$productArray[$jsonData],
				'quotationNumber'=>$quotationNumber[$jsonData],
				'total'=>$total[$jsonData],
				'totalDiscounttype'=>$totalDiscounttype[$jsonData],
				'totalDiscount'=>$totalDiscount[$jsonData],
				'totalCgstPercentage'=>$totalCgstPercentage[$jsonData],
				'totalSgstPercentage'=>$totalSgstPercentage[$jsonData],
				'totalIgstPercentage'=>$totalIgstPercentage[$jsonData],
				'extraCharge'=>$extraCharge[$jsonData],
				'tax'=>$tax[$jsonData],
				'grandTotal'=>$grandTotal[$jsonData],
				'remark'=>$remark[$jsonData],
				'jfId'=>$jfId[$jsonData],
				'createdAt'=>$getCreatedDate[$jsonData],
				'updatedAt'=>$getUpdatedDate[$jsonData],
				'entryDate'=>$getEntryDate[$jsonData],
				'client' => array(
					'clientId'=>$clientData->clientId,
					'clientName'=>$clientData->clientName,
					'companyName'=>$clientData->companyName,
					'contactNo'=>$clientData->contactNo,
					'contactNo1'=>$clientData->contactNo1,
					'emailId'=>$clientData->emailId,
					'professionId'=>$clientData->professionId,
					'address1'=>$clientData->address1,
					'gst'=>$clientData->gst,
					'isDisplay'=>$clientData->isDisplay,
					'createdAt'=>$clientData->createdAt,
					'updatedAt'=>$clientData->updatedAt,
					'stateAbb'=>$clientData->state->stateAbb,
					'cityId'=>$clientData->city->cityId
				),
				'company' => $getCompanyDetails[$jsonData],
				'branch' => $getBranchDetails[$jsonData]
			);
			if (isset($deocodedJsonData[$jsonData]->workflow_status_id)) {
				$data[$jsonData]['statusId'] = $deocodedJsonData[$jsonData]->workflow_status_id;
				$data[$jsonData]['processStatusDtlId'] = $deocodedJsonData[$jsonData]->process_status_dtl_id;
				$data[$jsonData]['assignedTo'] = $deocodedJsonData[$jsonData]->assigned_to;
				$data[$jsonData]['assignedBy'] = $deocodedJsonData[$jsonData]->assigned_by;
			}
			$data[$jsonData]['file'] = $arrayData[$jsonData];
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
	public function getEncodedStatusData($status)
	{
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$encodeAllData[$decodedData] = [
				'statusId' => $decodedJson[$decodedData]['status_id'],
				'status' => ucfirst($decodedJson[$decodedData]['status_name']),
				'statusType'=>$decodedJson[$decodedData]['status_position']
			];
		}
		return json_encode($encodeAllData);
	}
	public function getEncodedStatusCountData($status)
	{
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$encodeAllData[$decodedData] = [
				'statusId' => $decodedJson[$decodedData]['status_id'],
				'status' => ucfirst($decodedJson[$decodedData]['status_name']),
				'statusCount'=>$decodedJson[$decodedData]['status_count']
			];
		}
		return json_encode($encodeAllData);
	}
}