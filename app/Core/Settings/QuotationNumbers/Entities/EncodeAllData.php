<?php
namespace ERP\Core\Settings\QuotationNumbers\Entities;

use ERP\Core\Settings\QuotationNumbers\Entities\QuotationNumber;
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
		$quotation = new QuotationNumber();
		$encodeDataClass = new EncodeAllData();
		$companyArray = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$quotationId[$decodedData] = $decodedJson[$decodedData]['quotation_id'];
			$quotationLabel[$decodedData] = $decodedJson[$decodedData]['quotation_label'];
			$quotationType[$decodedData] = $decodedJson[$decodedData]['quotation_type'];
			$startAt[$decodedData] = $decodedJson[$decodedData]['start_at'];
			$endAt[$decodedData] = $decodedJson[$decodedData]['end_at'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			
			//get the company detail from database
			if (!isset($companyArray[$companyId[$decodedData]])) {
				$companyStatus[$decodedData] = $encodeDataClass->getCompanyData($companyId[$decodedData]);
				$companyArray[$companyId[$decodedData]] = json_decode($companyStatus[$decodedData],true);
			}
			
			$companyDecodedJson[$decodedData] = $companyArray[$companyId[$decodedData]];

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
			$quotation->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $quotation->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$quotation->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedate[$decodedData] = $quotation->getUpdated_at();
			}
			$data[$decodedData]= array(
				'quotationId'=>$quotationId[$decodedData],
				'quotationLabel' => $quotationLabel[$decodedData],
				'quotationType' => $quotationType[$decodedData],
				'startAt' => $startAt[$decodedData],
				'endAt'=> $endAt[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedate[$decodedData],
				
				'company' => array(
					'companyId' => $companyId[$decodedData],
					'companyName' => $companyName[$decodedData],
					'isDisplay' => $companyIsDisplay[$decodedData],
					'createdAt' => $companyCreatedAt[$decodedData],
					'updatedAt' => $companyUpdatedAt[$decodedData],
					'companyDisplayName' => $companyDispName[$decodedData],
					'address1' => $companyAddress1[$decodedData],
					'address2' => $companyAddress2[$decodedData],
					'pincode' => $companyPincode[$decodedData],
					'pan' => $companyPanNo[$decodedData],
					'tin' => $companyTinNo[$decodedData],
					'vatNo' => $companyVatNo[$decodedData],
					'serviceTaxNo' => $companyServiceTaxNo[$decodedData],
					'baicCurrencySymbol' => $companybasicCurrencySymbol[$decodedData],
					'formalName' => $companyFormalName[$decodedData],
					'noOfDecimalPoints' => $companyNoOfDecimalPoints[$decodedData],
					'currencySymbol' => $companyCurrencySymbol[$decodedData],
					'logo' => array(
						'documentName' => $companyDocumentName[$decodedData],
						'documentUrl' => $companyDocumentUrl[$decodedData],
						'documentSize' => $companyDocumentSize[$decodedData],
						'documentFormat' => $companyDocumentFormat[$decodedData]
					),
					'isDefault' => $companyIsDefault[$decodedData],
					'stateAbb' => $companyStateAbb[$decodedData],
					'cityId' => $companyCityId[$decodedData]
				)
			);
		}
		return json_encode($data);
	}
}