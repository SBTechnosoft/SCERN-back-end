<?php
namespace ERP\Core\Accounting\Journals\Entities;

use ERP\Core\Shared\Properties\CreatedAtPropertyTrait;
use ERP\Core\Shared\Properties\UpdatedAtPropertyTrait;
use ERP\Core\Accounting\Journals\Properties\EntryDatePropertyTrait;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Journal
{
	use CreatedAtPropertyTrait;
    use UpdatedAtPropertyTrait;
    use EntryDatePropertyTrait;
}