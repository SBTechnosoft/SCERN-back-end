<?php
namespace ERP\Core\Products\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Products\Properties\ProductIdPropertyTrait;
use ERP\Core\Products\Properties\ProductNamePropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Products\Properties\MeasureUnitPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Products\Properties\ProductGrpIdPropertyTrait;
use ERP\Core\Branches\Properties\BranchIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\ProductCategories\Properties\ProductCatIdPropertyTrait;
use ERP\Core\Products\Properties\TransactionDatePropertyTrait;	
use ERP\Core\Products\Properties\DiscountPropertyTrait;
use ERP\Core\Products\Properties\DiscountTypePropertyTrait;
use ERP\Core\Products\Properties\PricePropertyTrait;
use ERP\Core\Products\Properties\QtyPropertyTrait;
use ERP\Core\Products\Properties\TransactionTypePropertyTrait;
use ERP\Core\Products\Properties\InvoiceNumberPropertyTrait;
use ERP\Core\Products\Properties\BillNumberPropertyTrait;
use ERP\Core\Products\Properties\TaxPropertyTrait;
use ERP\Core\Products\Properties\JfIdPropertyTrait;
use ERP\Core\Products\Properties\PurchasePricePropertyTrait;
use ERP\Core\Products\Properties\WholeSaleMarginPropertyTrait;
use ERP\Core\Products\Properties\WholeSaleMarginFlatPropertyTrait;
use ERP\Core\Products\Properties\SemiWholeSaleMarginPropertyTrait;
use ERP\Core\Products\Properties\VatPropertyTrait;
use ERP\Core\Products\Properties\MrpPropertyTrait;
use ERP\Core\Products\Properties\MarginPropertyTrait;
use ERP\Core\Products\Properties\MarginFlatPropertyTrait;
use ERP\Core\Products\Properties\DiscountValuePropertyTrait;
use ERP\Core\Products\Properties\FromDatePropertyTrait;
use ERP\Core\Products\Properties\ToDatePropertyTrait;
use ERP\Core\Products\Properties\AdditionalTaxPropertyTrait;
use ERP\Core\Products\Properties\ProductDescriptionPropertyTrait;
use ERP\Core\Products\Properties\ColorPropertyTrait;
use ERP\Core\Products\Properties\SizePropertyTrait;
use ERP\Core\Products\Properties\VariantPropertyTrait;
use ERP\Core\Products\Properties\ProductCodePropertyTrait;
use ERP\Core\Products\Properties\StockLevelPropertyTrait;
use ERP\Core\Products\Properties\IgstPropertyTrait;
use ERP\Core\Products\Properties\TaxInclusiveTrait;
use ERP\Core\Products\Properties\HsnPropertyTrait;
use ERP\Core\Products\Properties\PurchaseCgstTrait;
use ERP\Core\Products\Properties\PurchaseSgstTrait;
use ERP\Core\Products\Properties\PurchaseIgstTrait;

use ERP\Core\Products\Properties\HighestMeasurementUnitIdTrait;
use ERP\Core\Products\Properties\HigherMeasurementUnitIdTrait;
use ERP\Core\Products\Properties\MediumMeasurementUnitIdTrait;
use ERP\Core\Products\Properties\MediumLowerMeasurementUnitIdTrait;
use ERP\Core\Products\Properties\LowerMeasurementUnitIdTrait;
use ERP\Core\Products\Properties\PrimaryMeasureUnitTrait;

use ERP\Core\Products\Properties\HighestPurchasePricePropertyTrait;
use ERP\Core\Products\Properties\HigherPurchasePriceTrait;
use ERP\Core\Products\Properties\MediumPurchasePriceTrait;
use ERP\Core\Products\Properties\MediumLowerPurchasePriceTrait;
use ERP\Core\Products\Properties\LowerPurchasePriceTrait;

use ERP\Core\Products\Properties\QuantityWisePricingTrait;

use ERP\Core\Products\Properties\HigherUnitQtyTrait;
use ERP\Core\Products\Properties\HighestUnitQtyTrait;
use ERP\Core\Products\Properties\MediumUnitQtyTrait;
use ERP\Core\Products\Properties\MediumLowerUnitQtyTrait;
use ERP\Core\Products\Properties\LowerUnitQtyTrait;
use ERP\Core\Products\Properties\LowestUnitQtyTrait;


