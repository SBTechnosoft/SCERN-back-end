<?php
namespace ERP\Core\Crm\Conversations\Persistables;

use ERP\Core\Shared\Properties\NamePropertyTrait;
use ERP\Core\Shared\Properties\KeyPropertyTrait;
use ERP\Core\Shared\Properties\IdPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ContactNoPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\EmailIdPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\CompanyIdPropertyTrait;
use ERP\Core\Branches\Properties\BranchIdPropertyTrait;
use ERP\Core\Crm\JobForm\Properties\ClientIdPropertyTrait;
use ERP\Core\Crm\Conversations\Properties\ConversationTrait;
use ERP\Core\Crm\Conversations\Properties\SubjectTrait;
use ERP\Core\Crm\Conversations\Properties\CcEmailIdTrait;
use ERP\Core\Crm\Conversations\Properties\BccEmailIdTrait;
use ERP\Core\Crm\Conversations\Properties\ConversationTypeTrait;
use ERP\Core\Crm\Conversations\Properties\CommentTrait;
use ERP\Core\Crm\Conversations\Properties\UserIdTrait;

/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ConversationPersistable
{
    use NamePropertyTrait;
	use KeyPropertyTrait;
	use IdPropertyTrait;
    use ContactNoPropertyTrait;
    use EmailIdPropertyTrait;
	use CompanyIdPropertyTrait;
	use BranchIdPropertyTrait;
	use ClientIdPropertyTrait;
	use ConversationTrait;
	use SubjectTrait;
	use CcEmailIdTrait;
	use BccEmailIdTrait;
	use ConversationTypeTrait;
	use CommentTrait;
	use UserIdTrait;
}