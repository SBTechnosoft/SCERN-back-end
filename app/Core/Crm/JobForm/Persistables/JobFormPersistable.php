<?php
namespace ERP\Core\Crm\JobForm\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ClientNamePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ContactNoPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\EmailIdPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\JobCardNoPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ProductInformationPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\TaxPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\QtyPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\DiscountTypePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\DiscountPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\AdditionalTaxPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\PricePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\LabourChargePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ServiceTypePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\EntryDatePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\DeliveryDatePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\AdvancePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\TotalPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\PaymentModePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ProductIdPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\CompanyIdPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\AddressPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ProductNamePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\BankNamePropertyTrait;
use ERP\Core\Crm\JobForm\Properties\chequeNoPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ClientIdPropertyTrait;
use ERP\Core\States\Properties\StateAbbPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Cities\Properties\CityIdPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormPersistable
{
    use NamePropertyTrait;
    use ClientNamePropertyTrait;
    use ContactNoPropertyTrait;
    use EmailIdPropertyTrait;
    use JobCardNoPropertyTrait;
    use ProductInformationPropertyTrait;
    use TaxPropertyTrait;
    use QtyPropertyTrait;
    use DiscountTypePropertyTrait;
    use DiscountPropertyTrait;
    use AdditionalTaxPropertyTrait;
	use PricePropertyTrait;
	use LabourChargePropertyTrait;
	use ServiceTypePropertyTrait;
	use EntryDatePropertyTrait;
	use DeliveryDatePropertyTrait;
	use AdvancePropertyTrait;
	use TotalPropertyTrait;
	use PaymentModePropertyTrait;
	use ProductIdPropertyTrait;
	use CompanyIdPropertyTrait;
	use AddressPropertyTrait;
	use StateAbbPropertyTrait;
	use IdPropertyTrait;
	use KeyPropertyTrait;
	use CityIdPropertyTrait;
	use ProductNamePropertyTrait;
	use BankNamePropertyTrait;
	use chequeNoPropertyTrait;
	use ClientIdPropertyTrait;
}