use ERP\Core\Products\Properties\HighestMouConvTrait;
use ERP\Core\Products\Properties\HigherMouConvTrait;
use ERP\Core\Products\Properties\MediumMouConvTrait;
use ERP\Core\Products\Properties\MediumLowerMouConvTrait;
use ERP\Core\Products\Properties\LowerMouConvTrait;
use ERP\Core\Products\Properties\LowestMouConvTrait;


use ERP\Core\Products\Properties\ProductTypeTrait;
use ERP\Core\Products\Properties\ProductMenuTrait;
use ERP\Core\Products\Properties\NotForSaleTrait;
use ERP\Core\Products\Properties\MaxSaleQtyTrait;
use ERP\Core\Products\Properties\BestBeforeTimeTrait;
use ERP\Core\Products\Properties\BestBeforeTypeTrait;
use ERP\Core\Products\Properties\CessFlatTrait;
use ERP\Core\Products\Properties\CessPercentageTrait;
use ERP\Core\Products\Properties\OpeningTrait;
use ERP\Core\Products\Properties\RemarkTrait;
use ERP\Core\Products\Properties\CreatedByTrait;
use ERP\Core\Products\Properties\UpdatedByTrait;

use ERP\Core\Products\Properties\WebIntegrationTrait;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class productPersistable
{
	use ProductTypeTrait;
	use ProductMenuTrait;
	use NotForSaleTrait;
	use MaxSaleQtyTrait;
	use BestBeforeTimeTrait;
	use BestBeforeTypeTrait;
	use CessFlatTrait;
	use CessPercentageTrait;
	use OpeningTrait;
	use RemarkTrait;

    use NamePropertyTrait;
	use IsDisplayPropertyTrait;
	use CompanyIdPropertyTrait;
	use ProductIdPropertyTrait;
	use ProductNamePropertyTrait;
	use IdPropertyTrait;
	use ProductGrpIdPropertyTrait;
	use BranchIdPropertyTrait;
	use MeasureUnitPropertyTrait;
	use KeyPropertyTrait;
	use ProductCatIdPropertyTrait;
	use TransactionDatePropertyTrait;
	use DiscountPropertyTrait;
	use DiscountTypePropertyTrait;
	use PricePropertyTrait;
	use QtyPropertyTrait;
	use TransactionTypePropertyTrait;
	use InvoiceNumberPropertyTrait;
	use BillNumberPropertyTrait;
	use TaxPropertyTrait;
	use JfIdPropertyTrait;
	use PurchasePricePropertyTrait;
	use WholeSaleMarginPropertyTrait;
	use SemiWholeSaleMarginPropertyTrait;
	use VatPropertyTrait;
	use MrpPropertyTrait;
	use MarginPropertyTrait;
	use DiscountValuePropertyTrait;
	use FromDatePropertyTrait;
	use ToDatePropertyTrait;
	use AdditionalTaxPropertyTrait;
	use ProductDescriptionPropertyTrait;
	use ColorPropertyTrait;
	use SizePropertyTrait;
	use VariantPropertyTrait;
	use ProductCodePropertyTrait;
	use WholeSaleMarginFlatPropertyTrait;
	use MarginFlatPropertyTrait;
	use StockLevelPropertyTrait;
	use IgstPropertyTrait;
	use TaxInclusiveTrait;
	use HsnPropertyTrait;
	use PurchaseCgstTrait;
	use PurchaseSgstTrait;
	use PurchaseIgstTrait;

	use HighestMeasurementUnitIdTrait;
	use HigherMeasurementUnitIdTrait;
	use MediumMeasurementUnitIdTrait;
	use MediumLowerMeasurementUnitIdTrait;
	use LowerMeasurementUnitIdTrait;
	use PrimaryMeasureUnitTrait;

	use HighestPurchasePricePropertyTrait;
	use HigherPurchasePriceTrait;
	use MediumPurchasePriceTrait;
	use MediumLowerPurchasePriceTrait;
	use LowerPurchasePriceTrait;

	use HighestMouConvTrait;
	use HigherMouConvTrait;
	use MediumMouConvTrait;
	use MediumLowerMouConvTrait;
	use LowerMouConvTrait;
	use LowestMouConvTrait;

	use QuantityWisePricingTrait;
	
	use HighestUnitQtyTrait;
	use HigherUnitQtyTrait;
	use MediumUnitQtyTrait;
	use MediumLowerUnitQtyTrait;
	use LowerUnitQtyTrait;
	use LowestUnitQtyTrait;

	use WebIntegrationTrait;
	
	use CreatedByTrait;
	use UpdatedByTrait;
}