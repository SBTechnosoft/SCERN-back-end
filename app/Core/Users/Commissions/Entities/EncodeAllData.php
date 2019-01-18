<?php
namespace ERP\Core\Users\Commissions\Entities;

use ERP\Core\Users\Commissions\Entities\Commission;
use Carbon;
/**
 *
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class EncodeAllData extends CompanyService
{
	public function getEncodedAllData($status)
	{
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$commission = new Commission();
		$data = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$commissionId[$decodedData] = $decodedJson[$decodedData]['commission_id'];
			$userId[$decodedData] = $decodedJson[$decodedData]['user_id'];
			$commissionStatus[$decodedData] = $decodedJson[$decodedData]['commission_status'];
			$commissionRate[$decodedData] = $decodedJson[$decodedData]['commission_rate'];
			$commissionRateType[$decodedData] = $decodedJson[$decodedData]['commission_rate_type'];
			$commissionType[$decodedData] = $decodedJson[$decodedData]['commission_type'];
			$commissionCalcOn[$decodedData] = $decodedJson[$decodedData]['commission_calc_on'];
			$commissionFor[$decodedData] = $decodedJson[$decodedData]['commission_for'];

			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$commission->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $commission->getCreated_at();
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');
				$commission->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $commission->getUpdated_at();
			}
			//set all data into json array
			$data[$decodedData]['commissionId'] = $commissionId[$decodedData];
			$data[$decodedData]['userId'] = $userId[$decodedData];
			$data[$decodedData]['commissionStatus'] = $commissionStatus[$decodedData];
			$data[$decodedData]['commissionRate'] = $commissionRate[$decodedData];
			$data[$decodedData]['commissionRateType'] = $commissionRateType[$decodedData];
			$data[$decodedData]['commissionType'] = $commissionType[$decodedData];
			$data[$decodedData]['commissionCalcOn'] = $commissionCalcOn[$decodedData];
			$data[$decodedData]['commissionFor'] = $commissionFor[$decodedData];
			$data[$decodedData]['createdAt'] = $getCreatedDate[$decodedData];
			$data[$decodedData]['updatedAt'] = $getUpdatedDate[$decodedData];
		}
		$encodeData = json_encode($data);
		return $encodeData;
	}
}