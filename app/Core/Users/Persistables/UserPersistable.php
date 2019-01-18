<?php
namespace ERP\Core\Users\Persistables;

use ERP\Core\Users\Properties\UserNamePropertyTrait;
use ERP\Core\Users\Properties\UserIdPropertyTrait;
use ERP\Core\Accounting\Ledgers\Properties\ContactNoPropertyTrait;
use ERP\Core\Accounting\Ledgers\Properties\EmailIdPropertyTrait;
use ERP\Core\Users\Properties\PasswordPropertyTrait;
use ERP\Core\Users\Properties\AddressPropertyTrait;
use ERP\Core\Users\Properties\UserTypePropertyTrait;
use ERP\Core\Users\Properties\PermissionArrayTrait;
use ERP\Core\Users\Properties\DefaultCompanyIdPropertyTrait;
use ERP\Core\Companies\Properties\PincodePropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Branches\Properties\BranchIdPropertyTrait;
use ERP\Core\Cities\Properties\CityIdPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserPersistable
{
    use UserNamePropertyTrait;
    use UserIdPropertyTrait;
    use ContactNoPropertyTrait;
    use EmailIdPropertyTrait;
    use AddressPropertyTrait;
    use PincodePropertyTrait;
    use CompanyIdPropertyTrait;
    use BranchIdPropertyTrait;
    use CityIdPropertyTrait;
    use StateAbbPropertyTrait;
    use KeyPropertyTrait;
    use NamePropertyTrait;
    use PasswordPropertyTrait;
    use UserTypePropertyTrait;
    use PermissionArrayTrait;
    use DefaultCompanyIdPropertyTrait;
}