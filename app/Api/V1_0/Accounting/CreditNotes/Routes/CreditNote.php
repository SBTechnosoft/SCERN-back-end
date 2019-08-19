<?php
namespace ERP\Api\V1_0\Accounting\CreditNotes\Routes;

use ERP\Api\V1_0\Accounting\CreditNotes\Controllers\CreditNoteController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class CreditNote implements RouteRegistrarInterface
{
	/**
	 * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
	 */
	public function register(RegistrarInterface $Registrar)
	{
		// insert data post request
		Route::post('Accounting/CreditNotes/CreditNote/{saleId}', 'Accounting\CreditNotes\Controllers\CreditNoteController@store');

	}
}