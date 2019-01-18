<?php
namespace ERP\Core\Settings\Templates\Entities;

use ERP\Core\Settings\Templates\Entities\Template;
use Carbon;
use ERP\Core\Companies\Services\CompanyService;
use ERP\Core\Settings\Services\SettingService;
use ERP\Entities\Constants\ConstantClass;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeData extends CompanyService
{
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt= $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$templateId= $decodedJson[0]['template_id'];
		$templateName= $decodedJson[0]['template_name'];
		$templateType= $decodedJson[0]['template_type'];
		$templateBody= $decodedJson[0]['template_body'];
		$companyId= $decodedJson[0]['company_id'];
		
		//get the company details from database
		$encodeCompanyDataClass = new EncodeData();
		$companyStatus = $encodeCompanyDataClass->getCompanyData($companyId);
		$companyDecodedJson = json_decode($companyStatus,true);
		
		//date format conversion
		$template = new Template();
		
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$template->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $template->getCreated_at();
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$template->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $template->getUpdated_at();
		}

		// $constantClass = new ConstantClass();
		// $templateTypeArray = $constantClass->templateConstants();

		// if ($templateType == $templateTypeArray['Invoice'])
		// {
		// 	$SettingService = new SettingService();
		// 	$htmlTh = $SettingService->settingTemplateColumns();

		// 	$templateBody = str_replace('[advanceMeasureOrColor]', $htmlTh, $templateBody);
		// }
		

		//set all data into json array
		$data = array();
		$data['templateId'] = $templateId;
		$data['templateName'] = $templateName;
		$data['templateBody'] = $templateBody;
		$data['templateType'] = $templateType;
		$data['createdAt'] = $getCreatedDate;	
		$data['updatedAt'] = $getUpdatedDate;	
		$data['company']= array(
			'companyId' => $companyDecodedJson['companyId'],	
			'companyName' => $companyDecodedJson['companyName'],
			'companyDisplayName' => $companyDecodedJson['companyDisplayName'],	
			'address1' => $companyDecodedJson['address1'],	
			'address2' => $companyDecodedJson['address2'],
			'pincode'=> $companyDecodedJson['pincode'],	
			'pan' => $companyDecodedJson['pan'],	
			'tin' => $companyDecodedJson['tin'],	
			'vatNo' => $companyDecodedJson['vatNo'],
			'serviceTaxNo' => $companyDecodedJson['serviceTaxNo'],	
			'basicCurrencySymbol'=> $companyDecodedJson['basicCurrencySymbol'],
			'formalName'=> $companyDecodedJson['formalName'],
			'noOfDecimalPoints' => $companyDecodedJson['noOfDecimalPoints'],	
			'currencySymbol' => $companyDecodedJson['currencySymbol'],
			'logo'=> array(
				'documentName'=> $companyDecodedJson['logo']['documentName'],
				'documentUrl' => $companyDecodedJson['logo']['documentUrl'],
				'documentSize' => $companyDecodedJson['logo']['documentSize'],	
				'documentFormat' => $companyDecodedJson['logo']['documentFormat']
			),
			'isDisplay' => $companyDecodedJson['isDisplay'],	
			'isDefault' => $companyDecodedJson['isDefault'],	
			'createdAt' => $companyDecodedJson['createdAt'],	
			'updatedAt' => $companyDecodedJson['updatedAt'],	
			'stateAbb' => $companyDecodedJson['state']['stateAbb'],
			'cityId' => $companyDecodedJson['city']['cityId'],
		);
		$encodeData = json_encode($data);
		return $encodeData;
	}
}