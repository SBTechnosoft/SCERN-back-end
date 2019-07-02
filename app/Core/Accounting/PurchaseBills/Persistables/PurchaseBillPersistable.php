<?php
namespace ERP\Core\Accounting\PurchaseBills\Persistables;

use ERP\Core\Accounting\Bills\Properties\EntryDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\FromDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\ToDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\ProductArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\PaymentModePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\BankNamePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\BankLedgerIdPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\CheckNumberPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TotalPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TaxPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\GrandTotalPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\AdvancePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\BalancePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\RemarkPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\JfIdPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\KeyArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\NameArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\ExtraChargePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TotalDiscounttypeTrait;
use ERP\Core\Accounting\Bills\Properties\TotalDiscountTrait;
use ERP\Core\Accounting\Bills\Properties\TotalCgstPercentageTrait;
use ERP\Core\Accounting\Bills\Properties\TotalSgstPercentageTrait;
use ERP\Core\Accounting\Bills\Properties\TotalIgstPercentageTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\TransactionDateTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\VendorIdTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\BillNumberTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\BillTypeTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\TransactionTypeTrait;
// use ERP\Core\Accounting\PurchaseBills\Properties\IsPurchaseOrderTrait;
use ERP\Core\Accounting\Bills\Properties\ExpenseTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\DueDateTrait;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PurchaseBillPersistable
{
	use ProductArrayPropertyTrait;
    use PaymentModePropertyTrait;
    use BankNamePropertyTrait;
    use BankLedgerIdPropertyTrait;
    use CheckNumberPropertyTrait;
    use TotalPropertyTrait;
	use TaxPropertyTrait;
    use GrandTotalPropertyTrait;
    use AdvancePropertyTrait;
	use BalancePropertyTrait;
	use RemarkPropertyTrait;
	use CompanyIdPropertyTrait;
	use JfIdPropertyTrait;
	use KeyArrayPropertyTrait;
	use NameArrayPropertyTrait;
	use ExtraChargePropertyTrait;
	use TotalDiscounttypeTrait;
	use TotalDiscountTrait;
	use TotalCgstPercentageTrait;
	use TotalSgstPercentageTrait;
	use TotalIgstPercentageTrait;
	use TransactionDateTrait;
	use VendorIdTrait;
	use BillNumberTrait;
	use BillTypeTrait;
	use EntryDatePropertyTrait;
	use FromDatePropertyTrait;
	use ToDatePropertyTrait;
	use TransactionTypeTrait;
	// use IsPurchaseOrderTrait;
	use ExpenseTrait;
	use DueDateTrait;
}