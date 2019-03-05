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
class EncodeData extends ProductCategoryService 
{
    public function getEncodedData($status)
	{
		$constantArray = new ConstantClass();
		$constantArrayData = $constantArray->constantVariable();

		$decodedJson = json_decode($status,true);
		$createdAt = $decodedJson[0]['created_at'];
		$updatedAt= $decodedJson[0]['updated_at'];
		$productId= $decodedJson[0]['product_id'];
		$productName= $decodedJson[0]['product_name'];
		$altProductName= $decodedJson[0]['alt_product_name'];

		$highestMeasurementUnitId= $decodedJson[0]['highest_measurement_unit_id'];
		$higherMeasurementUnitId= $decodedJson[0]['higher_measurement_unit_id'];

		$mediumMeasurementUnitId= $decodedJson[0]['medium_measurement_unit_id'];
		$mediumLowerMeasurementUnitId= $decodedJson[0]['medium_lower_measurement_unit_id'];
		$lowerMeasurementUnitId= $decodedJson[0]['lower_measurement_unit_id'];

		$measurementUnit= $decodedJson[0]['measurement_unit'];

		$primaryMeasureUnit= $decodedJson[0]['primary_measure_unit'];
		// $quantityWisePricing = isset($decodedJson[0]['quantityWisePricing']) ? $decodedJson[0]['quantityWisePricing'] : array();
		$isDisplay= $decodedJson[0]['is_display'];

		$highestPurchasePrice= $decodedJson[0]['highest_purchase_price'];
		$higherPurchasePrice= $decodedJson[0]['higher_purchase_price'];

		$mediumPurchasePrice= $decodedJson[0]['medium_purchase_price'];
		$mediumLowerPurchasePrice= $decodedJson[0]['medium_lower_purchase_price'];
		$lowerPurchasePrice= $decodedJson[0]['lower_purchase_price'];

		$highestUnitQty = $decodedJson[0]['highest_unit_qty'] ? $decodedJson[0]['highest_unit_qty'] : 0;
		$higherUnitQty = $decodedJson[0]['higher_unit_qty'] ? $decodedJson[0]['higher_unit_qty'] : 0;

		$mediumUnitQty = $decodedJson[0]['medium_unit_qty'] ? $decodedJson[0]['medium_unit_qty'] : 0;
		$mediumLowerUnitQty = $decodedJson[0]['medium_lower_unit_qty'] ? $decodedJson[0]['medium_lower_unit_qty'] : 0;
		$lowerUnitQty = $decodedJson[0]['lower_unit_qty'] ? $decodedJson[0]['lower_unit_qty'] : 0;

		$lowestUnitQty = $decodedJson[0]['lowest_unit_qty'] ? $decodedJson[0]['lowest_unit_qty'] : 0;

		$highestMouConv = $decodedJson[0]['highest_mou_conv'] ? $decodedJson[0]['highest_mou_conv'] : 1;
		$higherMouConv = $decodedJson[0]['higher_mou_conv'] ? $decodedJson[0]['higher_mou_conv'] : 1;
		$mediumMouConv = $decodedJson[0]['medium_mou_conv'] ? $decodedJson[0]['medium_mou_conv'] : 1;
		$mediumLowerMouConv = $decodedJson[0]['medium_lower_mou_conv'] ? $decodedJson[0]['medium_lower_mou_conv'] : 1;
		$lowerMouConv = $decodedJson[0]['lower_mou_conv'] ? $decodedJson[0]['lower_mou_conv'] : 1;
		$lowestMouConv = $decodedJson[0]['lowest_mou_conv'] ? $decodedJson[0]['lowest_mou_conv'] : 1;

		$purchasePrice= $decodedJson[0]['purchase_price'];
		$wholesaleMargin= $decodedJson[0]['wholesale_margin'];
		$wholesaleMarginFlat= $decodedJson[0]['wholesale_margin_flat'];
		$semiWholeSaleMargin= $decodedJson[0]['semi_wholesale_margin'];
		$vat= $decodedJson[0]['vat'];
		$purchaseCgst= $decodedJson[0]['purchase_cgst'];
		$purchaseSgst= $decodedJson[0]['purchase_sgst'];
		$purchaseIgst= $decodedJson[0]['purchase_igst'];
		$mrp= $decodedJson[0]['mrp'];
		$igst= $decodedJson[0]['igst'];
		$hsn= $decodedJson[0]['hsn'];
		$color= $decodedJson[0]['color'];
		$size= $decodedJson[0]['size'];
		$margin = $decodedJson[0]['margin'];
		$marginFlat= $decodedJson[0]['margin_flat'];
		$productDescription= $decodedJson[0]['product_description'];
		$additionalTax= $decodedJson[0]['additional_tax'];
		$minimumStockLevel= $decodedJson[0]['minimum_stock_level'];

		$productMenu = $decodedJson[0]['product_menu'];
		$productCode = $decodedJson[0]['product_code'];
		$productType = $decodedJson[0]['product_type'];
		$notForSale = $decodedJson[0]['not_for_sale'];
		$maxSaleQty = $decodedJson[0]['max_sale_qty'];
		$bestBeforeTime = $decodedJson[0]['best_before_time'];
		$bestBeforeType = $decodedJson[0]['best_before_type'];
		$cessFlat = $decodedJson[0]['cess_flat'];
		$cessPercentage = $decodedJson[0]['cess_percentage'];
		$taxInclusive = $decodedJson[0]['tax_inclusive'];
		$webIntegration = $decodedJson[0]['web_integration'];
		$opening = $decodedJson[0]['opening'];
		$remark = $decodedJson[0]['remark'];
		$productCoverId = $decodedJson[0]['product_cover_id'];

		$documentName= $decodedJson[0]['document_name'];
		$documentFormat= $decodedJson[0]['document_format'];
		$productCatId= $decodedJson[0]['product_category_id'];
		$productGrpId= $decodedJson[0]['product_group_id'];
		$companyId= $decodedJson[0]['company_id'];
		$branchId= $decodedJson[0]['branch_id'];
		

		//convert amount(number_format) into their company's selected decimal points
		$highestPurchasePrice = number_format($highestPurchasePrice,2,'.','');
		$higherPurchasePrice = number_format($higherPurchasePrice,2,'.','');
		
		$mediumPurchasePrice = number_format($mediumPurchasePrice,2,'.','');
		$mediumLowerPurchasePrice = number_format($mediumLowerPurchasePrice,2,'.','');
		$lowerPurchasePrice = number_format($lowerPurchasePrice,2,'.','');

		$purchasePrice = number_format($purchasePrice,2,'.','');
		$wholesaleMargin = number_format($wholesaleMargin,2,'.','');
		$semiWholeSaleMargin = number_format($semiWholeSaleMargin,2,'.','');
		$vat= number_format($vat,2,'.','');
		$purchaseCgst= number_format($purchaseCgst,2,'.','');
		$purchaseSgst= number_format($purchaseSgst,2,'.','');
		$purchaseIgst= number_format($purchaseIgst,2,'.','');
		$mrp= number_format($mrp,2,'.','');
		$margin= number_format($margin,2,'.','');
		$additionalTax = number_format($additionalTax,2,'.','');
		$igst = number_format($igst,2,'.','');
		$marginFlat = number_format($marginFlat,2,'.','');
		$wholesaleMarginFlat = number_format($wholesaleMarginFlat,2,'.','');
				
		$highestUnitQty = number_format($highestUnitQty,2,'.','');
		$higherUnitQty = number_format($higherUnitQty,2,'.','');
		$mediumUnitQty = number_format($mediumUnitQty,2,'.','');
		$mediumLowerUnitQty = number_format($mediumLowerUnitQty,2,'.','');
		$lowerUnitQty = number_format($lowerUnitQty,2,'.','');
		$lowestUnitQty = number_format($lowestUnitQty,2,'.','');


		$highestMouConv = number_format($highestMouConv,2,'.','');
		$higherMouConv = number_format($higherMouConv,2,'.','');
		$mediumMouConv = number_format($mediumMouConv,2,'.','');
		$mediumLowerMouConv = number_format($mediumLowerMouConv,2,'.','');
		$lowerMouConv = number_format($lowerMouConv,2,'.','');
		$lowestMouConv = number_format($lowestMouConv,2,'.','');


		//get the product_cat_details from database
		// $encodeMeasurementService = new MeasurementService();
		// $highestMeasurementUnitStatus = $encodeMeasurementService->getMeasurementData($highestMeasurementUnitId);
		// $highestMeasurDecodedJson = json_decode($highestMeasurementUnitStatus,true);
		$highestMeasurDecodedJson = $highestMeasurementUnitId;
		// if(is_array($highestMeasurDecodedJson)){
		// 	$highestMeasurDecodedJson['measurementUnit'] = 'highest';
		// 	$highestMeasurDecodedJson['purchasePrice'] = $highestPurchasePrice;
		// }

		// $higherMeasurementUnitStatus = $encodeMeasurementService->getMeasurementData($higherMeasurementUnitId);
		// $higherMeasurDecodedJson = json_decode($higherMeasurementUnitStatus,true);
		$higherMeasurDecodedJson = $higherMeasurementUnitId;
		// if(is_array($higherMeasurDecodedJson)){
		// 	$higherMeasurDecodedJson['measurementUnit'] = 'higher';
		// 	$higherMeasurDecodedJson['purchasePrice'] = $higherPurchasePrice;
		// }
		$mediumMeasurDecodedJson = $mediumMeasurementUnitId;
		$mediumLowerMeasurDecodedJson = $mediumLowerMeasurementUnitId;
		$lowerMeasurDecodedJson = $lowerMeasurementUnitId;
		// $measurementUnitStatus = $encodeMeasurementService->getMeasurementData($measurementUnit);
		// $measurementUnitDecodedJson = json_decode($measurementUnitStatus,true);
		$measurementUnitDecodedJson = $measurementUnit;
		// if(is_array($measurementUnitDecodedJson)){
		// 	$measurementUnitDecodedJson['measurementUnit'] = 'lowest';
		// 	$measurementUnitDecodedJson['purchasePrice'] = $purchasePrice;
		// }

		//date format conversion['created_at','updated_at'] product
		$product = new Product();
		$convertedCreatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('d-m-Y');
		$convertedCreatedTime = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $createdAt)->format('h:i A');
		$product->setCreated_at($convertedCreatedDate);
		$getCreatedDate = $product->getCreated_at();
		$getCreatedTime = $convertedCreatedTime;
		
