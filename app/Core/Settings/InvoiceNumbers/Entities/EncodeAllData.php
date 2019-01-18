<?php
namespace ERP\Core\Settings\InvoiceNumbers\Entities;

use ERP\Core\Settings\InvoiceNumbers\Entities\InvoiceNumber;
use ERP\Core\Companies\Services\CompanyService;
use Carbon;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends CompanyService
{
	public function getEncodedAllData($status)
	{
		$convertedCreatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$invoice = new InvoiceNumber();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$invoiceId[$decodedData] = $decodedJson[$decodedData]['invoice_id'];
			$invoiceLabel[$decodedData] = $decodedJson[$decodedData]['invoice_label'];
			$invoiceType[$decodedData] = $decodedJson[$decodedData]['invoice_type'];
			$startAt[$decodedData] = $decodedJson[$decodedData]['start_at'];
			$endAt[$decodedData] = $decodedJson[$decodedData]['end_at'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			
			//get the company detail from database
			$encodeDataClass = new EncodeAllData();
			$companyStatus[$decodedData] = $encodeDataClass->getCompanyData($companyId[$decodedData]);
			$companyDecodedJson[$decodedData] = json_decode($companyStatus[$decodedData],true);
			$companyName[$decodedData]= $companyDecodedJson[$decodedData]['companyName'];
			$companyIsDisplay[$decodedData]= $companyDecodedJson[$decodedData]['isDisplay'];
			$companyCreatedAt[$decodedData]= $companyDecodedJson[$decodedData]['createdAt'];
			$companyUpdatedAt[$decodedData]= $companyDecodedJson[$decodedData]['updatedAt'];
			$companyDispName[$decodedData]= $companyDecodedJson[$decodedData]['companyDisplayName'];
			$companyAddress1[$decodedData]= $companyDecodedJson[$decodedData]['address1'];
			$companyAddress2[$decodedData]= $companyDecodedJson[$decodedData]['address2'];
			$companyPincode[$decodedData]= $companyDecodedJson[$decodedData]['pincode'];
			$companyPanNo[$decodedData]= $companyDecodedJson[$decodedData]['pan'];
			$companyTinNo[$decodedData]= $companyDecodedJson[$decodedData]['tin'];
			$companyVatNo[$decodedData]= $companyDecodedJson[$decodedData]['vatNo'];
			$companyServiceTaxNo[$decodedData]= $companyDecodedJson[$decodedData]['serviceTaxNo'];
			$companybasicCurrencySymbol[$decodedData]= $companyDecodedJson[$decodedData]['basicCurrencySymbol'];
			$companyFormalName[$decodedData]= $companyDecodedJson[$decodedData]['formalName'];
			$companyNoOfDecimalPoints[$decodedData]= $companyDecodedJson[$decodedData]['noOfDecimalPoints'];
			$companyCurrencySymbol[$decodedData]= $companyDecodedJson[$decodedData]['currencySymbol'];
			$companyDocumentName[$decodedData]= $companyDecodedJson[$decodedData]['logo']['documentName'];
			$companyDocumentUrl[$decodedData]= $companyDecodedJson[$decodedData]['logo']['documentUrl'];
			$companyDocumentSize[$decodedData]= $companyDecodedJson[$decodedData]['logo']['documentSize'];
			$companyDocumentFormat[$decodedData]= $companyDecodedJson[$decodedData]['logo']['documentFormat'];
			$companyIsDefault[$decodedData]= $companyDecodedJson[$decodedData]['isDefault'];
			$companyStateAbb[$decodedData]= $companyDecodedJson[$decodedData]['state']['stateAbb'];
			$companyCityId[$decodedData]= $companyDecodedJson[$decodedData]['city']['cityId'];
			
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$invoice->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $invoice->getCreated_at();
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$invoice->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $invoice->getUpdated_at();
			}
		}
		$data = array();
		for($jsonData=0;$jsonData<count($decodedJson);$jsonData++)
		{
			$data[$jsonData]= array(
				'invoiceId'=>$invoiceId[$jsonData],
				'invoiceLabel' => $invoiceLabel[$jsonData],
				'invoiceType' => $invoiceType[$jsonData],
				'startAt' => $startAt[$jsonData],
				'endAt'=> $endAt[$jsonData],
				'createdAt' => $getCreatedDate[$jsonData],
				'updatedAt' => $getUpdatedDate[$jsonData],
				
				'company' => array(
					'companyId' => $companyId[$jsonData],
					'companyName' => $companyName[$jsonData],
					'isDisplay' => $companyIsDisplay[$jsonData],
					'createdAt' => $companyCreatedAt[$jsonData],
					'updatedAt' => $companyUpdatedAt[$jsonData],
					'companyDisplayName' => $companyDispName[$jsonData],
					'address1' => $companyAddress1[$jsonData],
					'address2' => $companyAddress2[$jsonData],
					'pincode' => $companyPincode[$jsonData],
					'pan' => $companyPanNo[$jsonData],
					'tin' => $companyTinNo[$jsonData],
					'vatNo' => $companyVatNo[$jsonData],
					'serviceTaxNo' => $companyServiceTaxNo[$jsonData],
					'baicCurrencySymbol' => $companybasicCurrencySymbol[$jsonData],
					'formalName' => $companyFormalName[$jsonData],
					'noOfDecimalPoints' => $companyNoOfDecimalPoints[$jsonData],
					'currencySymbol' => $companyCurrencySymbol[$jsonData],
					'logo' => array(
						'documentName' => $companyDocumentName[$jsonData],
						'documentUrl' => $companyDocumentUrl[$jsonData],
						'documentSize' => $companyDocumentSize[$jsonData],
						'documentFormat' => $companyDocumentFormat[$jsonData]
					),
					'isDefault' => $companyIsDefault[$jsonData],
					'stateAbb' => $companyStateAbb[$jsonData],
					'cityId' => $companyCityId[$jsonData]
				)
			);
		}
		return json_encode($data);
	}
}