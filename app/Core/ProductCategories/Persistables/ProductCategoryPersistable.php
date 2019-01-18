<?php
namespace ERP\Core\ProductCategories\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\ProductCategories\Properties\ProductCatNamePropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\ProductCategories\Properties\ProductCatIdPropertyTrait;
use ERP\Core\ProductCategories\Properties\ProductCatDescPropertyTrait;
use ERP\Core\ProductCategories\Properties\ProductParentCatIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductCategoryPersistable
{
    use NamePropertyTrait;
    use IsDisplayPropertyTrait;
    use IdPropertyTrait;
	use ProductCatDescPropertyTrait;
	use ProductParentCatIdPropertyTrait;
	use ProductCatNamePropertyTrait;
	use KeyPropertyTrait;
	use ProductCatIdPropertyTrait;
}