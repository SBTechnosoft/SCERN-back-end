<?php
namespace ERP\Api\V1_0\Crm\Conversations\Routes;

use ERP\Api\V1_0\Crm\Conversations\Controllers\ConversationController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Conversation implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		// insert data post request
		Route::post('Crm/Conversations/Conversation/bulk-email', 'Crm\Conversations\Controllers\ConversationController@storeEmail');
		Route::post('Crm/Conversations/Conversation/bulk-sms', 'Crm\Conversations\Controllers\ConversationController@storeSms');
	}
}