		if(strcmp($updatedAt,'0000-00-00 00:00:00')==0)
		{
			$getUpdatedDate = "00-00-0000";
			$getUpdatedTime = "00:00";
		}
		else
		{
			$convertedUpdatedDate = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('d-m-Y');
			$convertedUpdatedTime = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('h:i A');
			$product->setUpdated_at($convertedUpdatedDate);
			$getUpdatedDate = $product->getUpdated_at();
			$getUpdatedTime = $convertedUpdatedTime;
		}
		$documentPath = $constantArrayData['productBarcode'];
		

		//Quantity of Product
		$quantity = 0;
		if (isset($decodedJson[0]['quantity']))
		{
			$quantity = $decodedJson[0]['quantity'];
		}

		//last Insert/Updated by user
		$createdBy = isset($decodedJson[0]['created_by']) ? $decodedJson[0]['created_by'] : 0;
		$updatedBy = isset($decodedJson[0]['updated_by']) ? $decodedJson[0]['updated_by'] : 0;

		//set all data into json array
		$data = array();
		$data['productId'] = $productId;
		$data['productName'] = $productName;
		$data['altProductName'] = $altProductName;
		$data['highestMeasurementUnitId'] = $highestMeasurDecodedJson;
		$data['higherMeasurementUnitId'] = $higherMeasurDecodedJson;

