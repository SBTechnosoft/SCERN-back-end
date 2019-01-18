<?php
namespace ERP\Api\V1_0\Accounting\Ledgers\Routes;

use ERP\Api\V1_0\Accounting\Ledgers\Controllers\LedgerController;
use ERP\Support\Interfaces\RouteRegistrarInterface;
use Illuminate\Contracts\Routing\Registrar as RegistrarInterface;
use Illuminate\Support\Facades\Route;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Ledger implements RouteRegistrarInterface
{
    /**
     * @param RegistrarInterface $registrar
	 * description : this function is going to the controller page
     */
    public function register(RegistrarInterface $Registrar)
    {
		// all the possible get request 
		Route::group(['as' => 'get'], function ()
		{
			Route::get('Accounting/Ledgers/Ledger/{ledgerId?}', 'Accounting\Ledgers\Controllers\LedgerController@getData');
			Route::get('Accounting/Ledgers/Ledger/ledgerGrp/{ledgerGrpId}', 'Accounting\Ledgers\Controllers\LedgerController@getAllData');
			Route::get('Accounting/Ledgers/Ledger/company/{companyId}', 'Accounting\Ledgers\Controllers\LedgerController@getLedgerData');
			Route::get('Accounting/Ledgers/Ledger/{ledgerId}/transactions', 'Accounting\Ledgers\Controllers\LedgerController@getLedgerTransactionData');
		});
		// insert data post request
		Route::post('Accounting/Ledgers/Ledger', 'Accounting\Ledgers\Controllers\LedgerController@store');
	
		// update data post request
		Route::post('Accounting/Ledgers/Ledger/{ledgerId}', 'Accounting\Ledgers\Controllers\LedgerController@update');
	
		// delete data post request
		Route::delete('Accounting/Ledgers/Ledger/{ledgerId}', 'Accounting\Ledgers\Controllers\LedgerController@destroy');
	}
}


