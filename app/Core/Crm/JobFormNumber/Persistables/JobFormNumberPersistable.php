<?php
namespace ERP\Core\Crm\JobFormNumber\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Crm\JobFormNumber\Properties\JobFormNumberIdPropertyTrait;
use ERP\Core\Crm\JobFormNumber\Properties\JobFormNumberLabelPropertyTrait;
use ERP\Core\Crm\JobFormNumber\Properties\JobFormNumberTypePropertyTrait;
use ERP\Core\Crm\JobFormNumber\Properties\StartAtPropertyTrait;
use ERP\Core\Crm\JobFormNumber\Properties\EndAtPropertyTrait;
use ERP\Core\Companies\Properties\CompanyIdPropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class JobFormNumberPersistable
{
    use NamePropertyTrait;
    use JobFormNumberIdPropertyTrait;
    use JobFormNumberLabelPropertyTrait;
    use JobFormNumberTypePropertyTrait;
    use StartAtPropertyTrait;
    use EndAtPropertyTrait;
    use CompanyIdPropertyTrait;
	use KeyPropertyTrait;
}