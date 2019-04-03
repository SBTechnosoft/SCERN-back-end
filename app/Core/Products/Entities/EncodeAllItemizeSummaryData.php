<?php
namespace ERP\Core\Products\Entities;

use ERP\Core\Products\Services\ProductService;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllItemizeSummaryData extends ProductService
{
	public function getEncodedItemizeSummaryData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$productDecodedJson = array();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$data = array();
		$productArray = array();
		$encodeDataClass = new EncodeAllItemizeSummaryData();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];

			$productId[$decodedData] = $decodedJson[$decodedData]['product_id'];
			$imeiNo[$decodedData] = $decodedJson[$decodedData]['imei_no'];
			$barcodeNo[$decodedData] = $decodedJson[$decodedData]['barcode_no'];
			$qty[$decodedData] = $decodedJson[$decodedData]['qty'];
			$stock[$decodedData] = $decodedJson[$decodedData]['stock'];
			$purchaseBillNo[$decodedData] = $decodedJson[$decodedData]['purchase_bill_no'];
			$salesBillNo[$decodedData] = $decodedJson[$decodedData]['sales_bill_no'];
			$jfId[$decodedData] = $decodedJson[$decodedData]['jf_id'];
			
			
			//get the product detail from database
			
			if (!isset($productArray[$productId[$decodedData]])) {
				$productArray[$productId[$decodedData]] = $encodeDataClass->getProductData($productId[$decodedData]);
			}
			$productStatus[$decodedData] = $productArray[$productId[$decodedData]];
			$productDecodedJson[$decodedData] = json_decode($productStatus[$decodedData],true);
			if(strcmp($productStatus[$decodedData],$exceptionArray['404'])==0)
			{
				//remove deleted product from an array(splice and break)
				array_splice($decodedJson,$decodedData,1);
				$decodedData = $decodedData-1;
				continue;
			}
			//date format conversion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt[$decodedData])->format('d-m-Y');
			$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt[$decodedData])->format('d-m-Y');

			//Encode
			$data[$decodedData] = [
				'productId'=>$productId[$decodedData],
				'imeiNo'=>$imeiNo[$decodedData],
				'barcodeNo'=>$barcodeNo[$decodedData],
				'qty'=>$qty[$decodedData],
				'stock'=>$stock[$decodedData],
				'purchaseBillNo'=>$purchaseBillNo[$decodedData],
				'salesBillNo'=>$salesBillNo[$decodedData],
				'jfId'=>$jfId[$decodedData],
				'createdAt' => $convertedCreatedDate[$decodedData],
				'updatedAt' => $convertedUpdatedDate[$decodedData]
			];
		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}