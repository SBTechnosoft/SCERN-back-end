<?php
namespace ERP\Core\Accounting\CreditNotes\Persistables;

use ERP\Core\Accounting\Bills\Properties\SaleIdArrayPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\InvoiceNumberPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\TotalPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\RemarkPropertyTrait;
use ERP\Core\Accounting\Bills\Properties\EntryDatePropertyTrait;
use ERP\Core\Accounting\Bills\Properties\JfIdPropertyTrait;
use ERP\Core\Accounting\CreditNotes\Properties\CreditArrayPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CreditNotePersistable
{
	use SaleIdArrayPropertyTrait;
	use InvoiceNumberPropertyTrait;
	use TotalPropertyTrait;
	use RemarkPropertyTrait;
	use EntryDatePropertyTrait;
	use JfIdPropertyTrait;
	use CreditArrayPropertyTrait;
	use CompanyIdPropertyTrait;
}