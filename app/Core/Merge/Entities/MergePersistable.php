<?php
namespace ERP\Core\Merge\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class MergePersistable 
{
    use NamePropertyTrait;
    use IdPropertyTrait;
	use KeyPropertyTrait;
}