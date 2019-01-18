<?php
namespace ERP\Api\V1_0\Accounting\TrialBalance\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Accounting\TrialBalance\Services\TrialBalanceService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Accounting\TrialBalance\TrialBalanceModel;
use ERP\Core\Accounting\TrialBalance\Entities\TrialBalanceOperation;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TrialBalanceController extends BaseController implements ContainerInterface
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
    public function getTrialBalanceData(Request $request,$companyId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$trialBalance = new TrialBalanceService();
			$result = $trialBalance->getData($companyId);
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
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$trialBalance = new TrialBalanceService();
			$result = $trialBalance->getData($companyId);
			$trialBalanceOperation = new TrialBalanceOperation();
			
			if(strcmp($request->header()['operation'][0],'pdf')==0)
			{
				$generatedPath = $trialBalanceOperation->generatePdf($result);
			}
			else if(strcmp($request->header()['operation'][0],'twoSidePdf')==0)
			{
				$generatedPath = $trialBalanceOperation->generateTwoSidePdf($result);
			}
			else if(strcmp($request->header()['operation'][0],'twoSideExcel')==0)
			{
				$generatedPath = $trialBalanceOperation->generateTwoSideExcel($result);
			}
			else
			{
				$generatedPath = $trialBalanceOperation->generateExcel($result);
			}
			return $generatedPath;
		}
		else
		{
			return $authenticationResult;
		}
	}
}
