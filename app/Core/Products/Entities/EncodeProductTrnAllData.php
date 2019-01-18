<?php
namespace ERP\Core\Products\Entities;

use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Core\Products\Services\ProductService;
use ERP\Core\Entities\CompanyDetail;
use ERP\Core\Entities\BranchDetail;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeProductTrnAllData extends ProductService
{
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$productDecodedJson = array();
		$ledger = new Ledger();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$transactionDate[$decodedData] = $decodedJson[$decodedData]['transaction_date'];
			$productTransactionId[$decodedData] = $decodedJson[$decodedData]['product_trn_id'];
			$transactionType[$decodedData] = $decodedJson[$decodedData]['transaction_type'];
			$qty[$decodedData] = $decodedJson[$decodedData]['qty'];
			$price[$decodedData] = $decodedJson[$decodedData]['price'];
			$discount[$decodedData] = $decodedJson[$decodedData]['discount'];
			$discountType[$decodedData] = $decodedJson[$decodedData]['discount_type'];
			$discountValue[$decodedData] = $decodedJson[$decodedData]['discount_value'];
			$tax[$decodedData] = $decodedJson[$decodedData]['tax'];
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			$invoiceNumber[$decodedData] = $decodedJson[$decodedData]['invoice_number'];
			$billNumber[$decodedData] = $decodedJson[$decodedData]['bill_number'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$branchId[$decodedData] = $decodedJson[$decodedData]['branch_id'];
			$jfId[$decodedData] = $decodedJson[$decodedData]['jf_id'];
			$productId[$decodedData] = $decodedJson[$decodedData]['product_id'];
			
			//get the product detail from database
			$encodeDataClass = new EncodeProductTrnAllData();
			$productStatus[$decodedData] = $encodeDataClass->getProductData($productId[$decodedData]);
			$productDecodedJson[$decodedData] = json_decode($productStatus[$decodedData],true);
			
			//get the company details from database
			$companyDetail = new CompanyDetail();
			$getCompanyDetails[$decodedData] = $companyDetail->getCompanyDetails($companyId[$decodedData]);
			
			//get the branch detail from database
			$branchDetail  = new BranchDetail();
			$getBranchDetails[$decodedData] = $branchDetail->getBranchDetails($branchId[$decodedData]);
			
			//convert amount(number_format) into their company's selected decimal points
			$price[$decodedData] = number_format($price[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$discountValue[$decodedData] = number_format($discountValue[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			$tax[$decodedData] = number_format($tax[$decodedData],$getCompanyDetails[$decodedData]['noOfDecimalPoints'],'.','');
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$ledger->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $ledger->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$ledger->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $ledger->getUpdated_at();
			}
			if(strcmp($transactionDate[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getTransactionDate[$decodedData]="00-00-0000";
			}
			else
			{
				$convertedTransactionDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d', $transactionDate[$decodedData])->format('d-m-Y');
				$ledger->setTransactionDate($convertedTransactionDate[$decodedData]);
				$getTransactionDate[$decodedData] = $ledger->getTransactionDate();
			}
		}
		$constantArray = new ConstantClass();
		$constantArrayData = $constantArray->constantVariable();
		$documentPath = $constantArrayData['productBarcode'];

		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'productTransactionId'=>$productTransactionId[$jsonData],
				'transactionType' => $transactionType[$jsonData],
				'qty' => $qty[$jsonData],
				'price' => $price[$jsonData],
				'discount' => $discount[$jsonData],
				'discountValue' => $discountValue[$jsonData],
				'discountType' => $discountType[$jsonData],
				'isDisplay' => $isDisplay[$jsonData],
				'tax' => $tax[$jsonData],
				'invoiceNumber' => $invoiceNumber[$jsonData],
				'billNumber' => $billNumber[$jsonData],
				'jfId' => $jfId[$jsonData],
				'transactionDate' => $getTransactionDate[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				
				'product' => $productDecodedJson[$jsonData],
				'company' => array(
					'companyId' => $getCompanyDetails[$jsonData]['companyId'],	
					'companyName' => $getCompanyDetails[$jsonData]['companyName'],	
					'companyDisplayName' => $getCompanyDetails[$jsonData]['companyDisplayName'],	
					'websiteName' => $getCompanyDetails[$jsonData]['websiteName'],	
					'address1' => $getCompanyDetails[$jsonData]['address1'],	
					'address2'=> $getCompanyDetails[$jsonData]['address2'],	
					'emailId'=> $getCompanyDetails[$jsonData]['emailId'],	
					'customerCare'=> $getCompanyDetails[$jsonData]['customerCare'],	
					'pincode' => $getCompanyDetails[$jsonData]['pincode'],	
					'pan' => $getCompanyDetails[$jsonData]['pan'],	
					'tin'=> $getCompanyDetails[$jsonData]['tin'],	
					'vatNo' => $getCompanyDetails[$jsonData]['vatNo'],	
					'cgst' => $getCompanyDetails[$jsonData]['cgst'],	
					'sgst' => $getCompanyDetails[$jsonData]['sgst'],	
					'cess' => $getCompanyDetails[$jsonData]['cess'],	
					'serviceTaxNo' => $getCompanyDetails[$jsonData]['serviceTaxNo'],	
					'basicCurrencySymbol' => $getCompanyDetails[$jsonData]['basicCurrencySymbol'],	
					'formalName' => $getCompanyDetails[$jsonData]['formalName'],	
					'noOfDecimalPoints' => $getCompanyDetails[$jsonData]['noOfDecimalPoints'],	
					'currencySymbol' => $getCompanyDetails[$jsonData]['currencySymbol'],
					'printType' => $getCompanyDetails[$jsonData]['printType'],
					'logo' => array(
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
				),
				'branch' => array(
					'branchId' => $getBranchDetails[$jsonData]['branchId'],	
					'branchName'=> $getBranchDetails[$jsonData]['branchName'],	
					'address1' => $getBranchDetails[$jsonData]['address1'],	
					'address2' => $getBranchDetails[$jsonData]['address2'],	
					'pincode' => $getBranchDetails[$jsonData]['pincode'],	
					'isDisplay' => $getBranchDetails[$jsonData]['isDisplay'],	
					'isDefault' => $getBranchDetails[$jsonData]['isDefault'],	
					'createdAt' => $getBranchDetails[$jsonData]['createdAt'],	
					'updatedAt' => $getBranchDetails[$jsonData]['updatedAt'],	
					'stateAbb' => $getBranchDetails[$jsonData]['state']['stateAbb'],	
					'cityId' => $getBranchDetails[$jsonData]['city']['cityId'],	
					'companyId' => $getBranchDetails[$jsonData]['company']['companyId']	
				)
			);
		}
		
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}