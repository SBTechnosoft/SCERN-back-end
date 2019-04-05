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
		$deocodedJsonData = json_decode($status);
		$quotation = new Quotation();
		$clientArray = array();
		$companyArray = array();
		$branchArray = array();
		$encodeAllData = new EncodeAllData();
		$companyDetail  = new CompanyDetail();
		$branchDetail  = new BranchDetail();
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
			$decodedDocumentData = $deocodedJsonData[$decodedData]->file;
			

			//get the client detail from database
			if (!isset($clientArray[$clientId[$decodedData]])) {
				$clientArray[$clientId[$decodedData]] = $encodeAllData->getClientData($clientId[$decodedData]);
			}
			
			$getClientDetails[$decodedData] = $clientArray[$clientId[$decodedData]];

			//get the company detail from database
			if (!isset($companyArray[$companyId[$decodedData]])) {
				$companyArray[$companyId[$decodedData]] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			}
			$getCompanyDetails[$decodedData] = $companyArray[$companyId[$decodedData]];

			//get the Branch detail from database
			if (!isset($branchArray[$branchId[$decodedData]])) {
				$branchArray[$branchId[$decodedData]] = $branchDetail->getBranchDetails($branchId[$decodedData]);
			}
			$getBranchDetails[$decodedData] = $branchArray[$branchId[$decodedData]];

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
			$defaultFileArray = array([
				'documentId'=>0,
				'quotationBillId'=>0,
				'documentName'=>'',
				'documentSize'=>0,
				'documentFormat'=>'',
				'documentType'=>'quotation',
				'createdAt'=>'0000-00-00 00:00:00',
				'updatedAt'=>'0000-00-00 00:00:00'
			]);
			$arrayData = $decodedDocumentData != '' ? json_decode($decodedDocumentData,true) : $defaultFileArray;
			$arrayData = array_map(function($ar) use ($constantArray){
				$ar['documentUrl'] = strcmp($ar['documentFormat'],"pdf")==0 ? $constantArray['quotationDocUrl'] : $constantArray['billDocumentUrl'];
				return $ar;
			}, $arrayData);
			$clientData = json_decode($getClientDetails[$decodedData]);
			$data[$decodedData]= array(
				'quotationBillId'=>$quotationBillId[$decodedData],
				'productArray'=>$productArray[$decodedData],
				'quotationNumber'=>$quotationNumber[$decodedData],
				'total'=>$total[$decodedData],
				'totalDiscounttype'=>$totalDiscounttype[$decodedData],
				'totalDiscount'=>$totalDiscount[$decodedData],
				'totalCgstPercentage'=>$totalCgstPercentage[$decodedData],
				'totalSgstPercentage'=>$totalSgstPercentage[$decodedData],
				'totalIgstPercentage'=>$totalIgstPercentage[$decodedData],
				'extraCharge'=>$extraCharge[$decodedData],
				'tax'=>$tax[$decodedData],
				'grandTotal'=>$grandTotal[$decodedData],
				'remark'=>$remark[$decodedData],
				'jfId'=>$jfId[$decodedData],
				'createdAt'=>$getCreatedDate[$decodedData],
				'updatedAt'=>$getUpdatedDate[$decodedData],
				'entryDate'=>$getEntryDate[$decodedData],
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
				'company' => $getCompanyDetails[$decodedData],
				'branch' => $getBranchDetails[$decodedData]
			);
			if (isset($deocodedJsonData[$decodedData]->workflow_status_id)) {
				$data[$decodedData]['statusId'] = $deocodedJsonData[$decodedData]->workflow_status_id;
				$data[$decodedData]['processStatusDtlId'] = $deocodedJsonData[$decodedData]->process_status_dtl_id;
				$data[$decodedData]['assignedTo'] = $deocodedJsonData[$decodedData]->assigned_to;
				$data[$decodedData]['assignedBy'] = $deocodedJsonData[$decodedData]->assigned_by;
			}
			$data[$decodedData]['file'] = $arrayData;
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
				'statusCount'=>$decodedJson[$decodedData]['status_count'],
				'statusPosition'=>$decodedJson[$decodedData]['status_position']
			];
		}
		return json_encode($encodeAllData);
	}
}