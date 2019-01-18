<?php
namespace ERP\Core\Products\Entities;

use ERP\Core\Shared\Properties\CreatedAtPropertyTrait;
use ERP\Core\Shared\Properties\UpdatedAtPropertyTrait;
use ERP\Core\Shared\Properties\TransactionDatePropertyTrait;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Product
{
	use CreatedAtPropertyTrait;
    use UpdatedAtPropertyTrait;
    use TransactionDatePropertyTrait;
}