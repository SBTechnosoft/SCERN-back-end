<?php
namespace ERP\Core\Settings\Expenses\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Settings\Expenses\Properties\ExpenseIdTrait;
use ERP\Core\Settings\Expenses\Properties\ExpenseNameTrait;
use ERP\Core\Settings\Expenses\Properties\ExpenseTypeTrait;
use ERP\Core\Settings\Expenses\Properties\ExpenseValueTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExpensePersistable
{
    use NamePropertyTrait;
    use KeyPropertyTrait;
    use ExpenseIdTrait;
    use ExpenseNameTrait;
    use ExpenseTypeTrait;
    use ExpenseValueTrait;
    use CompanyIdPropertyTrait;
}