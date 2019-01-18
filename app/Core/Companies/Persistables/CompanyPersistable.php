<?php
namespace ERP\Core\Companies\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Companies\Properties\CompanyDispNamePropertyTrait;
use ERP\Core\Companies\Properties\CompanyNamePropertyTrait;
use ERP\Core\Companies\Properties\WebsiteNameTrait;
use ERP\Core\Properties\Address1PropertyTrait;
use ERP\Core\Properties\PincodePropertyTrait;
use ERP\Core\Properties\Address2PropertyTrait;
use ERP\Core\Companies\Properties\PanPropertyTrait;
use ERP\Core\Companies\Properties\TinPropertyTrait;
use ERP\Core\Companies\Properties\VatNoPropertyTrait;
use ERP\Core\Companies\Properties\ServiceTaxNoPropertyTrait;
use ERP\Core\Companies\Properties\BasicCurrencySymbolPropertyTrait;
use ERP\Core\Companies\Properties\FormalNamePropertyTrait;
use ERP\Core\Companies\Properties\NoOfDecimalPointsPropertyTrait;
use ERP\Core\Companies\Properties\CurrencySymbolPropertyTrait;
use ERP\Core\Companies\Properties\SgstPropertyTrait;
use ERP\Core\Companies\Properties\CgstPropertyTrait;
use ERP\Core\Companies\Properties\EmailIdPropertyTrait;
use ERP\Core\Companies\Properties\CustomerCarePropertyTrait;
use ERP\Core\Companies\Properties\CessPropertyTrait;
use ERP\Core\Companies\Properties\PrintTypePropertyTrait;
use ERP\Core\Shared\Properties\IsDisplayPropertyTrait;
use ERP\Core\Properties\IsDefaultPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Cities\Properties\CityIdPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CompanyPersistable
{
    use NamePropertyTrait;
    use CompanyIdPropertyTrait;
    use CompanyDispNamePropertyTrait;
    use Address1PropertyTrait;
    use PincodePropertyTrait;
    use Address2PropertyTrait;
    use PanPropertyTrait;
    use TinPropertyTrait;
    use VatNoPropertyTrait;
    use ServiceTaxNoPropertyTrait;
    use BasicCurrencySymbolPropertyTrait;
    use FormalNamePropertyTrait;
    use NoOfDecimalPointsPropertyTrait;
    use CurrencySymbolPropertyTrait;
    use IsDisplayPropertyTrait;
    use IsDefaultPropertyTrait;
    use StateAbbPropertyTrait;
    use IdPropertyTrait;
	use KeyPropertyTrait;
	use CompanyNamePropertyTrait;
	use CityIdPropertyTrait;
	use SgstPropertyTrait;
	use CgstPropertyTrait;
	use EmailIdPropertyTrait;
	use CustomerCarePropertyTrait;
	use CessPropertyTrait;
	use PrintTypePropertyTrait;
	use WebsiteNameTrait;
}