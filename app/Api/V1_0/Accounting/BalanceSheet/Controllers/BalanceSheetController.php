<?php
namespace ERP\Api\V1_0\Accounting\BalanceSheet\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Accounting\BalanceSheet\Services\BalanceSheetService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Accounting\BalanceSheet\BalanceSheetModel;
use ERP\Core\Accounting\BalanceSheet\Entities\BalanceSheetOperation;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BalanceSheetController extends BaseController implements ContainerInterface
{
	/**
	 * get and invoke method is of ContainerInterface method
	 */		
    public function get($id,$name)
	{
		// echo "get";
	}
	public function invoke(callable $method)
	{
		// echo "invoke";
	}
	
	/**
	 * get the specified resource 
	 * @param  companyId
	 * method calls the model and get the data
	*/
    public function getBalanceSheetData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$balanceSheet = new BalanceSheetService();
			$result = $balanceSheet->getData($companyId);
			return $result;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
	/**
	 * get the specified resource 
	 * @param  companyId
	 * method calls the model and get the data
	*/
    public function getDocumentpath(Request $request,$companyId)
    {
		$balanceData = $this->getBalanceSheetData($request,$companyId);
		if(is_array(json_decode($balanceData)))
		{
			$balanceSheetOperation = new BalanceSheetOperation();
			if(strcmp($request->header()['operation'][0],'twoSidePdf')==0)
			{
				$generatedPath = $balanceSheetOperation->generateTwoSidePdf($balanceData);
			}
			else if(strcmp($request->header()['operation'][0],'pdf')==0)
			{
				$generatedPath = $balanceSheetOperation->generatePdf($balanceData);
			}
			else if(strcmp($request->header()['operation'][0],'twoSideExcel')==0)
			{
				$generatedPath = $balanceSheetOperation->generateTwoSideExcel($balanceData);
			}
			else
			{
				$generatedPath = $balanceSheetOperation->generateExcel($balanceData);
			}
			return $generatedPath;
		}
		else
		{
			return $balanceData;
		}
	}
}
