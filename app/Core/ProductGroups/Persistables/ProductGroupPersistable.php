<?php
namespace ERP\Core\ProductGroups\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\ProductGroups\Properties\ProductGrpDescPropertyTrait;
use ERP\Core\ProductGroups\Properties\ProductGroupNamePropertyTrait;
use ERP\Core\ProductGroups\Properties\ProductGrpParentIdPropertyTrait;
use ERP\Core\ProductGroups\Properties\ProductGrpIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProductGroupPersistable
{
    use NamePropertyTrait;
    use ProductGroupNamePropertyTrait;
    use IsDisplayPropertyTrait;
    use IdPropertyTrait;
	use ProductGrpDescPropertyTrait;
	use ProductGrpParentIdPropertyTrait;
	use ProductGrpIdPropertyTrait;
	use KeyPropertyTrait;
}