<?php
namespace ERP\Api\V1_0\Products\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Exceptions\ExceptionMessage;
use Carbon;
use ERP\Core\Products\Entities\EnumClasses\DiscountTypeEnum;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Core\Products\Entities\EnumClasses\measurementUnitEnum;
use ERP\Core\Products\Entities\EnumClasses\PrimaryMeasureUnitEnum;
use ERP\Entities\Constants\ConstantClass;
use stdClass;
use ERP\Model\ProductCategories\ProductCategoryModel;
use ERP\Model\ProductGroups\ProductGroupModel;
use ERP\Model\Branches\BranchModel;
use ERP\Model\Companies\CompanyModel;
use ERP\Model\Authenticate\AuthenticateModel;
use ERP\Core\Settings\Services\SettingService;
use ERP\Core\Products\Services\ProductService;
use Log;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductTransformer extends ExceptionMessage
{
    /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
    	$isDisplayFlag=0;
		$measurementUnitFlag=0;	 
		$primaryMeasureUnitFlag=0;	 
		$headerData = $request->header();
		
		//trim an input
		$tProductName = trim($request->input('productName'));
		$tAltProductName = trim($request->input('altProductName'));
		$tHighestMeaUnitId = trim($request->input('highestMeasurementUnitId'));
		$tHigherMeaUnitId = trim($request->input('higherMeasurementUnitId'));
		$tMediumMeaUnitId = trim($request->input('mediumMeasurementUnitId'));
		$tMediumLowerMeaUnitId = trim($request->input('mediumLowerMeasurementUnitId'));
		$tLowerMeaUnitId = trim($request->input('lowerMeasurementUnitId'));

		$tHigherUnitQty = trim($request->input('higherUnitQty'));
		$tHighestUnitQty = trim($request->input('highestUnitQty'));
		$tMediumUnitQty = trim($request->input('mediumUnitQty'));
		$tMediumLowerUnitQty = trim($request->input('mediumLowerUnitQty'));
		$tLowerUnitQty = trim($request->input('lowerUnitQty'));
		$tLowestUnitQty = trim($request->input('lowestUnitQty'));

		$tHigherMouConv = trim($request->input('higherMouConv'));
		$tHighestMouConv = trim($request->input('highestMouConv'));
		$tMediumMouConv = trim($request->input('mediumMouConv'));
		$tMediumLowerMouConv = trim($request->input('mediumLowerMouConv'));
		$tLowerMouConv = trim($request->input('lowerMouConv'));
		$tLowestMouConv = trim($request->input('lowestMouConv'));

		$tMediumPurchasePrice = trim($request->input('mediumPurchasePrice'));
		$tMediumLowerPurchasePrice = trim($request->input('mediumLowerPurchasePrice'));
		$tLowerPurchasePrice = trim($request->input('lowerPurchasePrice'));

		$tMeasUnit = trim($request->input('measurementUnit'));
		$tPrimaryMeasureUnit = trim($request->input('primaryMeasureUnit'));
		$tColor = trim($request->input('color'));
		$tSize = trim($request->input('size'));
		$tVariant = trim($request->input('variant'));
		$tIsDisplay = trim($request->input('isDisplay'));
		$tHighestPurchasePrice = trim($request->input('highestPurchasePrice'));
		$tHigherPurchasePrice = trim($request->input('higherPurchasePrice'));
		$tPurchasePrice = trim($request->input('purchasePrice'));
		$tWholeSaleMargin = trim($request->input('wholesaleMargin'));
		$tWholeSaleMarginFlat = trim($request->input('wholesaleMarginFlat'));
		$tSemiWholeSaleMargin = trim($request->input('semiWholesaleMargin'));
		$tVat = trim($request->input('vat'));
		$tPurchaseCgst = trim($request->input('purchaseCgst'));
		$tPurchaseSgst = trim($request->input('purchaseSgst'));
		$tPurchaseIgst = trim($request->input('purchaseIgst'));
		$tMrp = trim($request->input('mrp'));
		$tIgst = trim($request->input('igst'));
		$tHsn = trim($request->input('hsn'));
		$tMargin = trim($request->input('margin'));
		$tMarginFlat = trim($request->input('marginFlat'));
		$tProductDescription = trim($request->input('productDescription'));
		$tAdditionalTax = trim($request->input('additionalTax'));
		$tMinimumStockLevel = trim($request->input('minimumStockLevel'));
		$tCompanyId = trim($request->input('companyId'));
		$tProductCatId = trim($request->input('productCategoryId'));
		$tProductGrpId = trim($request->input('productGroupId'));
		$tOpening = trim($request->input('opening'));
		$tRemark = trim($request->input('remark'));
		$tBranchId = trim($request->input('branchId'));
		$tBarcodeNo = trim($request->input('productCode'));
		if(strcmp("product",trim($request->input('productType')))==0 || strcmp("accessories",trim($request->input('productType')))==0 || 
			strcmp("service",trim($request->input('productType')))==0 || strcmp("",trim($request->input('productType')))==0)
		{
			$tProductType =  trim($request->input('productType'));
		}
		else
		{
			return "1";
		}
		if(strcmp("ok",trim($request->input('productMenu')))==0 || strcmp("not",trim($request->input('productMenu')))==0 || 
			strcmp("",trim($request->input('productMenu')))==0)
		{
			$tProductMenu =  trim($request->input('productMenu'));
		}
		else
		{
			return "1";
		}
		if(strcmp("true",trim($request->input('notForSale')))==0 || strcmp("false",trim($request->input('notForSale')))==0 || 
			strcmp("",trim($request->input('notForSale')))==0)
		{
			$tNotForSale =  trim($request->input('notForSale'));
		}
		else
		{
			return "1";
		}
		if(strcmp("inclusive",trim($request->input('taxInclusive')))==0 || strcmp("exclusive",trim($request->input('taxInclusive')))==0 || 
			strcmp("",trim($request->input('taxInclusive')))==0)
		{
			$tTaxInclusive =  trim($request->input('taxInclusive'));
		}
		else
		{
			return "1";
		}
		if(strcmp("day",trim($request->input('bestBeforeType')))==0 || strcmp("month",trim($request->input('bestBeforeType')))==0 || 
			strcmp("year",trim($request->input('bestBeforeType')))==0 || strcmp("",trim($request->input('bestBeforeType')))==0)
		{
			$tBestBeforeType =  trim($request->input('bestBeforeType'));
		}
		else
		{
			return "1";
		}

		// $tNotForSale = trim($request->input('notForSale'));
		$tMaxSaleQty = trim($request->input('maxSaleQty'));
		$tBestBeforeTime = trim($request->input('bestBeforeTime'));
		// $tBestBeforeType = trim($request->input('bestBeforeType'));
		$tCessFlat = trim($request->input('cessFlat'));
		$tCessPercentage = trim($request->input('cessPercentage'));

		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if($tIsDisplay=="")
		{
			$tIsDisplay=$enumIsDispArray['display'];
		}
		else
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($value,$tIsDisplay)==0)
				{
					$isDisplayFlag=1;
					break;
				}
				else
				{
					$isDisplayFlag=2;
				}
			}
		}

		//get user-id and add it to the database product-insertion operation
 		$authenticateModel = new AuthenticateModel();
 		$userId = $authenticateModel->getActiveUser($headerData);
		
		$tCreatedBy = isset($userId[0]->user_id) ? $userId[0]->user_id : 0;


		//check Primary Measure enum data
		$enumPrimaryMeasureUnitArray = array();
		$primaryMeasureUnitEnum = new PrimaryMeasureUnitEnum();
		$enumPrimaryMeasureUnitArray = $primaryMeasureUnitEnum->enumArrays();
		if($tPrimaryMeasureUnit != "")
		{
			foreach ($enumPrimaryMeasureUnitArray as $key => $value)
			{
				if(strcmp($value,$tPrimaryMeasureUnit)==0)
				{
					$primaryMeasureUnitFlag=1;
					break;
				}
				else
				{
					$primaryMeasureUnitFlag=2;
				}
			}
		}

		// Default Values of Measurement Related Data
		$tHighestMeaUnitId = $tHighestMeaUnitId ? $tHighestMeaUnitId : 0;
		$tHigherMeaUnitId = $tHigherMeaUnitId ? $tHigherMeaUnitId : 0;
		$tMediumMeaUnitId = $tMediumMeaUnitId ? $tMediumMeaUnitId : 0;
		$tMediumLowerMeaUnitId = $tMediumLowerMeaUnitId ? $tMediumLowerMeaUnitId : 0;
		$tLowerMeaUnitId = $tLowerMeaUnitId ? $tLowerMeaUnitId : 0;
		$tHigherUnitQty = $tHigherUnitQty ? $tHigherUnitQty : 0;
		$tHighestUnitQty = $tHighestUnitQty ? $tHighestUnitQty : 0;
		$tMediumUnitQty = $tMediumUnitQty ? $tMediumUnitQty : 0;
		$tMediumLowerUnitQty = $tMediumLowerUnitQty ? $tMediumLowerUnitQty : 0;
		$tLowerUnitQty = $tLowerUnitQty ? $tLowerUnitQty : 0;
		$tLowestUnitQty = $tLowestUnitQty ? $tLowestUnitQty : 0;
		$tHigherMouConv = $tHigherMouConv ? $tHigherMouConv : 0;
		$tHighestMouConv = $tHighestMouConv ? $tHighestMouConv : 0;
		$tMediumMouConv = $tMediumMouConv ? $tMediumMouConv : 0;
		$tMediumLowerMouConv = $tMediumLowerMouConv ? $tMediumLowerMouConv : 0;
		$tLowerMouConv = $tLowerMouConv ? $tLowerMouConv : 0;
		$tLowestMouConv = $tLowestMouConv ? $tLowestMouConv : 0;
		$tMediumPurchasePrice = $tMediumPurchasePrice ? $tMediumPurchasePrice : 0;
		$tMediumLowerPurchasePrice = $tMediumLowerPurchasePrice ? $tMediumLowerPurchasePrice : 0;
		$tLowerPurchasePrice = $tLowerPurchasePrice ? $tLowerPurchasePrice : 0;

		// dd($request->input('quantityWisePricing'));

		/* Pricing */
			$tPricingArray = array();
			if (array_key_exists("quantityWisePricing",$request->input())) 
			{
				for ($trimPricing=0;$trimPricing<count($request->input('quantityWisePricing'));$trimPricing++) 
				{
					$tPricingArray[$trimPricing] = array();
					$tPricingArray[$trimPricing]['productPricingId'] = @is_numeric(trim($request->input('quantityWisePricing')[$trimPricing]['productPricingId'])) ? trim($request->input('quantityWisePricing')[$trimPricing]['productPricingId']) : null;
					$tPricingArray[$trimPricing]['fromQty'] = trim($request->input('quantityWisePricing')[$trimPricing]['fromQty']);
					$tPricingArray[$trimPricing]['toQty'] = trim($request->input('quantityWisePricing')[$trimPricing]['toQty']);
					$tPricingArray[$trimPricing]['salesPrice'] = $this->checkValue(trim($request->input('quantityWisePricing')[$trimPricing]['salesPrice']));
				}
			}
		/* End Pricing */

		if($isDisplayFlag==2 || $measurementUnitFlag==2 || $primaryMeasureUnitFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['product_name'] = $tProductName;
			$data['alt_product_name'] = $tAltProductName;
			$data['highest_measurement_unit_id'] = $tHighestMeaUnitId;
			$data['higher_measurement_unit_id'] = $tHigherMeaUnitId;
			$data['measurement_unit'] = $tMeasUnit;
			$data['primary_measure_unit'] = $tPrimaryMeasureUnit;
			$data['color'] = $tColor;
			$data['size'] = $tSize;
			$data['variant'] = $tVariant;
			$data['is_display'] = $tIsDisplay;
			$data['highest_purchase_price'] = $tHighestPurchasePrice;

			$data['highest_unit_qty'] = $tHighestUnitQty;
			$data['higher_unit_qty'] = $tHigherUnitQty;
			$data['lowest_unit_qty'] = $tLowestUnitQty;

			$data['medium_measurement_unit_id'] = $tMediumMeaUnitId;
			$data['medium_lower_measurement_unit_id'] = $tMediumLowerMeaUnitId;
			$data['lower_measurement_unit_id'] = $tLowerMeaUnitId;
			$data['medium_unit_qty'] = $tMediumUnitQty;
			$data['medium_lower_unit_qty'] = $tMediumLowerUnitQty;
			$data['lower_unit_qty'] = $tLowerUnitQty;
			$data['lowest_unit_qty'] = $tLowestUnitQty;
			$data['higher_mou_conv'] = $tHigherMouConv;
			$data['highest_mou_conv'] = $tHighestMouConv;
			$data['medium_mou_conv'] = $tMediumMouConv;
			$data['medium_lower_mou_conv'] = $tMediumLowerMouConv;
			$data['lower_mou_conv'] = $tLowerMouConv;
			$data['lowest_mou_conv'] = $tLowestMouConv;
			$data['medium_purchase_price'] = $tMediumPurchasePrice;
			$data['medium_lower_purchase_price'] = $tMediumLowerPurchasePrice;
			$data['lower_purchase_price'] = $tLowerPurchasePrice;
			$data['higher_purchase_price'] = $tHigherPurchasePrice;

			$data['purchase_price'] = $tPurchasePrice;
			$data['wholesale_margin'] = $tWholeSaleMargin;
			$data['wholesale_margin_flat'] = $tWholeSaleMarginFlat;
			$data['vat'] = $tVat;
			$data['purchase_cgst'] = $tPurchaseCgst;
			$data['purchase_sgst'] = $tPurchaseSgst;
			$data['purchase_igst'] = $tPurchaseIgst;
			$data['mrp'] = $tMrp;
			$data['igst'] = $tIgst;
			$data['hsn'] = $tHsn;
			$data['margin'] = $tMargin;
			$data['margin_flat'] = $tMarginFlat;
			$data['product_description'] = $tProductDescription;
			$data['additional_tax'] = $tAdditionalTax;
			$data['minimum_stock_level'] = $tMinimumStockLevel;
			$data['semi_wholesale_margin'] = $tSemiWholeSaleMargin;
			$data['company_id'] = $tCompanyId;
			$data['product_category_id'] = $tProductCatId;
			$data['product_group_id'] = $tProductGrpId;
			$data['branch_id'] = $tBranchId;
			$data['product_type'] = $tProductType;
			$data['product_menu'] = $tProductMenu;
			$data['not_for_sale'] = $tNotForSale;
			$data['tax_inclusive'] = $tTaxInclusive;
			$data['max_sale_qty'] = $tMaxSaleQty;
			$data['best_before_time'] = $tBestBeforeTime;
			$data['best_before_type'] = $tBestBeforeType;
			$data['cess_flat'] = $tCessFlat;
			$data['cess_percentage'] = $tCessPercentage;
			$data['opening'] = $tOpening;
			$data['remark'] = $tRemark;
			$data['created_by'] = $tCreatedBy;
			$data['product_code'] = $tBarcodeNo;

			if (!empty($tPricingArray)) {
				$data['quantityWisePricing'] = json_encode($tPricingArray);
			}

			return $data;
		}
	}
	
	/**
     * @param Request $request
     * @return array
     */
    public function trimInsertBatchData(Request $request)
    {
    	$transformerClass = new ProductTransformer();
		$exceptionArray = $transformerClass->messageArrays();
		
		//data mapping
		$mappingResult = $this->mappingData($request->input());

		if(is_array($mappingResult))
		{
			$data = array();
			$errorArray = array();
			$inputRequestData = $mappingResult;
			$errorIndex = 0;
			$dataIndex = 0;
			for($arrayData=0;$arrayData<count($inputRequestData);$arrayData++)
			{
				$tIsDisplay='';
				$isDisplayFlag=0;
				$measurementUnitFlag=0;
				$notForSale=0;
				$productType=0;
				$productMenu=0;
				$bestBeforeType=0;

				//trim an input
				$tProductName = trim($inputRequestData[$arrayData]['productName']);
				// $tAltProductName = trim($inputRequestData[$arrayData]['altProductName']);
				$tMeasUnit = trim($inputRequestData[$arrayData]['measurementUnit']);
				$tColor = array_key_exists("color",$inputRequestData[$arrayData]) ? trim($inputRequestData[$arrayData]['color']) : "XX";
				$tSize = array_key_exists("size",$inputRequestData[$arrayData]) ? trim($inputRequestData[$arrayData]['size']) : "ZZ";
				$tVariant = array_key_exists("variant",$inputRequestData[$arrayData]) ? trim($inputRequestData[$arrayData]['variant']) : "YY";
				// $tIsDisplay = trim($inputRequestData[$arrayData]['isDisplay']);
				$tPurchasePrice = trim($inputRequestData[$arrayData]['purchasePrice']);
				$tWholeSaleMargin = trim($inputRequestData[$arrayData]['wholesaleMargin']);
				$tWholeSaleMarginFlat = trim($inputRequestData[$arrayData]['wholesaleMarginFlat']);
				$tSemiWholeSaleMargin = trim($inputRequestData[$arrayData]['semiWholesaleMargin']);
				$tVat = trim($inputRequestData[$arrayData]['vat']);
				$tMrp = trim($inputRequestData[$arrayData]['mrp']);
				// $tIgst = trim($inputRequestData[$arrayData]['igst']);
				// $tHsn = trim($inputRequestData[$arrayData]['hsn']);
				$tMargin = trim($inputRequestData[$arrayData]['margin']);
				$tMarginFlat = trim($inputRequestData[$arrayData]['marginFlat']);
				$tProductDescription = trim($inputRequestData[$arrayData]['productDescription']);
				$tAdditionalTax = trim($inputRequestData[$arrayData]['additionalTax']);
				$tMinimumStockLevel = trim($inputRequestData[$arrayData]['minimumStockLevel']);

				// $tHigherUnitQty = trim($inputRequestData[$arrayData]['higherUnitQty']);
				// $tHighestUnitQty = trim($inputRequestData[$arrayData]['highestUnitQty']);
				// $tLowestUnitQty = trim($inputRequestData[$arrayData]['lowestUnitQty']);


				if(strcmp("product",strtolower(trim($inputRequestData[$arrayData]['productType'])))==0 || strcmp("accessories",strtolower(trim($inputRequestData[$arrayData]['productType'])))==0 || 
					strcmp("service",strtolower(trim($inputRequestData[$arrayData]['productType'])))==0 || strcmp("",trim($inputRequestData[$arrayData]['productType']))==0)
				{
					$tProductType =  trim($inputRequestData[$arrayData]['productType']);
				}
				else
				{
					$tProductType =  trim($inputRequestData[$arrayData]['productType']);
					$productType=2;
				}
				if(strcmp("ok",strtolower(trim($inputRequestData[$arrayData]['productMenu'])))==0 || strcmp("not",strtolower(trim($inputRequestData[$arrayData]['productMenu'])))==0 || 
					strcmp("",trim($inputRequestData[$arrayData]['productMenu']))==0)
				{
					$tProductMenu =  trim($inputRequestData[$arrayData]['productMenu']);
				}
				else
				{
					$tProductMenu =  trim($inputRequestData[$arrayData]['productMenu']);
					$productMenu=2;
				}
				if(strcmp("true",strtolower(trim($inputRequestData[$arrayData]['notForSale'])))==0 || strcmp("false",strtolower(trim($inputRequestData[$arrayData]['notForSale'])))==0 || 
					strcmp("",trim($inputRequestData[$arrayData]['notForSale']))==0)
				{
					$tNotForSale =  trim($inputRequestData[$arrayData]['notForSale']);
				}
				else
				{
					$tNotForSale =  trim($inputRequestData[$arrayData]['notForSale']);
					$notForSale=2;
				}
				// if(strcmp("inclusive",strtolower(trim($inputRequestData[$arrayData]['taxInclusive'])))==0 || strcmp("exclusive",strtolower(trim($inputRequestData[$arrayData]['taxInclusive'])))==0 || 
				// 	strcmp("",trim($inputRequestData[$arrayData]['taxInclusive']))==0)
				// {
				// 	$tTaxInclusive =  trim($inputRequestData[$arrayData]['taxInclusive']);
				// }
				// else
				// {
				// 	$tTaxInclusive =  trim($inputRequestData[$arrayData]['taxInclusive']);
				// 	$taxInclusive=2;
				// }
				if(strcmp("day",strtolower(trim($inputRequestData[$arrayData]['bestBeforeType'])))==0 || strcmp("month",strtolower(trim($inputRequestData[$arrayData]['bestBeforeType'])))==0 || 
					strcmp("year",strtolower(trim($inputRequestData[$arrayData]['bestBeforeType'])))==0 || strcmp("",trim($inputRequestData[$arrayData]['bestBeforeType']))==0)
				{
					$tBestBeforeType =  trim($inputRequestData[$arrayData]['bestBeforeType']);
				}
				else
				{
					$tBestBeforeType =  trim($inputRequestData[$arrayData]['bestBeforeType']);
					$bestBeforeType=2;
				}

				// $tProductMenu = trim($inputRequestData[$arrayData]['productMenu']);
				// $tProductType = trim($inputRequestData[$arrayData]['productType']);
				$tMaxSaleQty = trim($inputRequestData[$arrayData]['maxSaleQty']);
				// $tNotForSale = trim($inputRequestData[$arrayData]['notForSale']);
				$tBestBeforeTime = trim($inputRequestData[$arrayData]['bestBeforeTime']);
				// $tBestBeforeType= trim($inputRequestData[$arrayData]['bestBeforeType']);
				$tCessFlat = trim($inputRequestData[$arrayData]['cessFlat']);
				$tCessPercentage = trim($inputRequestData[$arrayData]['cessPercentage']);
				$tOpening = trim($inputRequestData[$arrayData]['opening']);
				// Git missed change
				// $tIgst = trim($inputRequestData[$arrayData]['igst']);
				// $tHsn = trim($inputRequestData[$arrayData]['hsn']);

				$tCompanyId = trim($inputRequestData[$arrayData]['companyId']);
				$tProductCatId = trim($inputRequestData[$arrayData]['productCategoryId']);
				$tProductGrpId = trim($inputRequestData[$arrayData]['productGroupId']);
				$tBranchId = trim($inputRequestData[$arrayData]['branchId']);

				// $tBarcodeNo = trim($inputRequestData[$arrayData]['productCode']);
				
				$tProductName = preg_replace('/[^a-zA-Z0-9 &,\/_`#().\'-]/', '',$tProductName);
				$enumIsDispArray = array();
				$isDispEnum = new IsDisplayEnum();
				$enumIsDispArray = $isDispEnum->enumArrays();
				if($tIsDisplay=="")
				{
					$tIsDisplay=$enumIsDispArray['display'];
				}
				else
				{
					foreach ($enumIsDispArray as $key => $value)
					{
						if(strcmp($value,$tIsDisplay)==0)
						{
							$isDisplayFlag=1;
							break;
						}
						else
						{
							$isDisplayFlag=2;
						}
					}
				}
				
				// $enumMeasurementUnitArray = array();
				// $measurementUnitEnum = new measurementUnitEnum();
				// $enumMeasurementUnitArray = $measurementUnitEnum->enumArrays();
				// if($tMeasUnit!="")
				// {
				// 	foreach ($enumMeasurementUnitArray as $key => $value)
				// 	{
				// 		if(strcmp($value,$tMeasUnit)==0)
				// 		{
				// 			$measurementUnitFlag=1;
				// 			break;
				// 		}
				// 		else
				// 		{
				// 			$measurementUnitFlag=2;
				// 		}
				// 	}
				// }
				if($isDisplayFlag==2 || $notForSale==2 || $productType==2 || $productMenu==2 || $bestBeforeType==2)
				{
					$errorArray[$errorIndex] = array();
					$errorArray[$errorIndex]['productName'] = $tProductName;
					// $errorArray[$errorIndex]['altProductName'] = $tAltProductName;
					$errorArray[$errorIndex]['measurementUnit'] = $tMeasUnit;
					$errorArray[$errorIndex]['color'] = $tColor;
					$errorArray[$errorIndex]['size'] = $tSize;
					// $errorArray[$errorIndex]['variant'] = $tVariant;
					$errorArray[$errorIndex]['isDisplay'] = $tIsDisplay;
					$errorArray[$errorIndex]['purchasePrice'] = $tPurchasePrice;
					$errorArray[$errorIndex]['wholesaleMargin'] = $tWholeSaleMargin;
					$errorArray[$errorIndex]['wholesaleMarginFlat'] = $tWholeSaleMarginFlat;
					$errorArray[$errorIndex]['semiWholesaleMargin'] = $tSemiWholeSaleMargin;
					$errorArray[$errorIndex]['vat'] = $tVat;
					$errorArray[$errorIndex]['mrp'] = $tMrp;

					// $errorArray[$errorIndex]['higherUnitQty'] = $tHigherUnitQty;
					// $errorArray[$errorIndex]['highestUnitQty'] = $tHighestUnitQty;
					// $errorArray[$errorIndex]['lowestUnitQty'] = $tLowestUnitQty;
					// $errorArray[$errorIndex]['igst'] = $tIgst;
					// $errorArray[$errorIndex]['hsn'] = $tHsn;
					$errorArray[$errorIndex]['margin'] = $tMargin;
					$errorArray[$errorIndex]['marginFlat'] = $tMarginFlat;
					$errorArray[$errorIndex]['productDescription'] = $tProductDescription;
					$errorArray[$errorIndex]['additionalTax'] = $tAdditionalTax;
					$errorArray[$errorIndex]['minimumStockLevel'] = $tMinimumStockLevel;
					$errorArray[$errorIndex]['productMenu'] = $tProductMenu;
					$errorArray[$errorIndex]['productType'] = $tProductType;
					$errorArray[$errorIndex]['maxSaleQty'] = $tMaxSaleQty;
					$errorArray[$errorIndex]['notForSale'] = $tNotForSale;
					// $errorArray[$errorIndex]['taxInclusive'] = $tTaxInclusive;
					$errorArray[$errorIndex]['bestBeforeTime'] = $tBestBeforeTime;
					$errorArray[$errorIndex]['bestBeforeType'] = $tBestBeforeType;
					$errorArray[$errorIndex]['cessFlat'] = $tCessFlat;
					$errorArray[$errorIndex]['cessPercentage'] = $tCessPercentage;
					$errorArray[$errorIndex]['opening'] = $tOpening;
					// Git missed change
					// $errorArray[$errorIndex]['igst'] = $tIgst;
					// $errorArray[$errorIndex]['hsn'] = $tHsn;
					$errorArray[$errorIndex]['companyId'] = $tCompanyId;
					$errorArray[$errorIndex]['productCategoryId'] = $tProductCatId;
					$errorArray[$errorIndex]['productGroupId'] = $tProductGrpId;
					$errorArray[$errorIndex]['branchId'] = $tBranchId;
					// $errorArray[$errorIndex]['product_code'] = $tBarcodeNo;
					if($isDisplayFlag==2)
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['isDisplayEnum'];
					}	
					else if($notForSale==2)
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['notForSale'];
					}
					else if($taxInclusive==2)
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['taxInclusive'];
					}
					else if($productType==2)
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['productType'];
					}
					else if($productMenu==2)
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['productMenu'];
					}
					else if($bestBeforeType==2)
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['bestBeforeType'];
					}
					else
					{
						$errorArray[$errorIndex]['remark'] = $exceptionArray['measurementUnitEnum'];
					}
					$errorIndex++;
				}
				else
				{

					//make an array
					$data[$dataIndex] = array();
					$data[$dataIndex]['product_name'] = $tProductName;
					// $data[$dataIndex]['alt_product_name'] = $tAltProductName;
					$data[$dataIndex]['measurement_unit'] = $tMeasUnit;
					$data[$dataIndex]['color'] = $tColor;
					$data[$dataIndex]['size'] = $tSize;
					// $data[$dataIndex]['variant'] = $tVariant;
					$data[$dataIndex]['is_display'] = $tIsDisplay;
					$data[$dataIndex]['purchase_price'] = $tPurchasePrice;
					$data[$dataIndex]['wholesale_margin'] = $tWholeSaleMargin;
					$data[$dataIndex]['wholesale_margin_flat'] = $tWholeSaleMarginFlat;
					$data[$dataIndex]['vat'] = $tVat;
					$data[$dataIndex]['mrp'] = $tMrp;
					// $data[$dataIndex]['product_code'] = $tBarcodeNo;

					// $data[$dataIndex]['higherUnitQty'] = $tHigherUnitQty;
					// $data[$dataIndex]['highestUnitQty'] = $tHighestUnitQty;
					// $data[$dataIndex]['lowestUnitQty'] = $tLowestUnitQty;
					// $data[$dataIndex]['igst'] = $tIgst;
					// $data[$dataIndex]['hsn'] = $tHsn;
					$data[$dataIndex]['margin'] = $tMargin;
					$data[$dataIndex]['margin_flat'] = $tMarginFlat;
					$data[$dataIndex]['product_description'] = $tProductDescription;
					$data[$dataIndex]['additional_tax'] = $tAdditionalTax;
					$data[$dataIndex]['minimum_stock_level'] = $tMinimumStockLevel;
					$data[$dataIndex]['product_menu'] = $tProductMenu;
					$data[$dataIndex]['product_type'] = $tProductType;
					$data[$dataIndex]['max_sale_qty'] = $tMaxSaleQty;
					$data[$dataIndex]['not_for_sale'] = $tNotForSale;
					// $data[$dataIndex]['tax_inclusive'] = $tTaxInclusive;
					$data[$dataIndex]['best_before_time'] = $tBestBeforeTime;
					$data[$dataIndex]['best_before_type'] = $tBestBeforeType;
					$data[$dataIndex]['cess_flat'] = $tCessFlat;
					$data[$dataIndex]['cess_percentage'] = $tCessPercentage;
					$data[$dataIndex]['opening'] = $tOpening;
					// Git missed change
					// $data[$dataIndex]['igst'] = $tIgst;
					// $data[$dataIndex]['hsn'] = $tHsn;
					$data[$dataIndex]['semi_wholesale_margin'] = $tSemiWholeSaleMargin;
					$data[$dataIndex]['company_id'] = $tCompanyId;
					$data[$dataIndex]['product_category_id'] = $tProductCatId;
					$data[$dataIndex]['product_group_id'] = $tProductGrpId;
					$data[$dataIndex]['branch_id'] = $tBranchId;
					$dataIndex++;
				}
			}
			$trimArray = array();
			$trimArray['errorArray']= $errorArray;
			$trimArray['dataArray'] = $data;
			return $trimArray;
		}
		else
		{
			return $mappingResult;
		}
	}
	
	/**
     * @param request array
     * @return array/error-message
     */
	public function mappingData()
	{
		$transformerClass = new ProductTransformer();
		$exceptionArray = $transformerClass->messageArrays();
		
		$rquestArray = func_get_arg(0);
		$mappingArray = $rquestArray['mapping'];
		$dataArray = $rquestArray['data'];
		
		$keyNameCount = array_count_values($mappingArray);
		//searching data in mapping array ..it is duplicate or not?
		for($index=0;$index<count($keyNameCount);$index++)
		{
			$value = $keyNameCount[array_keys($keyNameCount)[$index]];
			if($value>1 || array_keys($keyNameCount)[$index]=="")
			{
				return $exceptionArray['mapping'];
			}
		}

		// Git missed change
		// if(count($mappingArray)!=30)
		if(count($mappingArray)!=28)
		{
			return $exceptionArray['missingField'];
		}
			
		$requestArray = array();
		$categoryId = array();
		
		//Duplication Reduction Array
		$pro_categoryArray = array();
		$pro_groupArray = array();
		$pro_companyArray = array();
		$pro_branchArray = array();

		//make an requested array
		for($arrayData=0;$arrayData<count($dataArray);$arrayData++)
		{
			$categoryFlag=0;
			$groupFlag=0;
			$branchFlag=0;
			$companyFlag=0;
			//replace category-name with their id
			if(in_array("productCategoryId",$mappingArray))
			{
				$arrayKey = array_keys($mappingArray,"productCategoryId");
				
				//replace category-name with parent-category-id
				$convertedCatString = preg_replace('/[^A-Za-z0-9]/', '',$dataArray[$arrayData][$arrayKey[0]]);
				
				//database selection
				$categoryModel = new ProductCategoryModel();
				$convertedCatString = strtoupper($convertedCatString);

				if (!isset($pro_categoryArray[$convertedCatString])) {
					$pro_categoryArray[$convertedCatString] = $categoryModel->getCategoryId($convertedCatString);
				}
				
				if(strcmp($pro_categoryArray[$convertedCatString],$exceptionArray['204'])==0)
				{
					$categoryFlag=1;
				}
				else
				{
					$dataArray[$arrayData][$arrayKey[0]] = $pro_categoryArray[$convertedCatString];
				}
			}

			//replace group-name with their id
			if(in_array("productGroupId",$mappingArray))
			{
				$arrayKey = array_keys($mappingArray,"productGroupId");
				// replace group-name with parent-group-id
				$convertedGrpString = preg_replace('/[^A-Za-z0-9]/', '',$dataArray[$arrayData][$arrayKey[0]]);
				$convertedGrpString = strtoupper($convertedGrpString);
				// database selection
				$groupModel = new ProductGroupModel();
				if (!isset($pro_groupArray[$convertedGrpString])) {
					$pro_groupArray[$convertedGrpString] = $groupModel->getGroupId($convertedGrpString);
				}
				$groupResult = $pro_groupArray[$convertedGrpString];

				if(strcmp($groupResult,$exceptionArray['204'])==0)
				{
					Log::info("GroupName: ".$convertedGrpString." and  Result: ".$groupResult);
					$groupFlag=1;
				}
				else
				{
					$dataArray[$arrayData][$arrayKey[0]] = $groupResult;
				}
			}
			
			//replace company-name with their id
			if(in_array("companyId",$mappingArray))
			{
				$arrayKey = array_keys($mappingArray,"companyId");
				// replace group-name with parent-group-id
				$convertedCompanyString = preg_replace('/[^A-Za-z0-9]/', '',$dataArray[$arrayData][$arrayKey[0]]);
				$convertedCompanyString = strtoupper($convertedCompanyString);
				// database selection
				$companyModel = new CompanyModel();
				if (!isset($pro_companyArray[$convertedCompanyString])) {
					$pro_companyArray[$convertedCompanyString] = $companyModel->getCompanyId($convertedCompanyString);
				}
				$companyResult = $pro_companyArray[$convertedCompanyString];

				if(strcmp($companyResult,$exceptionArray['204'])==0)
				{
					$companyFlag=1;
				}
				else
				{
					$dataArray[$arrayData][$arrayKey[0]] = $companyResult;
				}
			}

			//replace branch-name with their id
			if(in_array("branchId",$mappingArray))
			{ 
				if (!$companyFlag) {
					$arrayKey = array_keys($mappingArray,"branchId");
					// replace group-name with parent-group-id
					$convertedBranchString = preg_replace('/[^A-Za-z0-9]/', '',$dataArray[$arrayData][$arrayKey[0]]);
					$convertedBranchString = strtoupper($convertedBranchString);
					// database selection
					$branchModel = new BranchModel();
					if (!isset($pro_branchArray[$companyResult.$convertedBranchString.$companyResult])) {
						$pro_branchArray[$companyResult.$convertedBranchString.$companyResult] = $branchModel->getBranchId($convertedBranchString,$companyResult);
					}
					$branchResult = $pro_branchArray[$companyResult.$convertedBranchString.$companyResult];
					if(strcmp($branchResult,$exceptionArray['204'])==0)
					{
						$branchFlag=1;
					}
					else
					{
						$dataArray[$arrayData][$arrayKey[0]] = $branchResult;
					}
				} else {
					$branchFlag=1;
				}
			}
			
			if($categoryFlag==1 || $groupFlag==1 || $branchFlag==1 || $companyFlag==1)
			{
				if($categoryFlag==1)
				{
					return $exceptionArray['invalidCategoryName'];
				}
				if($groupFlag==1)
				{
					return $exceptionArray['invalidGroupName'];
				}
				if($branchFlag==1)
				{
					return $exceptionArray['invalidBranchName'];
				}
				if($companyFlag==1)
				{
					return $exceptionArray['invalidCompanyName'];
				}
			}
			else
			{
				$requestArray[$arrayData] = array();
				$arrayKeys = array_keys($keyNameCount);
				for ($reqIndex=0; $reqIndex < 28; $reqIndex++) { 
					$requestArray[$arrayData][$arrayKeys[$reqIndex]] = $dataArray[$arrayData][$reqIndex];
				}
				// $requestArray[$arrayData][array_keys($keyNameCount)[0]] = $dataArray[$arrayData][0];
				// $requestArray[$arrayData][array_keys($keyNameCount)[1]] = $dataArray[$arrayData][1];
				// $requestArray[$arrayData][array_keys($keyNameCount)[2]] = $dataArray[$arrayData][2];
				// $requestArray[$arrayData][array_keys($keyNameCount)[3]] = $dataArray[$arrayData][3];
				// $requestArray[$arrayData][array_keys($keyNameCount)[4]] = $dataArray[$arrayData][4];
				// $requestArray[$arrayData][array_keys($keyNameCount)[5]] = $dataArray[$arrayData][5];
				// $requestArray[$arrayData][array_keys($keyNameCount)[6]] = $dataArray[$arrayData][6];
				// $requestArray[$arrayData][array_keys($keyNameCount)[7]] = $dataArray[$arrayData][7];
				// $requestArray[$arrayData][array_keys($keyNameCount)[8]] = $dataArray[$arrayData][8];
				// $requestArray[$arrayData][array_keys($keyNameCount)[9]] = $dataArray[$arrayData][9];
				// $requestArray[$arrayData][array_keys($keyNameCount)[10]] = $dataArray[$arrayData][10];
				// $requestArray[$arrayData][array_keys($keyNameCount)[11]] = $dataArray[$arrayData][11];
				// $requestArray[$arrayData][array_keys($keyNameCount)[12]] = $dataArray[$arrayData][12];
				// $requestArray[$arrayData][array_keys($keyNameCount)[13]] = $dataArray[$arrayData][13];
				// $requestArray[$arrayData][array_keys($keyNameCount)[14]] = $dataArray[$arrayData][14];
				// $requestArray[$arrayData][array_keys($keyNameCount)[15]] = $dataArray[$arrayData][15];
				// $requestArray[$arrayData][array_keys($keyNameCount)[16]] = $dataArray[$arrayData][16];
				// $requestArray[$arrayData][array_keys($keyNameCount)[17]] = $dataArray[$arrayData][17];
				// $requestArray[$arrayData][array_keys($keyNameCount)[18]] = $dataArray[$arrayData][18];
				// $requestArray[$arrayData][array_keys($keyNameCount)[19]] = $dataArray[$arrayData][19];
				// $requestArray[$arrayData][array_keys($keyNameCount)[20]] = $dataArray[$arrayData][20];
				// $requestArray[$arrayData][array_keys($keyNameCount)[21]] = $dataArray[$arrayData][21];
				// $requestArray[$arrayData][array_keys($keyNameCount)[22]] = $dataArray[$arrayData][22];
				// $requestArray[$arrayData][array_keys($keyNameCount)[23]] = $dataArray[$arrayData][23];
				// $requestArray[$arrayData][array_keys($keyNameCount)[24]] = $dataArray[$arrayData][24];
				// $requestArray[$arrayData][array_keys($keyNameCount)[25]] = $dataArray[$arrayData][25];
				// $requestArray[$arrayData][array_keys($keyNameCount)[26]] = $dataArray[$arrayData][26];
				// $requestArray[$arrayData][array_keys($keyNameCount)[27]] = $dataArray[$arrayData][27];
				// Git missed change
				// $requestArray[$arrayData][array_keys($keyNameCount)[28]] = $dataArray[$arrayData][28];
				// $requestArray[$arrayData][array_keys($keyNameCount)[29]] = $dataArray[$arrayData][29];
			}
		}
		
		return $requestArray;
	}
	
	/**
     * @param 
     * @return array
     */
    public function trimInsertInOutwardData(Request $request,$inOutWard)
    {
		$discountTypeFlag=0;
		$requestArray = array();
		$exceptionArray = array();
		$numberOfArray = count($request->input()['inventory']);
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
				//get exception message
		$exception = new ProductTransformer();
		$exceptionArray = $exception->messageArrays();

		$settingService = new SettingService();
		$settingStatus = $settingService->getData();
		if (strcmp($exceptionArray['204'], $settingStatus)==0) {
			return $settingStatus;
		}

		$settingArray = json_decode($settingStatus,true);
		$productSetting = array_first($settingArray, function($key, $value) use ($constantArray)
		{
		    return $value['settingType'] == $constantArray['productSetting'];
		},$exceptionArray['204']);

		$productMeasurementType = $productSetting['productMeasurementType'];
		$measurementTypes = $constantClass->measurementTypeConstants();


		//data get from body and trim an input
		$companyId = trim($request->input()['companyId']); 
		$transactionDate = trim($request->input()['transactionDate']); 
		$tax = trim($request->input()['tax']); 
		if(array_key_exists($constantArray['invoiceNumber'],$request->input()))
		{
			$invoiceNumber = trim($request->input()['invoiceNumber']);
			$billNumber="";
		}
		elseif(array_key_exists($constantArray['billNumber'],$request->input()))
		{
			$billNumber = trim($request->input()['billNumber']); 
			$invoiceNumber="";
		}
		else
		{
			$billNumber = ""; 
			$invoiceNumber="";
		}
		
		//transaction date conversion
		$splitedDate = explode("-",$transactionDate);
		$transformTransactionDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		// $transformEntryDate = Carbon\Carbon::createFromFormat('d-m-Y', $transactionDate)->format('Y-m-d');
		
		//get exception message
		$exception = new ProductTransformer();
		$exceptionArray = $exception->messageArrays();
		
		$enumDiscountTypeArray = array();
		$discountTypeEnum = new DiscountTypeEnum();
		$enumDiscountTypeArray = $discountTypeEnum->enumArrays();
		
		for($arrayData=0;$arrayData<$numberOfArray;$arrayData++)
		{
			$tempArray[$arrayData] = array();
			$tempArray[$arrayData][0] = trim($request->input()['inventory'][$arrayData]['productId']);
			$tempArray[$arrayData][1] = trim($request->input()['inventory'][$arrayData]['discount']);
			$tempArray[$arrayData][2] = trim($request->input()['inventory'][$arrayData]['discountType']);
			$tempArray[$arrayData][3] = trim($request->input()['inventory'][$arrayData]['price']);
			$tempArray[$arrayData][4] = trim($request->input()['inventory'][$arrayData]['qty']);
			// Get Product Units to tranform Qty into primary unit Qty
			if (strcmp($measurementTypes['unit'], $productMeasurementType)==0) {

				if (array_key_exists('stockFt', $request->input()['inventory'][$arrayData]) &&
					$request->input()['inventory'][$arrayData]['stockFt'] != 'undefined' &&
					$request->input()['inventory'][$arrayData]['stockFt'] != 0 ) {

					$tempArray[$arrayData][4] = trim($request->input()['inventory'][$arrayData]['stockFt']);

				}elseif (array_key_exists('totalFt', $request->input()['inventory'][$arrayData]) &&
					$request->input()['inventory'][$arrayData]['totalFt'] != 'undefined' &&
					$request->input()['inventory'][$arrayData]['totalFt'] != 0 ){

					$tempArray[$arrayData][4] = trim($request->input()['inventory'][$arrayData]['totalFt']);
				}
			}elseif (strcmp($measurementTypes['advance'], $productMeasurementType)==0) {

				if (array_key_exists('measurementUnit', $request->input()['inventory'][$arrayData])) {
					// Get Product Units to tranform Qty into primary unit Qty
					$productTransformData = json_decode($ProductService->getProductData($request->input()['inventory'][$arrayData]['productId']));
					$highestMeasurementUnit = $productTransformData->highestMeasurementUnitId;
					$higherMeasurementUnit = $productTransformData->higherMeasurementUnitId;
					$mediumMeasurementUnit = $productTransformData->mediumMeasurementUnitId;
					$mediumLowerMeasurementUnit = $productTransformData->mediumLowerMeasurementUnitId;
					$lowerMeasurementUnit = $productTransformData->lowerMeasurementUnitId;
					$lowestMeasurementUnit = $productTransformData->measurementUnitId;
					$primaryMeasurement = $productTransformData->primaryMeasureUnit;
					$currentQty = trim($request->input()['inventory'][$arrayData]['qty']);
					$currentMeasurementUnit = $request->input()['inventory'][$arrayData]['measurementUnit'];
					switch ($currentMeasurementUnit) {
						case $highestMeasurementUnit:
								$currentQty = round($currentQty * $productTransformData->highestMouConv);
							break;
						case $higherMeasurementUnit:
								$currentQty = round($currentQty * $productTransformData->higherMouConv);
							break;
						case $mediumMeasurementUnit:
								$currentQty = round($currentQty * $productTransformData->mediumMouConv);
							break;
						case $mediumLowerMeasurementUnit:
								$currentQty = round($currentQty * $productTransformData->mediumLowerMouConv);
							break;
						case $lowerMeasurementUnit:
								$currentQty = round($currentQty * $productTransformData->lowerMouConv);
							break;
						
						default:
								$currentQty = round($currentQty * $productTransformData->lowestMouConv);
							break;
					}
					$tempArray[$arrayData][4] = $currentQty;
				}

			}
			// Unitwise qty Conversion ends.
			
			if($tempArray[$arrayData][1]!=0 && $tempArray[$arrayData][1]!="")
			{
				if(strcmp($tempArray[$arrayData][2],$constantArray['percentage'])==0)
				{
					$tempArray[$arrayData][5]=($tempArray[$arrayData][1]/100)*$tempArray[$arrayData][3];
				}
				else
				{
					$tempArray[$arrayData][5]=$tempArray[$arrayData][1];
				}
			}
			else
		    {
				$tempArray[$arrayData][5] = 0;
				$tempArray[$arrayData][1] = 0;
		    }
			foreach ($enumDiscountTypeArray as $key => $value)
			{
				if(strcmp($value,$tempArray[$arrayData][2])==0)
				{
					$discountTypeFlag=1;
					break;
				}
				else
				{
					$discountTypeFlag=0;
				}
			}
			if($discountTypeFlag==0)
			{
				$discountTypeFlag=0;
				break;
			}
		}
		
		if($discountTypeFlag==0)
		{
			return "1";
		}
		else
		{
			// make an array
			$simpleArray = array();
			$simpleArray['transactionDate'] = $transformTransactionDate;
			$simpleArray['companyId'] = $companyId;
			$simpleArray['transactionType'] = $inOutWard;
			$simpleArray['invoiceNumber'] = $invoiceNumber;
			$simpleArray['billNumber'] = $billNumber;
			$simpleArray['tax'] = $tax;
			
			$trimArray = array();
			for($data=0;$data<$numberOfArray;$data++)
			{
				$trimArray[$data]= array(
					'productId' => $tempArray[$data][0],
					'discount' => $tempArray[$data][1],
					'discountType' => $tempArray[$data][2],
					'price' => $tempArray[$data][3],
					'qty' => $tempArray[$data][4],
					'discountValue' => $tempArray[$data][5]
				);
			}
			array_push($simpleArray,$trimArray);
			return $simpleArray;
		}
	}
	
	/**
     * @param key and value
     * @return array
     */
	public function trimUpdateData($arrayData,$headerData)
	{
		$productEnumArray = array();
		$isDisplayFlag=0;
		$measurementUnitFlag=0;
		$primaryMeasureUnitFlag=0;
		$tProductArray = array();
		$productValue;
		// $keyValue = func_get_arg(0);
		$convertedValue="";
		
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$index=0;
		foreach($arrayData as $keyValue => $value)
		{
			$convertedValue = "";
			for($asciiChar=0;$asciiChar<strlen($keyValue);$asciiChar++)
			{
				if(ord($keyValue[$asciiChar])<=90 && ord($keyValue[$asciiChar])>=65) 
				{
					$convertedValue1 = "_".chr(ord($keyValue[$asciiChar])+32);
					$convertedValue=$convertedValue.$convertedValue1;
				}
				else
				{
					$convertedValue=$convertedValue.$keyValue[$asciiChar];
				}
			}

			if(strcmp($keyValue, "quantityWisePricing")==0) 
			{
				if ($value == "") {
					$productValue[$index] = "";
				} else {
					$productValue[$index] = json_encode($value);
				}
				
				$tProductArray[$index]= array($convertedValue=> $productValue[$index]);
			}
			else
			{
				$productValue[$index] = $value;
				$tProductArray[$index]= array($convertedValue=> trim($productValue[$index]));
			}
				$productEnumArray = array_keys($tProductArray[$index])[0];

			//check enum data
			// $enumMeasurementUnitArray = array();
			// $measurementUnitEnum = new measurementUnitEnum();
			// $enumMeasurementUnitArray = $measurementUnitEnum->enumArrays();
			// if(strcmp($constantArray['measurementUnit'],$productEnumArray)==0)
			// {
			// 	foreach ($enumMeasurementUnitArray as $innerKey => $innerValue)
			// 	{
			// 		if(strcmp($tProductArray[$index]['measurement_unit'],$innerValue)==0)
			// 		{
			// 			$measurementUnitFlag=1;
			// 			break;
			// 		}
			// 		else
			// 		{
			// 			$measurementUnitFlag=2;
			// 		}
			// 	}
			// }

			//check Primary Measure enum data
			$enumPrimaryMeasureUnitArray = array();
			$primaryMeasureUnitEnum = new PrimaryMeasureUnitEnum();
			$enumPrimaryMeasureUnitArray = $primaryMeasureUnitEnum->enumArrays();
			if(strcmp($constantArray['primaryMeasureUnit'],$productEnumArray)==0)
			{
				foreach ($enumPrimaryMeasureUnitArray as $innerKey => $innerValue)
				{
					if(strcmp($tProductArray[$index]['primary_measure_unit'],$innerValue)==0)
					{
						$primaryMeasureUnitFlag=1;
						break;
					}
					else
					{
						$primaryMeasureUnitFlag=2;
					}
				}
			}

			$enumIsDispArray = array();
			$isDispEnum = new IsDisplayEnum();
			$enumIsDispArray = $isDispEnum->enumArrays();
			
			if(strcmp($constantArray['isDisplay'],$productEnumArray)==0)
			{
				foreach ($enumIsDispArray as $innerKey => $innerValue)
				{
					if(strcmp($tProductArray[$index]['is_display'],$innerValue)==0)
					{
						$isDisplayFlag=1;
						break;
					}
					else
					{
						$isDisplayFlag=2;
					}
				}
			}
			if($isDisplayFlag==2 || $measurementUnitFlag==2 || $primaryMeasureUnitFlag==2)
			{
				return "1";
			}

			$index++;
		}

		//get user-id and add it to the database product-insertion operation
 		$authenticateModel = new AuthenticateModel();
 		$userId = $authenticateModel->getActiveUser($headerData);
		
		$tUpdatedBy = isset($userId[0]->user_id) ? $userId[0]->user_id : 0;
		array_push($tProductArray, array("updated_by"=> $tUpdatedBy));

		return $tProductArray;
	}
	
	/**
	 * trim request data for update
     * @param object
     * @return array
     */
	public function trimUpdateProductData($productArray,$inOutWard)
	{
		$discountTypeFlag=0;
		$requestArray = array();
		$exceptionArray = array();
		$tProductArray = array();
		$convertedValue="";
		$arraySample = array();
		$tempArrayFlag=0;
		$productArrayFlag=0;
		$tempFlag=0;
		
		//get exception message
		$exception = new ProductTransformer();
		$exceptionArray = $exception->messageArrays();

		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$settingService = new SettingService();
		$settingStatus = $settingService->getData();
		if (strcmp($exceptionArray['204'], $settingStatus)==0) {
			return $settingStatus;
		}

		$settingArray = json_decode($settingStatus,true);
		$productSetting = array_first($settingArray, function($key, $value) use ($constantArray)
		{
		    return $value['settingType'] == $constantArray['productSetting'];
		},$exceptionArray['204']);

		$productMeasurementType = $productSetting['productMeasurementType'];
		$measurementTypes = $constantClass->measurementTypeConstants();


		//get exception message
		
		for($requestArray=0;$requestArray<count($productArray);$requestArray++)
		{
			//check if array is exists
			if(strcmp(array_keys($productArray)[$requestArray],$constantArray['inventory'])==0)
			{
				//number of array elements
				for($arrayElement=0;$arrayElement<count($productArray['inventory']);$arrayElement++)
				{
					$tempArrayFlag=1;
					$tempArray[$arrayElement] = array();
					$tempArray[$arrayElement]['product_id'] = trim($productArray['inventory'][$arrayElement]['productId']);
					$tempArray[$arrayElement]['discount'] = trim($productArray['inventory'][$arrayElement]['discount']);
					$tempArray[$arrayElement]['discount_type'] = trim($productArray['inventory'][$arrayElement]['discountType']);
					$tempArray[$arrayElement]['price'] = trim($productArray['inventory'][$arrayElement]['price']);
					$tempArray[$arrayElement]['qty'] = trim($productArray['inventory'][$arrayElement]['qty']);
					// Get Product Units to tranform Qty into primary unit Qty

					if (strcmp($measurementTypes['unit'], $productMeasurementType)==0) {

						if (array_key_exists('stockFt', $productArray['inventory'][$arrayElement]) &&
							$productArray['inventory'][$arrayElement]['stockFt'] != 'undefined' &&
							$productArray['inventory'][$arrayElement]['stockFt'] != 0 ) {

							$tempArray[$arrayElement]['qty'] = trim($productArray['inventory'][$arrayElement]['stockFt']);

						}elseif (array_key_exists('totalFt', $productArray['inventory'][$arrayElement]) &&
							$productArray['inventory'][$arrayElement]['totalFt'] != 'undefined' &&
							$productArray['inventory'][$arrayElement]['totalFt'] != 0 ){

							$tempArray[$arrayElement]['qty'] = trim($productArray['inventory'][$arrayElement]['totalFt']);
						}
					}elseif (strcmp($measurementTypes['advance'], $productMeasurementType)==0) {

						if (array_key_exists('measurementUnit', $productArray['inventory'][$arrayElement])) {
							// Get Product Units to tranform Qty into primary unit Qty
							$productTransformData = json_decode($ProductService->getProductData($productArray['inventory'][$arrayElement]['productId']));
							$highestMeasurementUnit = $productTransformData->highestMeasurementUnitId;
							$higherMeasurementUnit = $productTransformData->higherMeasurementUnitId;
							$mediumMeasurementUnit = $productTransformData->mediumMeasurementUnitId;
							$mediumLowerMeasurementUnit = $productTransformData->mediumLowerMeasurementUnitId;
							$lowerMeasurementUnit = $productTransformData->lowerMeasurementUnitId;
							$lowestMeasurementUnit = $productTransformData->measurementUnitId;
							$primaryMeasurement = $productTransformData->primaryMeasureUnit;
							$currentQty = trim($productArray['inventory'][$arrayElement]['qty']);
							$currentMeasurementUnit = $productArray['inventory'][$arrayElement]['measurementUnit'];
							switch ($currentMeasurementUnit) {
								case $highestMeasurementUnit:
										$currentQty = round($currentQty * $productTransformData->highestMouConv);
									break;
								case $higherMeasurementUnit:
										$currentQty = round($currentQty * $productTransformData->higherMouConv);
									break;
								case $mediumMeasurementUnit:
										$currentQty = round($currentQty * $productTransformData->mediumMouConv);
									break;
								case $mediumLowerMeasurementUnit:
										$currentQty = round($currentQty * $productTransformData->mediumLowerMouConv);
									break;
								case $lowerMeasurementUnit:
										$currentQty = round($currentQty * $productTransformData->lowerMouConv);
									break;
								
								default:
										$currentQty = round($currentQty * $productTransformData->lowestMouConv);
									break;
							}
							$tempArray[$arrayElement]['qty'] = $currentQty;
						}

					}
					// Unitwise qty Conversion ends.
					$tempArray[$arrayElement]['measurementUnit'] = trim($productArray['inventory'][$arrayElement]['measurementUnit']);
					
					if($tempArray[$arrayElement]['discount']!=0 && $tempArray[$arrayElement]['discount']!="")
					{
						if(strcmp($tempArray[$arrayElement]['discount_type'],$constantArray['percentage'])==0)
						{
							$tempArray[$arrayElement]['discount_value']=($tempArray[$arrayElement]['discount']/100)* $tempArray[$arrayElement]['price'];
						}
						else
						{
							$tempArray[$arrayElement]['discount_value'] = $tempArray[$arrayElement]['discount'];
						}
					}
					else
					{
						$tempArray[$arrayElement]['discount_value']=0;
					}
					//check enum type[amount-type]
					$enumDiscountTypeArray = array();
					$discountTypeEnum = new DiscountTypeEnum();
					$enumDiscountTypeArray = $discountTypeEnum->enumArrays();
					foreach ($enumDiscountTypeArray as $key => $value)
					{
						if(strcmp($value,$tempArray[$arrayElement]['discount_type'])==0)
						{
							$discountTypeFlag=1;
							break;
						}
						else
						{
							$discountTypeFlag=0;
						}
					}
				}
				if($discountTypeFlag==0)
				{
					return "1";
				}
			}
			else
			{
				$key = array_keys($productArray)[$requestArray];
				$value = $productArray[$key];
				$productArrayFlag=1;
				for($asciiChar=0;$asciiChar<strlen($key);$asciiChar++)
				{
					if(ord($key[$asciiChar])<=90 && ord($key[$asciiChar])>=65) 
					{
						$convertedValue1 = "_".chr(ord($key[$asciiChar])+32);
						$convertedValue=$convertedValue.$convertedValue1;
					}
					else
					{
						$convertedValue=$convertedValue.$key[$asciiChar];
					}
				}
				if(strcmp($convertedValue,$constantArray['transactionDate'])==0)
				{
					$transformTransactionDate=trim($value);
					$splitedDate = explode("-",$transformTransactionDate);
					$tProductArray[$convertedValue] = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
					// $transformTransactionDate = Carbon\Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
					// $tProductArray[$convertedValue]=trim($transformTransactionDate);
					$convertedValue="";
				}
				else
				{
					$tProductArray[$convertedValue]=trim($value);
					$convertedValue="";
				}
				$tempFlag=1;
			}
			if($tempFlag==1)
			{
				if($requestArray==count($productArray)-1)
				{
					$tProductArray['transaction_type']=$inOutWard;
					$tProductArray['flag']="1";
				}
			}
		}
		if($productArrayFlag==1 && $tempArrayFlag==1)
		{
			array_push($tProductArray,$tempArray);
			return $tProductArray;
		}
		else if($productArrayFlag==1)
		{
			return $tProductArray;
		}
		else
		{
			return $tempArray;
		}
	}

	/**
	* check value
	* @param integer value
	* @return tax-value/0
	*/
	public function checkValue($tax)
	{
		if($tax=='' || strcmp($tax,'undefined')==0 || is_NaN(floatval($tax)) || $tax==null)
		{
			return 0;
		}
		return $tax;	
	}
}