<?php
namespace ERP\Api\V1_0\Logout\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Model\Logout\LogoutModel;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Support\Service\ContainerInterface;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class LogoutController extends BaseController implements ContainerInterface
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
     * Remove the specified resource from storage.
     * @param  Request object[Request $request]     
     * @param  state_abb     
     */
    public function Destroy($userId)
    {
		$logoutModel = new LogoutModel();
		$deletedResult = $logoutModel->deleteData($userId);
		return $deletedResult;
    }
}
