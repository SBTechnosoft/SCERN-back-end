<?php
namespace ERP\Api\V1_0\Reports\ReportBuilder\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Reports\ReportBuilder\Services\ReportBuilderService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
/**
 * @author Hiren Faldu<hiren.f@siliconbrain.in>
 */
class ReportBuilderController extends BaseController implements ContainerInterface
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
	 * @param (no param)
	 * @return array-data/exception message
	 * method calls the model and get the data
	*/
	public function getReportBuilderGroups(Request $request)
	{
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$reportBuilder = new ReportBuilderService();
			return $reportBuilder->getReportBuilderGroups();
		}
		
		return $authenticationResult;
	}

	/**
	 * get the specified resource 
	 * @param $groupId
	 * @return array-data/exception message
	 * method calls the model and get the data
	*/
	public function getTablesByGroup(Request $request, $groupId)
	{
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		//get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$reportBuilder = new ReportBuilderService();
			return $reportBuilder->getTablesByGroup($groupId);
		}
		
		return $authenticationResult;
	}
}