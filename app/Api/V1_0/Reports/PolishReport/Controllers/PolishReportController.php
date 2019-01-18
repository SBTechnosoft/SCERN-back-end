<?php
namespace ERP\Api\V1_0\Reports\PolishReport\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Reports\PolishReport\Services\PolishReportService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Core\Reports\PolishReport\Entities\PolishReportOperation;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class PolishReportController extends BaseController implements ContainerInterface
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
    public function getPolishReportData(Request $request,$companyId)
    {
		//Authentication
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
			if(array_key_exists('fromdate',$request->header()) && array_key_exists('todate',$request->header()))
			{
				//from-date conversion
				$splitedFromDate = explode("-",$request->header()['fromdate'][0]);
				$transformFromDate = $splitedFromDate[2]."-".$splitedFromDate[1]."-".$splitedFromDate[0];
				//to-date conversion
				$splitedToDate = explode("-",$request->header()['todate'][0]);
				$transformToDate = $splitedToDate[2]."-".$splitedToDate[1]."-".$splitedToDate[0];
				if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$transformFromDate))
				{
					return "from-date is not valid";
				}
				if(!preg_match("/^[0-9]{4}-([1-9]|1[0-2]|0[1-9])-([1-9]|0[1-9]|[1-2][0-9]|3[0-1])$/",$transformToDate))
				{
					return "to-date is not valid";
				}
				$polishReport = new PolishReportService();
				$result = $polishReport->getData($companyId,$transformFromDate,$transformToDate);
				if(array_key_exists('operation',$request->header()))
				{
					//call for pdf designing
					$polishReportOperation = new PolishReportOperation();
					$documentPath = $polishReportOperation->generatePdf($result,$request->header()['fromdate'][0],$request->header()['todate'][0]);
					return $documentPath;
				}
				else
				{
					return $result;
				}
			}
			else
			{
				return $exceptionArray['content'];
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
}
