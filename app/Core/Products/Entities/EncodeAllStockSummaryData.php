<?php
namespace ERP\Core\Products\Entities;

use ERP\Core\Accounting\Ledgers\Entities\Ledger;
use ERP\Core\Products\Services\ProductService;
// use ERP\Core\Entities\CompanyDetail;
// use ERP\Core\Entities\BranchDetail;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllStockSummaryData extends ProductService
{
	public function getEncodedStockSummaryData($status)
	{
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$productDecodedJson = array();
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		
		$encodeAllData =  array();
		$decodedJson = json_decode($status,true);
		$ledger = new Ledger();
		$data = array();
		$encodeDataClass = new EncodeAllStockSummaryData();
		$productArray = array();
		for($decodedData=0;$decodedData<count($decodedJson);$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			
			$productTrnSummaryId[$decodedData] = $decodedJson[$decodedData]['product_trn_summary_id'];
			$qty[$decodedData] = $decodedJson[$decodedData]['qty'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$branchId[$decodedData] = $decodedJson[$decodedData]['branch_id'];
			$productId[$decodedData] = $decodedJson[$decodedData]['product_id'];
			
			//get the product detail from database
			if (!isset($productArray[$productId[$decodedData]])) {
				$productStatus[$decodedData] = $encodeDataClass->getProductData($productId[$decodedData]);
				$productArray[$productId[$decodedData]] = json_decode($productStatus[$decodedData],true);
			}
			
			$productDecodedJson[$decodedData] = $productArray[$productId[$decodedData]];
			if(strcmp($productStatus[$decodedData],$exceptionArray['404'])==0)
			{
				//remove deleted product from an array(splice and break)
				array_splice($decodedJson,$decodedData,1);
				$decodedData = $decodedData-1;
				continue;
			}
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

			//Encode
			$data[$decodedData]= array(
				'productTrnSummaryId'=>$productTrnSummaryId[$decodedData],
				'qty' => $qty[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData],
				'product' => $productDecodedJson[$decodedData],
				'companyId' => $companyId[$decodedData],
				'branchId' => $branchId[$decodedData]
			);

		}
		$jsonEncodedData = json_encode($data);
		return $jsonEncodedData;
	}
}