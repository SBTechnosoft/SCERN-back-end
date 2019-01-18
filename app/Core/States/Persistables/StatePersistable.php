<?php
namespace ERP\Core\States\Persistables;

use ERP\Core\States\Properties\StateNamePropertyTrait;
use ERP\Core\States\Properties\StateCodeTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class StatePersistable
{
    use StateNamePropertyTrait;
    use IsDisplayPropertyTrait;
    use StateAbbPropertyTrait;
    use NamePropertyTrait;
    use KeyPropertyTrait;
    use StateCodeTrait;
}