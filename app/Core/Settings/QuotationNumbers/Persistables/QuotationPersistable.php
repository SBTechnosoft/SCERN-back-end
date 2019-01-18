<?php
namespace ERP\Core\Settings\QuotationNumbers\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Settings\QuotationNumbers\Properties\QuotationIdPropertyTrait;
use ERP\Core\Settings\QuotationNumbers\Properties\QuotationLabelPropertyTrait;
use ERP\Core\Settings\QuotationNumbers\Properties\QuotationTypePropertyTrait;
use ERP\Core\Settings\QuotationNumbers\Properties\StartAtPropertyTrait;
use ERP\Core\Settings\QuotationNumbers\Properties\EndAtPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class QuotationPersistable
{
    use NamePropertyTrait;
    use QuotationIdPropertyTrait;
    use QuotationLabelPropertyTrait;
    use QuotationTypePropertyTrait;
    use StartAtPropertyTrait;
    use EndAtPropertyTrait;
    use CompanyIdPropertyTrait;
	use KeyPropertyTrait;
	
}