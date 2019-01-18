<?php
namespace ERP\Core\Clients\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Clients\Properties\ClientIdPropertyTrait;
use ERP\Core\Clients\Properties\ClientNamePropertyTrait;
use ERP\Core\Clients\Properties\CompanyNamePropertyTrait;
use ERP\Core\Clients\Properties\ContactNoPropertyTrait;
use ERP\Core\Clients\Properties\ContactNo1PropertyTrait;
use ERP\Core\Clients\Properties\WorkNoPropertyTrait;
use ERP\Core\Clients\Properties\EmailIdPropertyTrait;
use ERP\Core\Clients\Properties\ProfessionIdTrait;
use ERP\Core\Properties\Address1PropertyTrait;
use ERP\Core\Properties\Address2PropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Cities\Properties\CityIdPropertyTrait;
use ERP\Core\Clients\Properties\BirthDateTrait;
use ERP\Core\Clients\Properties\AnniversaryDateTrait;
use ERP\Core\Clients\Properties\OtherDateTrait;
use ERP\Core\Clients\Properties\GstTrait;
use ERP\Core\Clients\Properties\CreditLimitTrait;
use ERP\Core\Clients\Properties\CreditDaysTrait;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ClientPersistable
{
    use NamePropertyTrait;
    use ClientNamePropertyTrait;
    use ClientIdPropertyTrait;
    use Address1PropertyTrait;
	use Address2PropertyTrait;
    use IsDisplayPropertyTrait;
    use StateAbbPropertyTrait;
    use KeyPropertyTrait;
	use CityIdPropertyTrait;
	use CompanyNamePropertyTrait;
	use ContactNoPropertyTrait;
	use ContactNo1PropertyTrait;
	use WorkNoPropertyTrait;
	use EmailIdPropertyTrait;
	use ProfessionIdTrait;
	use BirthDateTrait;
	use AnniversaryDateTrait;
	use OtherDateTrait;
	use GstTrait;
	use CreditLimitTrait;
	use CreditDaysTrait;
}