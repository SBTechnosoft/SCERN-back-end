<?php
namespace ERP\Core\Branches\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Branches\Properties\BranchIdPropertyTrait;
use ERP\Core\Branches\Properties\BranchNamePropertyTrait;
use ERP\Core\Properties\Address1PropertyTrait;
use ERP\Core\Properties\PincodePropertyTrait;
use ERP\Core\Properties\Address2PropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\Properties\IsDefaultPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Cities\Properties\CityIdPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BranchPersistable
{
    use NamePropertyTrait;
    use BranchNamePropertyTrait;
    use BranchIdPropertyTrait;
    use Address1PropertyTrait;
    use PincodePropertyTrait;
    use Address2PropertyTrait;
    use IsDisplayPropertyTrait;
    use IsDefaultPropertyTrait;
    use StateAbbPropertyTrait;
    use IdPropertyTrait;
    use CompanyIdPropertyTrait;
	use KeyPropertyTrait;
	use CityIdPropertyTrait;
}