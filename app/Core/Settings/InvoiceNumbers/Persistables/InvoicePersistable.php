<?php
namespace ERP\Core\Settings\InvoiceNumbers\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Settings\InvoiceNumbers\Properties\InvoiceIdPropertyTrait;
use ERP\Core\Settings\InvoiceNumbers\Properties\InvoiceLabelPropertyTrait;
use ERP\Core\Settings\InvoiceNumbers\Properties\InvoiceTypePropertyTrait;
use ERP\Core\Settings\InvoiceNumbers\Properties\StartAtPropertyTrait;
use ERP\Core\Settings\InvoiceNumbers\Properties\EndAtPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class InvoicePersistable
{
    use NamePropertyTrait;
    use InvoiceIdPropertyTrait;
    use InvoiceLabelPropertyTrait;
    use InvoiceTypePropertyTrait;
    use StartAtPropertyTrait;
    use EndAtPropertyTrait;
    use CompanyIdPropertyTrait;
	use KeyPropertyTrait;
	
}