<?php
namespace ERP\Core\Accounting\Quotations\Persistables;

use ERP\Core\Accounting\Bills\Properties\ProductArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TotalPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TaxPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\GrandTotalPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\RemarkPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\EntryDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\ClientIdPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Branches\Properties\BranchIdPropertyTrait;
// use ERP\Core\Accounting\Bills\Properties\FromDatePropertyTrait;
// use ERP\Core\Accounting\Bills\Properties\ToDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\JfIdPropertyTrait;
// use ERP\Core\Accounting\Bills\Properties\BillArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\KeyArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\NameArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\SaleIdArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\ExtraChargePropertyTrait;
use ERP\Core\Products\Properties\ProductNamePropertyTrait;
use ERP\Core\Products\Properties\MeasureUnitPropertyTrait;
use ERP\Core\Products\Properties\ColorPropertyTrait;
use ERP\Core\Products\Properties\SizePropertyTrait;
use ERP\Core\Accounting\Quotations\Properties\QuotationNumberPropertyTrait;
use ERP\Core\Accounting\Quotations\Properties\QuotationIdPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TotalDiscounttypeTrait;
use ERP\Core\Accounting\Bills\Properties\TotalDiscountTrait;
use ERP\Core\Accounting\Bills\Properties\TotalCgstPercentageTrait;
use ERP\Core\Accounting\Bills\Properties\TotalSgstPercentageTrait;
use ERP\Core\Accounting\Bills\Properties\TotalIgstPercentageTrait;
use ERP\Core\Accounting\Bills\Properties\PaymentModePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\PoNumberTrait;
use ERP\Core\Accounting\Bills\Properties\InvoiceNumberPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\CheckNumberPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\BankNamePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\BalancePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\AdvancePropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationPersistable
{
	use ProductArrayPropertyTrait;
    use QuotationNumberPropertyTrait;
    use TotalPropertyTrait;
	use TaxPropertyTrait;
    use GrandTotalPropertyTrait;
	use RemarkPropertyTrait;
	use EntryDatePropertyTrait;
	use CompanyIdPropertyTrait;
	use BranchIdPropertyTrait;
	use ClientIdPropertyTrait;
	// use FromDatePropertyTrait;
	// use ToDatePropertyTrait;
	use JfIdPropertyTrait;
	// use BillArrayPropertyTrait;
	use KeyArrayPropertyTrait;
	use NameArrayPropertyTrait;
	use SaleIdArrayPropertyTrait;
	use ExtraChargePropertyTrait;
	use QuotationIdPropertyTrait;
	use ProductNamePropertyTrait;
	use MeasureUnitPropertyTrait;
	use ColorPropertyTrait;
	use SizePropertyTrait;
	use TotalDiscountTrait;
	use TotalDiscounttypeTrait;
	use TotalCgstPercentageTrait;
	use TotalSgstPercentageTrait;
	use TotalIgstPercentageTrait;
	use PaymentModePropertyTrait;
	use PoNumberTrait;
	use InvoiceNumberPropertyTrait;
	use BankNamePropertyTrait;
	use CheckNumberPropertyTrait;
	use BalancePropertyTrait;
	use AdvancePropertyTrait;
}