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
		$decodedJson = json_decode($status,true);
		$deocodedJsonData = json_decode($decodedJson['purchaseBillData']);
		$decodedDocumentData = json_decode($decodedJson['documentData']);
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
			$expense[$decodedData] = $deocodedJsonData[$decodedData]->expense;
			$companyId[$decodedData] = $deocodedJsonData[$decodedData]->company_id;
			$createdAt[$decodedData] = $deocodedJsonData[$decodedData]->created_at;
			$updatedAt[$decodedData] = $deocodedJsonData[$decodedData]->updated_at;
			//get the company detail from database
			$companyDetail  = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			//get vendor(ledger) detail from database
			$ledgerService =  new EncodeAllData();
			$getLedgerDetails[$decodedData] = $ledgerService->getLedgerData($vendorId[$decodedData]);
			$decodedLedgerDetail[$decodedData] = json_decode($getLedgerDetails[$decodedData]);
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
			$documentId[$decodedData] = array();
			$documentSaleId[$decodedData] = array();
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
				$documentPurchaseId[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->purchase_id;
				$documentName[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_name;
				$documentSize[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_size;
				$documentFormat[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->document_format;
				$documentCreatedAt[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->created_at;
				$documentUpdatedAt[$decodedData][$documentArray] = $decodedDocumentData[$decodedData][$documentArray]->updated_at;
				//date format conversion
				if(strcmp($documentCreatedAt[$decodedData][$documentArray],'0000-00-00 00:00:00')==0)
				{
					$getDocumentCreatedDate[$decodedData][$documentArray] = "00-00-0000";
				}
				else
				{
					$getDocumentCreatedDate[$decodedData][$documentArray] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $documentCreatedAt[$decodedData][$documentArray])->format('d-m-Y');
				}
				if(strcmp($documentUpdatedAt[$decodedData][$documentArray],'0000-00-00 00:00:00')==0)
				{
					$getDocumentUpdatedDate[$decodedData][$documentArray] = "00-00-0000";
				}
				else
				{
					$getDocumentUpdatedDate[$decodedData][$documentArray] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $documentUpdatedAt[$decodedData][$documentArray])->format('d-m-Y');
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
				if(strcmp($documentFormat[$jsonData][$innerArrayData],"pdf")!=0)
				{
					$arrayData[$jsonData][$innerArrayData] = array(
						'documentId'=>$documentId[$jsonData][$innerArrayData],
						'purchaseId'=>$documentPurchaseId[$jsonData][$innerArrayData],
						'documentName'=>$documentName[$jsonData][$innerArrayData],
						'documentSize'=>$documentSize[$jsonData][$innerArrayData],
						'documentFormat'=>$documentFormat[$jsonData][$innerArrayData],
						'documentUrl'=>$constantArray['purchaseBillDocUrl'],
						'createdAt'=>$getDocumentCreatedDate[$jsonData][$innerArrayData],
						'updatedAt'=>$getDocumentUpdatedDate[$jsonData][$innerArrayData]
					);
				}
			}
			$data[$jsonData]= array(
				'purchaseId'=>$purchaseId[$jsonData],
				'productArray'=>$productArray[$jsonData],
				'billNumber'=>$billNumber[$jsonData],
				'transactionType'=>$transactionType[$jsonData],
				'billType'=>$billType[$jsonData],
				'paymentMode'=>$paymentMode[$jsonData],
				'bankLedgerId'=>$bankLedgerId[$jsonData],
				'bankName'=>$bankName[$jsonData],
				'checkNumber'=>$checkNumber[$jsonData],
				'total'=>$total[$jsonData],
				'totalDiscounttype'=>$totalDiscounttype[$jsonData],
				'totalDiscount'=>$totalDiscount[$jsonData],
				'totalCgstPercentage'=>$totalCgstPercentage[$jsonData],
				'totalSgstPercentage'=>$totalSgstPercentage[$jsonData],
				'totalIgstPercentage'=>$totalIgstPercentage[$jsonData],
				'extraCharge'=>$extraCharge[$jsonData],
				'tax'=>$tax[$jsonData],
				'grandTotal'=>$grandTotal[$jsonData],
				'advance'=>$advance[$jsonData],
				'balance'=>$balance[$jsonData],
				'remark'=>$remark[$jsonData],
				'jfId'=>$jfId[$jsonData],
				'expense'=>$expense[$jsonData],
				'createdAt'=>$getCreatedDate[$jsonData],
				'updatedAt'=>$getUpdatedDate[$jsonData],
				'transactionDate'=>$getTransactionDate[$jsonData],
				'entryDate'=>$getEntryDate[$jsonData],
				'dueDate'=>$getDueDate[$jsonData],
				'vendor' =>$decodedLedgerDetail[$jsonData],
				'company' => array(	
					'companyId' => $getCompanyDetails[$jsonData]['companyId'],
					'companyName' => $getCompanyDetails[$jsonData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$jsonData]['companyDisplayName'],	
					'address1' => $getCompanyDetails[$jsonData]['address1'],	
					'address2'=> $getCompanyDetails[$jsonData]['address2'],	
					'pincode' => $getCompanyDetails[$jsonData]['pincode'],	
					'pan' => $getCompanyDetails[$jsonData]['pan'],	
					'tin'=> $getCompanyDetails[$jsonData]['tin'],	
					'cgst'=> $getCompanyDetails[$jsonData]['cgst'],	
					'sgst'=> $getCompanyDetails[$jsonData]['sgst'],	
					'vatNo' => $getCompanyDetails[$jsonData]['vatNo'],	
					'serviceTaxNo' => $getCompanyDetails[$jsonData]['serviceTaxNo'],	
					'basicCurrencySymbol' => $getCompanyDetails[$jsonData]['basicCurrencySymbol'],	
					'formalName' => $getCompanyDetails[$jsonData]['formalName'],	
					'noOfDecimalPoints' => $getCompanyDetails[$jsonData]['noOfDecimalPoints'],	
					'currencySymbol' => $getCompanyDetails[$jsonData]['currencySymbol'],	
					'logo'=> array(
						'documentName' => $getCompanyDetails[$jsonData]['logo']['documentName'],
						'documentUrl' => $getCompanyDetails[$jsonData]['logo']['documentUrl'],	
						'documentSize' =>$getCompanyDetails[$jsonData]['logo']['documentSize'],	
						'documentFormat' => $getCompanyDetails[$jsonData]['logo']['documentFormat']
					),
					'isDisplay' => $getCompanyDetails[$jsonData]['isDisplay'],	
					'isDefault' => $getCompanyDetails[$jsonData]['isDefault'],
					'createdAt' => $getCompanyDetails[$jsonData]['createdAt'],
					'updatedAt' => $getCompanyDetails[$jsonData]['updatedAt'],
					'stateAbb' => $getCompanyDetails[$jsonData]['state']['stateAbb'],
					'cityId' => $getCompanyDetails[$jsonData]['city']['cityId']	
				)		
			);
			$data[$jsonData]['file'] = $arrayData[$jsonData];
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}