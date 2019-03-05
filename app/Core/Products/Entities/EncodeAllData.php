<?php
namespace ERP\Core\Products\Entities;

use ERP\Core\Products\Entities\Product;
// use ERP\Core\Settings\MeasurementUnits\Services\MeasurementService;
use ERP\Core\ProductCategories\Services\ProductCategoryService;
// use ERP\Core\Entities\ProductGroupDetail;
// use ERP\Core\Entities\CompanyDetail;
// use ERP\Core\Entities\BranchDetail;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class EncodeAllData extends ProductCategoryService
{
	public function getEncodedAllData($status)
	{
		$constantArray = new ConstantClass();
		$constantArrayData = $constantArray->constantVariable();
		$convertedCreatedDate =  array();
		$convertedUpdatedDate =  array();
		$encodeAllData =  array();
		$getCompanyDetails = array();
			
		$decodedJson = json_decode($status,true);

		$product = new Product();
		$dataCount = count($decodedJson);
		// $documentDataArray = array();

		$documentPath = $constantArrayData['productBarcode'];
		$data = array();

		for($decodedData=0;$decodedData<$dataCount;$decodedData++)
		{
			$createdAt[$decodedData] = $decodedJson[$decodedData]['created_at'];
			$updatedAt[$decodedData] = $decodedJson[$decodedData]['updated_at'];
			$productId[$decodedData] = $decodedJson[$decodedData]['product_id'];
			$productName[$decodedData] = $decodedJson[$decodedData]['product_name'];
			$altProductName[$decodedData] = $decodedJson[$decodedData]['alt_product_name'];
			$highestMeasurementUnitId[$decodedData] = $decodedJson[$decodedData]['highest_measurement_unit_id'];
			$higherMeasurementUnitId[$decodedData] = $decodedJson[$decodedData]['higher_measurement_unit_id'];

			$mediumMeasurementUnitId[$decodedData] = $decodedJson[$decodedData]['medium_measurement_unit_id'];
			$mediumLowerMeasurementUnitId[$decodedData] = $decodedJson[$decodedData]['medium_lower_measurement_unit_id'];
			$lowerMeasurementUnitId[$decodedData] = $decodedJson[$decodedData]['lower_measurement_unit_id'];

			$measurementUnit[$decodedData] = $decodedJson[$decodedData]['measurement_unit'];
			$primaryMeasureUnit[$decodedData] = $decodedJson[$decodedData]['primary_measure_unit'];
			$quantityWisePricing[$decodedData] = isset($decodedJson[$decodedData]['quantityWisePricing']) ? $decodedJson[$decodedData]['quantityWisePricing'] : array();
			$isDisplay[$decodedData] = $decodedJson[$decodedData]['is_display'];
			$highestPurchasePrice[$decodedData] = $decodedJson[$decodedData]['highest_purchase_price'];
			$higherPurchasePrice[$decodedData] = $decodedJson[$decodedData]['higher_purchase_price'];

			$mediumPurchasePrice[$decodedData]= $decodedJson[$decodedData]['medium_purchase_price'];
			$mediumLowerPurchasePrice[$decodedData]= $decodedJson[$decodedData]['medium_lower_purchase_price'];
			$lowerPurchasePrice[$decodedData]= $decodedJson[$decodedData]['lower_purchase_price'];


			$highestUnitQty[$decodedData] = $decodedJson[$decodedData]['highest_unit_qty'] ? $decodedJson[$decodedData]['highest_unit_qty'] : 0;
			$higherUnitQty[$decodedData] = $decodedJson[$decodedData]['higher_unit_qty'] ? $decodedJson[$decodedData]['higher_unit_qty'] : 0;

			$mediumUnitQty[$decodedData] = $decodedJson[$decodedData]['medium_unit_qty'] ? $decodedJson[$decodedData]['medium_unit_qty'] : 0;
			$mediumLowerUnitQty[$decodedData] = $decodedJson[$decodedData]['medium_lower_unit_qty'] ? $decodedJson[$decodedData]['medium_lower_unit_qty'] : 0;
			$lowerUnitQty[$decodedData] = $decodedJson[$decodedData]['lower_unit_qty'] ? $decodedJson[$decodedData]['lower_unit_qty'] : 0;
			$lowestUnitQty[$decodedData] = $decodedJson[$decodedData]['lowest_unit_qty'] ? $decodedJson[$decodedData]['lowest_unit_qty'] : 0;


			$highestMouConv[$decodedData] = $decodedJson[$decodedData]['highest_mou_conv'] ? $decodedJson[$decodedData]['highest_mou_conv'] : 1;
			$higherMouConv[$decodedData] = $decodedJson[$decodedData]['higher_mou_conv'] ? $decodedJson[$decodedData]['higher_mou_conv'] : 1;
			$mediumMouConv[$decodedData] = $decodedJson[$decodedData]['medium_mou_conv'] ? $decodedJson[$decodedData]['medium_mou_conv'] : 1;
			$mediumLowerMouConv[$decodedData] = $decodedJson[$decodedData]['medium_lower_mou_conv'] ? $decodedJson[$decodedData]['medium_lower_mou_conv'] : 1;
			$lowerMouConv[$decodedData] = $decodedJson[$decodedData]['lower_mou_conv'] ? $decodedJson[$decodedData]['lower_mou_conv'] : 1;
			$lowestMouConv[$decodedData] = $decodedJson[$decodedData]['lowest_mou_conv'] ? $decodedJson[$decodedData]['lowest_mou_conv'] : 1;


			$purchasePrice[$decodedData] = $decodedJson[$decodedData]['purchase_price'];
			$wholesaleMargin[$decodedData] = $decodedJson[$decodedData]['wholesale_margin'];
			$wholesaleMarginFlat[$decodedData] = $decodedJson[$decodedData]['wholesale_margin_flat'];
			$semiWholesaleMargin[$decodedData] = $decodedJson[$decodedData]['semi_wholesale_margin'];
			$vat[$decodedData] = $decodedJson[$decodedData]['vat'];
			$purchaseCgst[$decodedData] = $decodedJson[$decodedData]['purchase_cgst'];
			$purchaseSgst[$decodedData] = $decodedJson[$decodedData]['purchase_sgst'];
			$purchaseIgst[$decodedData] = $decodedJson[$decodedData]['purchase_igst'];
			$margin[$decodedData] = $decodedJson[$decodedData]['margin'];
			$marginFlat[$decodedData] = $decodedJson[$decodedData]['margin_flat'];
			$mrp[$decodedData] = $decodedJson[$decodedData]['mrp'];
			$igst[$decodedData] = $decodedJson[$decodedData]['igst'];
			$hsn[$decodedData] = $decodedJson[$decodedData]['hsn'];
			$color[$decodedData] = $decodedJson[$decodedData]['color'];
			$size[$decodedData] = $decodedJson[$decodedData]['size'];
			$variant[$decodedData] = $decodedJson[$decodedData]['variant'];
			$productDescription[$decodedData] = $decodedJson[$decodedData]['product_description'];
			$additionalTax[$decodedData] = $decodedJson[$decodedData]['additional_tax'];
			$minimumStockLevel[$decodedData] = $decodedJson[$decodedData]['minimum_stock_level'];

			$productCode[$decodedData] = $decodedJson[$decodedData]['product_code'];
			$productMenu[$decodedData] = $decodedJson[$decodedData]['product_menu'];
			$productType[$decodedData] = $decodedJson[$decodedData]['product_type'];
			$notForSale[$decodedData] = $decodedJson[$decodedData]['not_for_sale'];
			$maxSaleQty[$decodedData] = $decodedJson[$decodedData]['max_sale_qty'];
			$bestBeforeTime[$decodedData] = $decodedJson[$decodedData]['best_before_time'];
			$bestBeforeType[$decodedData] = $decodedJson[$decodedData]['best_before_type'];
			$cessFlat[$decodedData] = $decodedJson[$decodedData]['cess_flat'];
			$cessPercentage[$decodedData] = $decodedJson[$decodedData]['cess_percentage'];
			$taxInclusive[$decodedData] = $decodedJson[$decodedData]['tax_inclusive'];
			$webIntegration[$decodedData] = $decodedJson[$decodedData]['web_integration'];
			$opening[$decodedData] = $decodedJson[$decodedData]['opening'];
			$remark[$decodedData] = $decodedJson[$decodedData]['remark'];
			$productCoverId[$decodedData] = $decodedJson[$decodedData]['product_cover_id'];

			$documentName[$decodedData] = $decodedJson[$decodedData]['document_name'];
			$documentFormat[$decodedData] = $decodedJson[$decodedData]['document_format'];
			$productCatId[$decodedData] = $decodedJson[$decodedData]['product_category_id'];
			$productGrpId[$decodedData] = $decodedJson[$decodedData]['product_group_id'];
			$companyId[$decodedData] = $decodedJson[$decodedData]['company_id'];
			$branchId[$decodedData] = $decodedJson[$decodedData]['branch_id'];
	
			//convert amount(number_format) into their company's selected decimal points
			$highestPurchasePrice[$decodedData] = number_format($highestPurchasePrice[$decodedData],2,'.','');
			$higherPurchasePrice[$decodedData] = number_format($higherPurchasePrice[$decodedData],2,'.','');

			$mediumPurchasePrice[$decodedData] = number_format($mediumPurchasePrice[$decodedData],2,'.','');
			$mediumLowerPurchasePrice[$decodedData] = number_format($mediumLowerPurchasePrice[$decodedData],2,'.','');
			$lowerPurchasePrice[$decodedData] = number_format($lowerPurchasePrice[$decodedData],2,'.','');

			$purchasePrice[$decodedData] = number_format($purchasePrice[$decodedData],2,'.','');
			$wholesaleMargin[$decodedData] = number_format($wholesaleMargin[$decodedData],2,'.','');
			$semiWholesaleMargin[$decodedData] = number_format($semiWholesaleMargin[$decodedData],2,'.','');
			$vat[$decodedData] = number_format($vat[$decodedData],2,'.','');
			$purchaseCgst[$decodedData] = number_format($purchaseCgst[$decodedData],2,'.','');
			$purchaseSgst[$decodedData] = number_format($purchaseSgst[$decodedData],2,'.','');
			$purchaseIgst[$decodedData] = number_format($purchaseIgst[$decodedData],2,'.','');
			$margin[$decodedData] = number_format($margin[$decodedData],2,'.','');
			$mrp[$decodedData] = number_format($mrp[$decodedData],2,'.','');
			$additionalTax[$decodedData] = number_format($additionalTax[$decodedData],2,'.','');
			$igst[$decodedData] = number_format($igst[$decodedData],2,'.','');
			$marginFlat[$decodedData] = number_format($marginFlat[$decodedData],2,'.','');
			$wholesaleMarginFlat[$decodedData] = number_format($wholesaleMarginFlat[$decodedData],2,'.','');
			
			$highestUnitQty[$decodedData] = number_format($highestUnitQty[$decodedData],2,'.','');
			$higherUnitQty[$decodedData] = number_format($higherUnitQty[$decodedData],2,'.','');

			$mediumUnitQty[$decodedData] = number_format($mediumUnitQty[$decodedData],2,'.','');
			$mediumLowerUnitQty[$decodedData] = number_format($mediumLowerUnitQty[$decodedData],2,'.','');
			$lowerUnitQty[$decodedData] = number_format($lowerUnitQty[$decodedData],2,'.','');

			$lowestUnitQty[$decodedData] = number_format($lowestUnitQty[$decodedData],2,'.','');


			$highestMouConv[$decodedData] = number_format($highestMouConv[$decodedData],2,'.','');
			$higherMouConv[$decodedData] = number_format($higherMouConv[$decodedData],2,'.','');
			$mediumMouConv[$decodedData] = number_format($mediumMouConv[$decodedData],2,'.','');
			$mediumLowerMouConv[$decodedData] = number_format($mediumLowerMouConv[$decodedData],2,'.','');
			$lowerMouConv[$decodedData] = number_format($lowerMouConv[$decodedData],2,'.','');
			$lowestMouConv[$decodedData] = number_format($lowestMouConv[$decodedData],2,'.','');


			//get the product_cat_details from database
			// $encodeMeasurementService = new MeasurementService();
			// $highestMeasurementUnitStatus[$decodedData] = $encodeMeasurementService->getMeasurementData($highestMeasurementUnitId[$decodedData]);
			// $highestMeasurDecodedJson[$decodedData] = json_decode($highestMeasurementUnitStatus[$decodedData],true);
			$highestMeasurDecodedJson[$decodedData] = $highestMeasurementUnitId[$decodedData];
			// if(is_array($highestMeasurDecodedJson[$decodedData]))
			// {
			// 	$highestMeasurDecodedJson[$decodedData]['measurementUnit'] = 'highest';
			// 	$highestMeasurDecodedJson[$decodedData]['purchasePrice'] = $highestPurchasePrice[$decodedData];
			// }

			// $higherMeasurementUnitStatus[$decodedData] = $encodeMeasurementService->getMeasurementData($higherMeasurementUnitId[$decodedData]);
			// $higherMeasurDecodedJson[$decodedData] = json_decode($higherMeasurementUnitStatus[$decodedData],true);
			$higherMeasurDecodedJson[$decodedData] = $higherMeasurementUnitId[$decodedData];
			// if(is_array($higherMeasurDecodedJson[$decodedData]))
			// {
			// 	$higherMeasurDecodedJson[$decodedData]['measurementUnit'] = 'higher';
			// 	$higherMeasurDecodedJson[$decodedData]['purchasePrice'] = $higherPurchasePrice[$decodedData];
			// }
			$mediumMeasurDecodedJson[$decodedData] = $mediumMeasurementUnitId[$decodedData];
			$mediumLowerMeasurDecodedJson[$decodedData] = $mediumLowerMeasurementUnitId[$decodedData];
			$lowerMeasurDecodedJson[$decodedData] = $lowerMeasurementUnitId[$decodedData];
			// $measurementUnitStatus[$decodedData] = $encodeMeasurementService->getMeasurementData($measurementUnit[$decodedData]);
			// $measurementUnitDecodedJson[$decodedData] = json_decode($measurementUnitStatus[$decodedData],true);
			$measurementUnitDecodedJson[$decodedData] = $measurementUnit[$decodedData];
			// if(is_array($measurementUnitDecodedJson[$decodedData]))
			// {
			// 	$measurementUnitDecodedJson[$decodedData]['measurementUnit'] = 'lowest';
			// 	$measurementUnitDecodedJson[$decodedData]['purchasePrice'] = $purchasePrice[$decodedData];
			// }

			//product date convertion
			$convertedCreatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$createdAt[$decodedData])->format('d-m-Y');
			$convertedCreatedTime[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$createdAt[$decodedData])->format('h:i A');
			$product->setCreated_at($convertedCreatedDate[$decodedData]);
			$getCreatedDate[$decodedData] = $product->getCreated_at();
			$getCreatedTime[$decodedData] = $convertedCreatedTime[$decodedData];
			
			if(strcmp($updatedAt[$decodedData],'0000-00-00 00:00:00')==0)
			{
				$getUpdatedDate[$decodedData] = "00-00-0000";
				$getUpdatedTime[$decodedData] = "00:00";
			}
			else
			{
				$convertedUpdatedDate[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$updatedAt[$decodedData])->format('d-m-Y');
				$convertedUpdatedTime[$decodedData] = Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$updatedAt[$decodedData])->format('h:i A');
				$product->setUpdated_at($convertedUpdatedDate[$decodedData]);
				$getUpdatedDate[$decodedData] = $product->getUpdated_at();
				$getUpdatedTime[$decodedData] = $convertedUpdatedTime[$decodedData];
			}
			//Quantity of Product
			$productQty[$decodedData] = 0;
			if (isset($decodedJson[$decodedData]['quantity']))
			{
				$productQty[$decodedData] = $decodedJson[$decodedData]['quantity'];
			}
			
			//last Insert/Updated by user
			$createdBy[$decodedData] = isset($decodedJson[$decodedData]['created_by']) ? $decodedJson[$decodedData]['created_by'] : 0;
			$updatedBy[$decodedData] = isset($decodedJson[$decodedData]['updated_by']) ? $decodedJson[$decodedData]['updated_by'] : 0;

			$data[$decodedData]= array(
				'productId'=>$productId[$decodedData],
				'productName' => $productName[$decodedData],
				'altProductName' => $altProductName[$decodedData],
				'isDisplay' => $isDisplay[$decodedData],
				'highestPurchasePrice' => $highestPurchasePrice[$decodedData],
				'higherPurchasePrice' => $higherPurchasePrice[$decodedData],
				'purchasePrice' => $purchasePrice[$decodedData],
				'wholesaleMargin' => $wholesaleMargin[$decodedData],
				'wholesaleMarginFlat' => $wholesaleMarginFlat[$decodedData],
				'semiWholesaleMargin' => $semiWholesaleMargin[$decodedData],
				'vat' => $vat[$decodedData],
				'purchaseCgst' => $purchaseCgst[$decodedData],
				'purchaseSgst' => $purchaseSgst[$decodedData],
				'purchaseIgst' => $purchaseIgst[$decodedData],
				'margin' => $margin[$decodedData],
				'marginFlat' => $marginFlat[$decodedData],
				'mrp' => $mrp[$decodedData],
				'igst' => $igst[$decodedData],
				'hsn' => $hsn[$decodedData],
				'color' => $color[$decodedData],
				'size' => $size[$decodedData],
				'variant' => $variant[$decodedData],
				'productDescription' => $productDescription[$decodedData],
				'additionalTax' => $additionalTax[$decodedData],
				'minimumStockLevel' => $minimumStockLevel[$decodedData],
				'documentName' => $documentName[$decodedData],
				'documentFormat' => $documentFormat[$decodedData],
				'documentPath' => $documentPath,
				'highestMeasurementUnitId' => $highestMeasurDecodedJson[$decodedData],
				'higherMeasurementUnitId' => $higherMeasurDecodedJson[$decodedData],
				'measurementUnitId' => $measurementUnitDecodedJson[$decodedData],
				'primaryMeasureUnit' => $primaryMeasureUnit[$decodedData],
				// 'quantityWisePricing' => $quantityWisePricing[$decodedData],
				'productCode' => $productCode[$decodedData],
				'productMenu' => $productMenu[$decodedData],
				'productType' => $productType[$decodedData],
				'notForSale' => $notForSale[$decodedData],
				'maxSaleQty' => $maxSaleQty[$decodedData],
				'bestBeforeTime' => $bestBeforeTime[$decodedData],
				'bestBeforeType' => $bestBeforeType[$decodedData],
				'cessFlat' => $cessFlat[$decodedData],
				'cessPercentage' => $cessPercentage[$decodedData],
				'taxInclusive' => $taxInclusive[$decodedData],
				'webIntegration' => $webIntegration[$decodedData],
				'opening' => $opening[$decodedData],
				'quantity' => $productQty[$decodedData],
				'remark' => $remark[$decodedData],
				'productCoverId' => $productCoverId[$decodedData],
				'createdBy' => $createdBy[$decodedData],
				'updatedBy' => $updatedBy[$decodedData],
				'createdTime' => $getCreatedTime[$decodedData],
				'updatedTime' => $getUpdatedTime[$decodedData],
				'createdAt' => $getCreatedDate[$decodedData],
				'updatedAt' => $getUpdatedDate[$decodedData],
				'companyId' => $companyId[$decodedData],
				'branchId' => $branchId[$decodedData],
				'productCategoryId' => $productCatId[$decodedData],
				'productGroupId' => $productGrpId[$decodedData],
				// 'document' => $documentDataArray[$decodedData]
				'highestUnitQty' => $highestUnitQty[$decodedData],
				'higherUnitQty' => $higherUnitQty[$decodedData],
				'lowestUnitQty' => $lowestUnitQty[$decodedData]
			);
			
			$data[$decodedData]['mediumMeasurementUnitId'] = $mediumMeasurDecodedJson[$decodedData];
			$data[$decodedData]['mediumLowerMeasurementUnitId'] = $mediumLowerMeasurDecodedJson[$decodedData];
			$data[$decodedData]['lowerMeasurementUnitId'] = $lowerMeasurDecodedJson[$decodedData];

			$data[$decodedData]['mediumPurchasePrice'] = $mediumPurchasePrice[$decodedData];
			$data[$decodedData]['mediumLowerPurchasePrice'] = $mediumLowerPurchasePrice[$decodedData];
			$data[$decodedData]['lowerPurchasePrice'] = $lowerPurchasePrice[$decodedData];

			$data[$decodedData]['mediumUnitQty'] = $mediumUnitQty[$decodedData];
			$data[$decodedData]['mediumLowerUnitQty'] = $mediumLowerUnitQty[$decodedData];
			$data[$decodedData]['lowerUnitQty'] = $lowerUnitQty[$decodedData];

			$data[$decodedData]['highestMouConv'] = $highestMouConv[$decodedData];
			$data[$decodedData]['higherMouConv'] = $higherMouConv[$decodedData];
			$data[$decodedData]['mediumMouConv'] = $mediumMouConv[$decodedData];
			$data[$decodedData]['mediumLowerMouConv'] = $mediumLowerMouConv[$decodedData];
			$data[$decodedData]['lowerMouConv'] = $lowerMouConv[$decodedData];
			$data[$decodedData]['lowestMouConv'] = $lowestMouConv[$decodedData];
		}
		
		return json_encode($data);
	}
}