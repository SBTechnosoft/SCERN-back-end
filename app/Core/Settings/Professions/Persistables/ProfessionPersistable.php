<?php
namespace ERP\Core\Settings\Professions\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Settings\Professions\Properties\ProfessionIdPropertyTrait;
use ERP\Core\Settings\Professions\Properties\ProfessionNamePropertyTrait;
use ERP\Core\Settings\Professions\Properties\DescriptionPropertyTrait;
use ERP\Core\Settings\Professions\Properties\ProfessionParentIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
// use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ProfessionPersistable
{
    use NamePropertyTrait;
    use ProfessionNamePropertyTrait;
    use DescriptionPropertyTrait;
    use ProfessionParentIdPropertyTrait;
    use KeyPropertyTrait;
    use ProfessionIdPropertyTrait;
    // use CompanyIdPropertyTrait;
}