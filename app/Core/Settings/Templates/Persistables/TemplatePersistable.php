<?php
namespace ERP\Core\Settings\Templates\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Settings\Templates\Properties\TemplateIdPropertyTrait;
use ERP\Core\Settings\Templates\Properties\TemplateNamePropertyTrait;
use ERP\Core\Settings\Templates\Properties\TemplateTypePropertyTrait;
use ERP\Core\Settings\Templates\Properties\TemplateBodyPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TemplatePersistable
{
    use NamePropertyTrait;
    use TemplateNamePropertyTrait;
    use TemplateIdPropertyTrait;
    use TemplateTypePropertyTrait;
    use TemplateBodyPropertyTrait;
    use KeyPropertyTrait;
    use CompanyIdPropertyTrait;
}