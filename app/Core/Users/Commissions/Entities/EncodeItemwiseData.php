<?php
namespace ERP\Core\Users\Commissions\Entities;

use ERP\Core\Users\Commissions\Entities\Commission;
use ERP\Core\Products\Services\ProductService;
use Carbon;
/**
 *
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class EncodeItemwiseData extends ProductService
{	
	public function getEncodedData($status)
	{
		$decodedJson = json_decode($status,true);
		$createdAt= $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$productCommissionId= $decodedJson[0]['product_commission_id'];
		$productId = $decodedJson[0]['product_id'];
		$productName = $decodedJson[0]['product_name'];
		$mrp = $decodedJson[0]['mrp'];
		$companyId = $decodedJson[0]['company_id'];
		$commissionRate= $decodedJson[0]['commission_rate'];
		$commissionRateType= ucfirst($decodedJson[0]['commission_rate_type']);
		$commissionCalcOn= $decodedJson[0]['commission_calc_on'];
		$commissionFromQty= $decodedJson[0]['commission_from_qty'];
		$commissionToQty= $decodedJson[0]['commission_to_qty'];
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
		$data['productCommissionId'] = $productCommissionId;
		$data['productId'] = $productId;
		$data['productName'] = $productName;
		$data['commissionFromQty'] = $commissionFromQty;
		$data['commissionToQty'] = $commissionToQty;
		$data['mrp'] = $mrp;
		$data['companyId'] = $companyId;
		$data['commissionRate'] = $commissionRate;
		$data['commissionRateType'] = $commissionRateType;
		$data['commissionCalcOn'] = $commissionCalcOn;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;
		$encodeData = json_encode($data);
		return $encodeData;
	}
	public function getAllEncodedData($status)
	{
		$data = array();
		$decodedJson = json_decode($status,true);
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt= $decodedJson[$decodedData]['created_at'];
			$updatedAt= $decodedJson[$decodedData]['updated_at'];
			$productCommissionId= $decodedJson[$decodedData]['product_commission_id'];
			$productId = $decodedJson[$decodedData]['product_id'];
			$productName = $decodedJson[$decodedData]['product_name'];
			$mrp = $decodedJson[$decodedData]['mrp'];
			$companyId = $decodedJson[$decodedData]['company_id'];
			$commissionRate= $decodedJson[$decodedData]['commission_rate'];
			$commissionRateType= ucfirst($decodedJson[$decodedData]['commission_rate_type']);
			$commissionCalcOn= $decodedJson[$decodedData]['commission_calc_on'];
			$commissionFromQty= $decodedJson[$decodedData]['commission_from_qty'];
			$commissionToQty= $decodedJson[$decodedData]['commission_to_qty'];
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
			$data[$decodedData]['productCommissionId'] = $productCommissionId;
			$data[$decodedData]['productId'] = $productId;
			$data[$decodedData]['productName'] = $productName;
			$data[$decodedData]['commissionFromQty'] = $commissionFromQty;
			$data[$decodedData]['commissionToQty'] = $commissionToQty;
			$data[$decodedData]['mrp'] = $mrp;
			$data[$decodedData]['companyId'] = $companyId;
			$data[$decodedData]['commissionRate'] = $commissionRate;
			$data[$decodedData]['commissionRateType'] = $commissionRateType;
			$data[$decodedData]['commissionCalcOn'] = $commissionCalcOn;
			$data[$decodedData]['createdAt'] = $getCreatedDate;
			$data[$decodedData]['updatedAt'] = $getUpdatedDate;
		}
		$encodeData = json_encode($data);
		return $encodeData;
	}
}