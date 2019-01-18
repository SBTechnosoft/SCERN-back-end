<?php
namespace ERP\Api\V1_0\Entities;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Banks\Services\BankService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Core\Banks\Persistables\BankPersistable;
use ERP\Core\Support\Service\ContainerInterface;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExceptionMessage 
{
	public function errorMessage()
	{
		$data = array();
		$data['fileSize']="FileNotFoundException: The file is too long";
		$data['fileFormat']="FileNotFoundException: The file formate is not valid";
		return $data;
		
	}
	
}
