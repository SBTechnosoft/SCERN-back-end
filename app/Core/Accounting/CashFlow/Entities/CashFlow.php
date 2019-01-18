<?php
namespace ERP\Core\Accounting\CashFlow\Entities;

use ERP\Core\Shared\Properties\CreatedAtPropertyTrait;
use ERP\Core\Shared\Properties\UpdatedAtPropertyTrait;
use ERP\Core\Accounting\Journals\Properties\EntryDatePropertyTrait;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CashFlow
{
	use CreatedAtPropertyTrait;
    use UpdatedAtPropertyTrait;
    use EntryDatePropertyTrait;
}