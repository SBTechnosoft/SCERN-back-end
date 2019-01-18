<?php
namespace ERP\Core\Accounting\Journals\Persistables;

use ERP\Core\Accounting\Journals\Properties\JfIdPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Accounting\Journals\Properties\EntryDatePropertyTrait;
use ERP\Core\Accounting\Journals\Properties\AmountPropertyTrait;
use ERP\Core\Accounting\Journals\Properties\AmountTypePropertyTrait;
use ERP\Core\Accounting\Ledgers\Properties\LedgerIdPropertyTrait;
use ERP\Core\Accounting\Journals\Properties\FromDatePropertyTrait;
use ERP\Core\Accounting\Journals\Properties\ToDatePropertyTrait;
use ERP\Core\Accounting\Journals\Properties\JournalIdPropertyTrait;
use ERP\Core\Accounting\Journals\Properties\JournalTypePropertyTrait;
use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JournalPersistable
{
	use JfIdPropertyTrait;
	use CompanyIdPropertyTrait;
    use EntryDatePropertyTrait;
    use AmountPropertyTrait;
    use AmountTypePropertyTrait;
    use LedgerIdPropertyTrait;
    use FromDatePropertyTrait;
    use ToDatePropertyTrait;
    use NamePropertyTrait;
    use KeyPropertyTrait;
    use JournalIdPropertyTrait;
    use JournalTypePropertyTrait;
}