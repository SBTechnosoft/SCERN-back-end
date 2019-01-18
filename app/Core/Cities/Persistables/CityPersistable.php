<?php
namespace ERP\Core\Cities\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Cities\Properties\CityIdPropertyTrait;
use ERP\Core\Cities\Properties\CityNamePropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityPersistable 
{
    use NamePropertyTrait;
    use StateAbbPropertyTrait;
    use IsDisplayPropertyTrait;
    use CityNamePropertyTrait;
    use CityIdPropertyTrait;
    use IdPropertyTrait;
	use KeyPropertyTrait;
}