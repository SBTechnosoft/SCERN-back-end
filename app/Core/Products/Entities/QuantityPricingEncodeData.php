<?php
namespace ERP\Core\Products\Entities;

// use ERP\Core\Products\Entities\Product;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 *
 * @author Farhan Shaikh<farhan.s@siliconbrain.in>
 */
class QuantityPricingEncodeData 
{
    public function getEncodedData($status)
	{
		$constantArray = new ConstantClass();
		$constantArrayData = $constantArray->constantVariable();

		// $documentPath = $constantArrayData['productBarcode'];
		$decodedJson = json_decode($status,true);
		$dataCount = count($decodedJson);

		for($decodedData=0;$decodedData<$dataCount;$decodedData++)
		{
			$decodedJson[$decodedData]['salesPrice'] = number_format($decodedJson[$decodedData]['salesPrice'],2,'.','');

			$decodedJson[$decodedData]['createdAt'] = 
			$decodedJson[$decodedData]['createdAt'] == "0000-00-00 00:00:00" ? "0000-00-00" : Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$decodedJson[$decodedData]['createdAt'])->format('d-m-Y');

			$decodedJson[$decodedData]['updatedAt'] = 
			$decodedJson[$decodedData]['updatedAt'] == "0000-00-00 00:00:00" ? "0000-00-00" : Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$decodedJson[$decodedData]['updatedAt'])->format('d-m-Y');
		}

		return json_encode($decodedJson);
	}
}