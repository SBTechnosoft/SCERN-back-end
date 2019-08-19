<?php
namespace ERP\Core\Accounting\DebitNotes\Persistables;

use ERP\Core\Accounting\PurchaseBills\Properties\PurchaseIdArrayPropertyTrait;
use ERP\Core\Accounting\PurchaseBills\Properties\BillNumberTrait;
use ERP\Core\Accounting\Bills\Properties\TotalPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\RemarkPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\EntryDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\JfIdPropertyTrait;
use ERP\Core\Accounting\DebitNotes\Properties\DebitArrayPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class DebitNotePersistable
{
	use PurchaseIdArrayPropertyTrait;
	use BillNumberTrait;
	use TotalPropertyTrait;
	use RemarkPropertyTrait;
	use EntryDatePropertyTrait;
	use JfIdPropertyTrait;
	use DebitArrayPropertyTrait;
	use CompanyIdPropertyTrait;
}