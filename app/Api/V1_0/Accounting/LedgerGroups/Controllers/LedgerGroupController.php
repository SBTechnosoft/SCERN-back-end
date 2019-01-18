<?php
namespace ERP\Api\V1_0\Accounting\LedgerGroups\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Accounting\LedgerGroups\Services\LedgerGroupService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Accounting\LedgerGroups\Persistables\LedgerGroupPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LedgerGroupController extends BaseController implements ContainerInterface
{
	/**
     * @var ledgerGroupService
     * @var request
     * @var ledgerGroupPersistable
     */
	private $ledgerGroupService;
	private $request;
	private $ledgerGroupPersistable;	
	
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
     * @param  int  $ledgerGrpId
     */
    public function getData(Request $request,$ledgerGrpId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$ledgerGrpService= new LedgerGroupService();
			if($ledgerGrpId==null)
			{	
				$status = $ledgerGrpService->getAllLedgerGrpData();
				return $status;
			}
			else
			{	
				$status = $ledgerGrpService->getLedgerGrpData($ledgerGrpId);
				return $status;
			} 
		}	
		else
		{
			return $authenticationResult;
		}
    }
}