		$data['mediumMeasurementUnitId'] = $mediumMeasurDecodedJson;
		$data['mediumLowerMeasurementUnitId'] = $mediumLowerMeasurDecodedJson;
		$data['lowerMeasurementUnitId'] = $lowerMeasurDecodedJson;

		$data['measurementUnitId'] = $measurementUnitDecodedJson;
		$data['primaryMeasureUnit'] = $primaryMeasureUnit;
		// $data['quantityWisePricing'] = $quantityWisePricing;
		$data['isDisplay'] = $isDisplay;
		$data['highestPurchasePrice'] = $highestPurchasePrice;
		$data['higherPurchasePrice'] = $higherPurchasePrice;

		$data['mediumPurchasePrice'] = $mediumPurchasePrice;
		$data['mediumLowerPurchasePrice'] = $mediumLowerPurchasePrice;
		$data['lowerPurchasePrice'] = $lowerPurchasePrice;

		$data['purchasePrice'] = $purchasePrice;
		$data['wholesaleMargin'] = $wholesaleMargin;
		$data['wholesaleMarginFlat'] = $wholesaleMarginFlat;
		$data['semiWholesaleMargin'] = $semiWholeSaleMargin;
		$data['vat'] = $vat;
		$data['purchaseCgst'] = $purchaseCgst;
		$data['purchaseSgst'] = $purchaseSgst;
		$data['purchaseIgst'] = $purchaseIgst;
		$data['mrp'] = $mrp;
		$data['igst'] = $igst;
		$data['hsn'] = $hsn;
		$data['color'] = $color;
		$data['size'] = $size;
		$data['margin'] = $margin;
		$data['marginFlat'] = $marginFlat;
		$data['productDescription'] = $productDescription;
		$data['additionalTax'] = $additionalTax;
		$data['minimumStockLevel'] = $minimumStockLevel;
		$data['productMenu'] = $productMenu;
		$data['productCode'] = $productCode;
		$data['productType'] = $productType;
		$data['notForSale'] = $notForSale;
		$data['maxSaleQty'] = $maxSaleQty;
		$data['bestBeforeTime'] = $bestBeforeTime;
		$data['bestBeforeType'] = $bestBeforeType;
		$data['cessFlat'] = $cessFlat;
		$data['cessPercentage'] = $cessPercentage;
		$data['taxInclusive'] = $taxInclusive;
		$data['webIntegration'] = $webIntegration;
		$data['opening'] = $opening;
		$data['quantity'] = $quantity;
		$data['remark'] = $remark;
		$data['productCoverId'] = $productCoverId;
		$data['documentName'] = $documentName;
		$data['documentFormat'] = $documentFormat;
		$data['documentPath'] = $documentPath;
		$data['createdBy'] = $createdBy;
		$data['updatedBy'] = $updatedBy;
		$data['createdTime'] = $getCreatedTime;
		$data['updatedTime'] = $getUpdatedTime;
		$data['createdAt'] = $getCreatedDate;
		$data['updatedAt'] = $getUpdatedDate;	
		$data['productCategoryId'] = $productCatId;
		$data['productGroupId'] = $productGrpId;
		$data['companyId'] = $companyId;
		$data['branchId'] = $branchId;
		// $data['document'] = $documentDataArray;
		$data['highestUnitQty'] = $highestUnitQty;
		$data['higherUnitQty'] = $higherUnitQty;

		$data['mediumUnitQty'] = $mediumUnitQty;
		$data['mediumLowerUnitQty'] = $mediumLowerUnitQty;
		$data['lowerUnitQty'] = $lowerUnitQty;

		$data['lowestUnitQty'] = $lowestUnitQty;


		$data['highestMouConv'] = $highestMouConv;
		$data['higherMouConv'] = $higherMouConv;
		$data['mediumMouConv'] = $mediumMouConv;
		$data['mediumLowerMouConv'] = $mediumLowerMouConv;
		$data['lowerMouConv'] = $lowerMouConv;
		$data['lowestMouConv'] = $lowestMouConv;

		$encodeData = json_encode($data);
		return $encodeData;
	}
}