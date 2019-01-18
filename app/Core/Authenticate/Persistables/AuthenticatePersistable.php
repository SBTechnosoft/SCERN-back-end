<?php
namespace ERP\Core\Authenticate\Persistables;

use ERP\Core\Authenticate\Properties\UserIdPropertyTrait;
use ERP\Core\Authenticate\Properties\TokenPropertyTrait;
use ERP\Core\Authenticate\Properties\WebTokenPropertyTrait;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class AuthenticatePersistable
{
    use UserIdPropertyTrait;
    use TokenPropertyTrait;
    use WebTokenPropertyTrait;
}