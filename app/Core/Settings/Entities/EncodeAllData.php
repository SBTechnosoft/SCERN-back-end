<?php
namespace ERP\Core\Settings\Entities;

use ERP\Core\Settings\Entities\Setting;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends Setting
{
	public function getEncodedAllData($status)
	{
		$convertedUpdatedDate =  array();
		$convertedCreatedDate =  array();
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		$decodedJson = json_decode($status,true);
		$setting = new EncodeAllData();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			
			// date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$setting->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $setting->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$setting->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $setting->getUpdated_at();
			}
			$settingData[$decodedData] = $decodedJson[$decodedData]['setting_data'];
			$decodedSettingData = json_decode($settingData[$decodedData]);
			if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['barcodeSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'barcodeWidth' => $decodedSettingData->barcode_width,
					'barcodeHeight' => $decodedSettingData->barcode_height,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['serviceDateSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'servicedateNoOfDays' => $decodedSettingData->servicedate_no_of_days,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['paymentDateSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'paymentdateNoOfDays' => $decodedSettingData->paymentdate_no_of_days,
					'paymentdateStatus' => $decodedSettingData->paymentdate_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['birthDateReminderSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'birthreminderType' => $decodedSettingData->birthreminder_type,
					'birthreminderTime' => $decodedSettingData->birthreminder_time,
					'birthreminderNotifyBy' => $decodedSettingData->birthreminder_notify_by,
					'birthreminderStatus' => $decodedSettingData->birthreminder_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['anniDateReminderSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'annireminderType' => $decodedSettingData->annireminder_type,
					'annireminderTime' => $decodedSettingData->annireminder_time,
					'annireminderNotifyBy' => $decodedSettingData->annireminder_notify_by,
					'annireminderStatus' => $decodedSettingData->annireminder_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['chequeNoSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'chequeno' => $decodedSettingData->chequeno_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['productSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'productBestBeforeStatus' => $decodedSettingData->product_best_before_status,
					'productSizeStatus' => $decodedSettingData->product_size_status,
					'productColorStatus' => $decodedSettingData->product_color_status,
					'productFrameNoStatus' => $decodedSettingData->product_frame_no_status,
					'productMrpRequireStatus' => $decodedSettingData->product_mrp_require_status,
					'productMarginStatus' => $decodedSettingData->product_margin_status,
					'productVariantStatus' => $decodedSettingData->product_variant_status,
					'productAdvanceMouStatus' => isset($decodedSettingData->product_advance_mou_status) ? $decodedSettingData->product_advance_mou_status : 'disable',
					'productMeasurementType' => $decodedSettingData->product_measurement_type,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['inventorySetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'inventoryItemizeStatus' => $decodedSettingData->inventory_itemize_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['languageSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'languageSettingType' => $decodedSettingData->language_setting_type,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['workFlowSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'workflowQuotationStatus' => $decodedSettingData->workflow_quotation_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['clientSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'clientAddressStatus' => $decodedSettingData->client_address_status,
					'clientEmailIdStatus' => $decodedSettingData->client_email_id_status,
					'clientWorkNoStatus' => $decodedSettingData->client_work_no_status,
					'clientStateStatus' => $decodedSettingData->client_state_status,
					'clientCityStatus' => $decodedSettingData->client_city_status,
					'clientProfessionStatus' => $decodedSettingData->client_profession_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['billSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'billSalesmanStatus' => $decodedSettingData->bill_salesman_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['advanceBillSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'advanceSalesStatus' => $decodedSettingData->advance_sales_status,
					'advancePurchaseStatus' => $decodedSettingData->advance_purchase_status,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
			else if(strcmp($decodedJson[$decodedData]['setting_type'],$constantArray['webIntegrationSetting'])==0)
			{
				$data[$decodedData]= array(
					'settingId' => $decodedJson[$decodedData]['setting_id'],
					'settingType' => $decodedJson[$decodedData]['setting_type'],
					'webintegrationStatus' => $decodedSettingData->webintegration_status,
					'webintegrationUserId' => $decodedSettingData->webintegration_user_id,
					'webintegrationPassword' => $decodedSettingData->webintegration_password,
					'webintegrationPushUrl' => $decodedSettingData->webintegration_push_url,
					'createdAt' => $getCreatedDate[$decodedData],
					'updatedAt' => $getUpdatedDate[$decodedData]
				);
			}
		}
		return json_encode($data);
	}
}