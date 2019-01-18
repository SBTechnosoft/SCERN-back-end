<?php
namespace ERP\Core\Users\Commissions\Entities;

use ERP\Core\Users\Commissions\Entities\Commission;
use ERP\Core\Companies\Services\CompanyService;
use Carbon;
/**
 *
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class EncodeData extends CompanyService
{	
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt= $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$commissionId= $decodedJson[0]['commission_id'];
		$userId= $decodedJson[0]['user_id'];
		$commissionStatus= $decodedJson[0]['commission_status'];
		$commissionRate= $decodedJson[0]['commission_rate'];
		$commissionRateType= $decodedJson[0]['commission_rate_type'];
		$commissionType= $decodedJson[0]['commission_type'];
		$commissionCalcOn= $decodedJson[0]['commission_calc_on'];
		$commissionFor= $decodedJson[0]['commission_for'];
		//date format conversion
		$commission = new Commission();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$commission->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $commission->getCreated_at();
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$commission->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $commission->getUpdated_at();
		}
		//set all data into json array
		$data = array();
		$data['commissionId'] = $commissionId;
		$data['userId'] = $userId;
		$data['commissionStatus'] = $commissionStatus;
		$data['commissionRate'] = $commissionRate;
		$data['commissionRateType'] = $commissionRateType;
		$data['commissionType'] = $commissionType;
		$data['commissionCalcOn'] = $commissionCalcOn;
		$data['commissionFor'] = $commissionFor;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;
		$encodeData = json_encode($data);
		return $encodeData;
	}
}