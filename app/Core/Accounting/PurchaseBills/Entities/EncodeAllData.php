<?php
namespace ERP\Core\Accounting\PurchaseBills\Entities;

use ERP\Core\Accounting\Ledgers\Services\LedgerService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Entities\Constants\ConstantClass;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends LedgerService
{
	public function getEncodedAllData($status)
	{
		$constantClass = new ConstantClass();		
		$constantArray = $constantClass->constantVariable();
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$deocodedJsonData = json_decode($status);
		$companyArray = array();
		$ledgerArray = array();
		$companyDetail  = new CompanyDetail();
		$ledgerService =  new EncodeAllData();
		for($decodedData=0;$decodedData<count($deocodedJsonData);$decodedData++)
		{
			$vendorId[$decodedData] = $deocodedJsonData[$decodedData]->vendor_id;
			$billNumber[$decodedData] = $deocodedJsonData[$decodedData]->bill_number;
			$purchaseId[$decodedData] = $deocodedJsonData[$decodedData]->purchase_id;
			$transactionDate[$decodedData] = $deocodedJsonData[$decodedData]->transaction_date;
			$entryDate[$decodedData] = $deocodedJsonData[$decodedData]->entry_date;
			$dueDate[$decodedData] = $deocodedJsonData[$decodedData]->due_date;
			$transactionType[$decodedData] = $deocodedJsonData[$decodedData]->transaction_type;
			$billType[$decodedData] = $deocodedJsonData[$decodedData]->bill_type;
			$productArray[$decodedData] = $deocodedJsonData[$decodedData]->product_array;
			$paymentMode[$decodedData] = $deocodedJsonData[$decodedData]->payment_mode;
			$bankLedgerId[$decodedData] = $deocodedJsonData[$decodedData]->bank_ledger_id;
			$bankName[$decodedData] = $deocodedJsonData[$decodedData]->bank_name;
			$checkNumber[$decodedData] = $deocodedJsonData[$decodedData]->check_number;
			$total[$decodedData] = $deocodedJsonData[$decodedData]->total;
			$totalDiscounttype[$decodedData] = $deocodedJsonData[$decodedData]->total_discounttype;
			$totalDiscount[$decodedData] = $deocodedJsonData[$decodedData]->total_discount;
			$totalCgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]->total_cgst_percentage;
			$totalSgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]->total_sgst_percentage;
			$totalIgstPercentage[$decodedData] = $deocodedJsonData[$decodedData]->total_igst_percentage;
			$extraCharge[$decodedData] = $deocodedJsonData[$decodedData]->extra_charge;
			$tax[$decodedData] = $deocodedJsonData[$decodedData]->tax;
			$grandTotal[$decodedData] = $deocodedJsonData[$decodedData]->grand_total;
			$advance[$decodedData] = $deocodedJsonData[$decodedData]->advance;
			$balance[$decodedData] = $deocodedJsonData[$decodedData]->balance;
			$remark[$decodedData] = $deocodedJsonData[$decodedData]->remark;
			$jfId[$decodedData] = $deocodedJsonData[$decodedData]->jf_id;
			$expense[$decodedData] = $deocodedJsonData[$decodedData]->expense ? : '[]';
			$documentData = $deocodedJsonData[$decodedData]->file;
			$companyId[$decodedData] = $deocodedJsonData[$decodedData]->company_id;
			$createdAt[$decodedData] = $deocodedJsonData[$decodedData]->created_at;
			$updatedAt[$decodedData] = $deocodedJsonData[$decodedData]->updated_at;
			//get the company detail from database
			if (!isset($companyArray[$companyId[$decodedData]])) {
				$companyArray[$companyId[$decodedData]] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			}
			$getCompanyDetails[$decodedData] = $companyArray[$companyId[$decodedData]];
			//get vendor(ledger) detail from database
			if (!isset($ledgerArray[$vendorId[$decodedData]])) {
				$getLedgerDetails[$decodedData] = $ledgerService->getLedgerData($vendorId[$decodedData]);
				$ledgerArray[$vendorId[$decodedData]] = json_decode($getLedgerDetails[$decodedData]);
			}
			$decodedLedgerDetail[$decodedData] = $ledgerArray[$vendorId[$decodedData]];
			
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
			//date format conversion
			$getCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
			}
			if(strcmp($transactionDate[$decodedData],'0000-00-00')==0)
			{
				$getTransactionDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getTransactionDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $transactionDate[$decodedData])->format('d-m-Y');
			}
			if(strcmp($entryDate[$decodedData],'0000-00-00')==0)
			{
				$getEntryDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getEntryDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $entryDate[$decodedData])->format('d-m-Y');
			}
			if(strcmp($dueDate[$decodedData],'0000-00-00')==0)
			{
				$getDueDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$getDueDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $dueDate[$decodedData])->format('d-m-Y');
			}
			$defaultFileArray = array(['documentId'=> 0,'purchaseId'=>0,'documentName'=>'','documentSize'=>'0','documentFormat'=>'','createdAt'=>'00-00-0000','updatedAt'=>'00-00-0000']);

			$arrayData = $documentData ? json_decode($documentData,true) : $defaultFileArray;

			$arrayData = array_map(function($ar) use ($constantArray){
				$ar['documentUrl'] = $constantArray['purchaseBillDocUrl'];
				return $ar;
			}, $arrayData);

			$expense[$decodedData] = json_decode($expense[$decodedData]);
			$data[$decodedData]= array(
				'purchaseId'=>$purchaseId[$decodedData],
				'productArray'=>$productArray[$decodedData],
				'billNumber'=>$billNumber[$decodedData],
				'transactionType'=>$transactionType[$decodedData],
				'billType'=>$billType[$decodedData],
				'paymentMode'=>$paymentMode[$decodedData],
				'bankLedgerId'=>$bankLedgerId[$decodedData],
				'bankName'=>$bankName[$decodedData],
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
				'remark'=>$remark[$decodedData],
				'jfId'=>$jfId[$decodedData],
				'expense'=>$expense[$decodedData],
				'createdAt'=>$getCreatedDate[$decodedData],
				'updatedAt'=>$getUpdatedDate[$decodedData],
				'transactionDate'=>$getTransactionDate[$decodedData],
				'entryDate'=>$getEntryDate[$decodedData],
				'dueDate'=>$getDueDate[$decodedData],
				'vendor' =>$decodedLedgerDetail[$decodedData],
				'company' => array(	
					'companyId' => $getCompanyDetails[$decodedData]['companyId'],
					'companyName' => $getCompanyDetails[$decodedData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$decodedData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$decodedData]['address1'],	
					'address2'=> $getCompanyDetails[$decodedData]['address2'],	
					'pincode' => $getCompanyDetails[$decodedData]['pincode'],	
					'pan' => $getCompanyDetails[$decodedData]['pan'],	
					'tin'=> $getCompanyDetails[$decodedData]['tin'],	
					'cgst'=> $getCompanyDetails[$decodedData]['cgst'],	
					'sgst'=> $getCompanyDetails[$decodedData]['sgst'],	
					'vatNo' => $getCompanyDetails[$decodedData]['vatNo'],	
					'serviceTaxNo' => $getCompanyDetails[$decodedData]['serviceTaxNo'],	
					'basicCurrencySymbol' => $getCompanyDetails[$decodedData]['basicCurrencySymbol'],	
					'formalName' => $getCompanyDetails[$decodedData]['formalName'],	
					'noOfDecimalPoints' => $getCompanyDetails[$decodedData]['noOfDecimalPoints'],	
					'currencySymbol' => $getCompanyDetails[$decodedData]['currencySymbol'],	
					'logo'=> array(
						'documentName' => $getCompanyDetails[$decodedData]['logo']['documentName'],
						'documentUrl' => $getCompanyDetails[$decodedData]['logo']['documentUrl'],	
						'documentSize' =>$getCompanyDetails[$decodedData]['logo']['documentSize'],	
						'documentFormat' => $getCompanyDetails[$decodedData]['logo']['documentFormat']
					),
					'isDisplay' => $getCompanyDetails[$decodedData]['isDisplay'],	
					'isDefault' => $getCompanyDetails[$decodedData]['isDefault'],
					'createdAt' => $getCompanyDetails[$decodedData]['createdAt'],
					'updatedAt' => $getCompanyDetails[$decodedData]['updatedAt'],
					'stateAbb' => $getCompanyDetails[$decodedData]['state']['stateAbb'],
					'cityId' => $getCompanyDetails[$decodedData]['city']['cityId']	
				)		
			);
			$data[$decodedData]['file'] = $arrayData;
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}