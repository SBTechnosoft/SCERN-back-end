<?php
namespace ERP\Api\V1_0\Banks\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Banks\Services\BankService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
// use ERP\Core\Banks\Persistables\BankPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class BankController extends BaseController implements ContainerInterface
{
	/**
     * @var bankService
     * @var request
     */
	private $bankService;
	private $request;
		
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
     * get the specified resource.
     * @param  int  $bankId
     */
    public function getData(Request $request,$bankId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());

		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$bankService= new BankService();
			if($bankId==null)
			{	
				$status = $bankService->getAllBankData();
				return $status;
			}
			else
			{	
				$status = $bankService->getBankData($bankId);
				return $status;
			}    
		}
		else
		{
			return $authenticationResult;
		}		
    }
	
	/**
     * get the specified resource.
     * @param  int  $bankId
     */
    public function getBranchData(Request $request,$bankId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$bankService= new BankService();
			if($bankId==null)
			{	
				$status = $bankService->getAllBranchData();
				return $status;
			}
			else
			{	
				$status = $bankService->getBranchData($bankId);
				return $status;
			}    
		}
		else
		{
			return $authenticationResult;
		}	
    }
}
