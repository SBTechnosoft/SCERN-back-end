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
class EncodeAllDraftData extends ClientService
{
	public function getEncodedAllData($status)
	{
		$constantClass = new ConstantClass();		
		$constantArray = $constantClass->constantVariable();
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$deocodedJsonData = json_decode($status,true);
		$bill = new Bill();
		$encodeAllData = new EncodeAllData();
		$companyDetail  = new CompanyDetail();
		$branchDetail  = new BranchDetail();
		$userService = new UserService();
		$clientArray = array();
		$companyArray = array();
		$branchArray = array();
		$userArray = array();

		for($decodedData=0;$decodedData<count($deocodedJsonData);$decodedData++)
		{
			$saleId[$decodedData] = $deocodedJsonData[$decodedData]['sale_id'];
			$productArray[$decodedData] = $deocodedJsonData[$decodedData]['product_array'];
			$paymentMode[$decodedData] = $deocodedJsonData[$decodedData]['payment_mode'];
			$bankName[$decodedData] = $deocodedJsonData[$decodedData]['bank_name'];
			$invoiceNumber[$decodedData] = $deocodedJsonData[$decodedData]['invoice_number'];
			$jobCardNumber[$decodedData] = $deocodedJsonData[$decodedData]['job_card_number'];
			$checkNumber[$decodedData] = $deocodedJsonData[$decodedData]['check_number'];
			$total[$decodedData] = $deocodedJsonData[$decodedData]['total'];
			$totalDiscounttype[$decodedData] = $deocodedJsonData[$decodedData]['total_discounttype'];
			$totalDiscount[$decodedData] = $deocodedJsonData[$decodedData]['total_discount'];
			$totalCgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]['total_cgst_percentage'];
			$totalSgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]['total_sgst_percentage'];
			$totalIgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]['total_igst_percentage'];
			$extraCharge[$decodedData] = $deocodedJsonData[$decodedData]['extra_charge'];
			$tax[$decodedData] = $deocodedJsonData[$decodedData]['tax'];
			$grandTotal[$decodedData] = $deocodedJsonData[$decodedData]['grand_total'];
			$advance[$decodedData] = $deocodedJsonData[$decodedData]['advance'];
			$balance[$decodedData] = $deocodedJsonData[$decodedData]['balance'];
			$poNumber[$decodedData] = $deocodedJsonData[$decodedData]['po_number'];
			$userId[$decodedData] = $deocodedJsonData[$decodedData]['user_id'];
			$remark[$decodedData] = $deocodedJsonData[$decodedData]['remark'];
			$refund[$decodedData] = $deocodedJsonData[$decodedData]['refund'];
			$entryDate[$decodedData] = $deocodedJsonData[$decodedData]['entry_date'];
			$serviceDate[$decodedData] = $deocodedJsonData[$decodedData]['service_date'];
			$clientId[$decodedData] = $deocodedJsonData[$decodedData]['client_id'];
			$jfId[$decodedData] = $deocodedJsonData[$decodedData]['jf_id'];
			$salesType[$decodedData] = $deocodedJsonData[$decodedData]['sales_type'];
			$companyId[$decodedData] = $deocodedJsonData[$decodedData]['company_id'];
			$branchId[$decodedData] = $deocodedJsonData[$decodedData]['branch_id'];
			$createdAt[$decodedData] = $deocodedJsonData[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $deocodedJsonData[$decodedData]['updated_at'];
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
			
			//get the user detail from database
			if (!isset($userArray[$userId[$decodedData]])) {
				$userData = $userService->getUserData($userId[$decodedData]);
				$userArray[$userId[$decodedData]] = json_decode($userData);
			}
			
			$decodedUserData[$decodedData] = $userArray[$userId[$decodedData]];
			

			//convert amount(round) into their company's selected decimal points
			$total[$decodedData] = number_format($total[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$totalDiscount[$decodedData] = number_format($totalDiscount[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');

			$totalCgstPercentage[$decodedData] = number_format($totalCgstPercentage[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$totalSgstPercentage[$decodedData] = number_format($totalSgstPercentage[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$totalIgstPercentage[$decodedData] = number_format($totalIgstPercentage[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');

			$tax[$decodedData] = number_format($tax[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$grandTotal[$decodedData] = number_format($grandTotal[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$advance[$decodedData] = number_format($advance[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$balance[$decodedData] = number_format($balance[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$refund[$decodedData] = number_format($refund[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			//date format conversion
			$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$bill->setCreated_at($convertedCreatedDate);
			$getCreatedDate[$decodedData] = $bill->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$bill->setUpdated_at($convertedUpdatedDate);
				$getUpdatedDate[$decodedData] = $bill->getUpdated_at();
			}
			if(strcmp($entryDate[$decodedData],'0000-00-00')==0)
			{
				$getEntryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedEntryDate = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
				$bill->setEntryDate($convertedEntryDate);
				$getEntryDate[$decodedData] = $bill->getEntryDate();
			}
			if(strcmp($serviceDate[$decodedData],'0000-00-00')==0)
			{
				$serviceDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$serviceDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $serviceDate[$decodedData])->format('d-m-Y');
				
			}
			$clientData = json_decode($getClientDetails[$decodedData]);
			$data[$decodedData]= array(
				'saleId'=>$saleId[$decodedData],
				'productArray'=>$productArray[$decodedData],
				'paymentMode'=>$paymentMode[$decodedData],
				'bankName'=>$bankName[$decodedData],
				'invoiceNumber'=>$invoiceNumber[$decodedData],
				'jobCardNumber'=>$jobCardNumber[$decodedData],
				'checkNumber'=>$checkNumber[$decodedData],
				'total'=>$total[$decodedData],
				'totalDiscounttype'=>$totalDiscounttype[$decodedData],
				'totalDiscount'=>$totalDiscount[$decodedData],
				'totalCgstPercentage'=>$totalCgstPercentage[$decodedData],
				'totalSgstPercentage'=>$totalSgstPercentage[$decodedData],
				'totalIgstPercentage'=>$totalIgstPercentage[$decodedData],
				'extraCharge'=>$extraCharge[$decodedData],
				'tax'=>$tax[$decodedData],
				'grandTotal'=>$grandTotal[$decodedData],
				'advance'=>$advance[$decodedData],
				'balance'=>$balance[$decodedData],
				'poNumber'=>$poNumber[$decodedData],
				'user'=>$decodedUserData[$decodedData],
				'remark'=>$remark[$decodedData],
				'salesType'=>$salesType[$decodedData],
				'refund'=>$refund[$decodedData],
				'jfId'=>$jfId[$decodedData],
				'createdAt'=>$getCreatedDate[$decodedData],
				'updatedAt'=>$getUpdatedDate[$decodedData],
				'entryDate'=>$getEntryDate[$decodedData],
				'serviceDate'=>$serviceDate[$decodedData],
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
				'company' => $getCompanyDetails[$decodedData],
				'branch' => $getBranchDetails[$decodedData]
			);
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}