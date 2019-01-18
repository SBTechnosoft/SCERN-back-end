<?php
namespace ERP\Api\V1_0\Cities\Controllers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use ERP\Core\Cities\Services\CityService;
use ERP\Http\Requests;
use ERP\Api\V1_0\Support\BaseController;
use ERP\Api\V1_0\Cities\Processors\CityProcessor;
use ERP\Core\Cities\Persistables\CityPersistable;
use ERP\Core\Support\Service\ContainerInterface;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Cities\CityModel;
use ERP\Entities\AuthenticationClass\TokenAuthentication;
use ERP\Entities\Constants\ConstantClass;
use DB;
use Carbon;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class CityController extends BaseController implements ContainerInterface
{
	/**
     * @var cityService
     * @var processor
     * @var request
     * @var cityPersistable
     */
	private $cityService;
	private $processor;
	private $request;
	private $cityPersistable;	
	
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
	 * insert the specified resource 
	 * @param  Request object[Request $request]
	 * method calls the processor for creating persistable object & setting the data
	*/
    public function store(Request $request)
    {
    	//script for change ledgerId_Dtl
		// $mytime = Carbon\Carbon::now();
		// echo "enterrrrdd";
		// DB::beginTransaction();
		// $ledgerDtl = DB::select("SELECT * from ledger_mst where deleted_at='0000-00-00 00:00:00'");
		// DB::commit();
		// for($index=0;$index<count($ledgerDtl);$index++)
		// {
		// 	DB::beginTransaction();
		// 	$result = DB::statement("ALTER TABLE ".$ledgerDtl[$index]->ledger_id."_ledger_dtl CHANGE `created_at` `created_at` DATETIME NOT NULL;");
		// 	DB::commit();
			
		// 	if($result!=1)
		// 	{
		// 		print_r($index);
		// 		echo "\n";
		// 	}
		// }
		echo "endd";
		exit;
		
		// for($)
		
		echo "aaa";
		DB::beginTransaction();
		$clientData1 = DB::select("SELECT * from client_mst");
		DB::commit();
		
		DB::beginTransaction();
		$ledgerData1 = DB::select("SELECT * from ledger_mst");
		DB::commit();
		for($clientData=0;$clientData<count($clientData1);$clientData++)
		{
			for($ledgerData=0;$ledgerData<count($ledgerData1);$ledgerData++)
			{
				if(strcmp($clientData1[$clientData]->contact_no,$ledgerData1[$ledgerData]->contact_no)==0)
				{
					DB::beginTransaction();
					$raw = DB::statement("update ledger_mst set client_id ='".$clientData1[$clientData]->client_id."',
					client_name='".$clientData1[$clientData]->client_name."' 
					where contact_no='".$ledgerData1[$ledgerData]->contact_no."'");
					DB::commit();
					if($raw!=1)
					{
						echo $clientData;
						echo  " = ";
						echo $ledgerData;
						exit;
					}
					else
					{
						break;
					}
				}
			}
		}
		// print_r($clientData);
		echo "hhh";
		// print_r($ledgerData);
		//echo "jji";	
		// print_r($raw);
		// $raw = DB::statement("update client_mst set client_name ='abcfghrf' where client_id=4");
				
		exit;
		
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			// check the requested Http method
			$requestMethod = $_SERVER['REQUEST_METHOD'];
			// insert
			if($requestMethod == 'POST')
			{
				$processor = new Cityprocessor();
				$cityPersistable = new CityPersistable();		
				$cityService= new CityService();			
				$cityPersistable = $processor->createPersistable($this->request);
				if($cityPersistable[0][0]=='[')
				{
					return $cityPersistable;
				}
				else if(is_array($cityPersistable))
				{
					$status = $cityService->insert($cityPersistable);
					return $status;
				}
				else
				{
					return $cityPersistable;
				}
			}
			else
			{
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
     * @param  int  $cityId
     */
    public function getData(Request $request,$cityId=null)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			if($cityId==null)
			{			
				$cityService= new CityService();
				$status = $cityService->getAllCityData();
				return $status;
			}
			else
			{	
				$cityService= new CityService();
				$status = $cityService->getCityData($cityId);
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
     * @param  int  $stateAbb
     */
    public function getAllData(Request $request,$stateAbb)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$cityService= new CityService();
			$status = $cityService->getAllData($stateAbb);
			return $status;
		}
		else
		{
			return $authenticationResult;
		}
	}
	
    /**
     * Update the specified resource in storage.
     * @param  Request object[Request $request]
     * @param  city_id
     * @return status
     */
	public function update(Request $request,$cityId)
    {    
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			$processor = new CityProcessor();
			$cityPersistable = new CityPersistable();		
			$cityService= new CityService();			
			
			//get exception message
			$exception = new ExceptionMessage();
			$exceptionArray = $exception->messageArrays();
			
			$cityModel = new CityModel();
			$result = $cityModel->getData($cityId);
			if(strcmp($result,$exceptionArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$cityPersistable = $processor->createPersistableChange($this->request,$cityId);
				if(is_array($cityPersistable))
				{
					$status = $cityService->update($cityPersistable);
					return $status;
				}
				else
				{
					return $cityPersistable;
				}
			}
		}
		else
		{
			return $authenticationResult;
		}
	}
	
    /**
     * Remove the specified resource from storage.
     * @param  Request object[Request $request]     
     * @param  city_id
     * @return status     
     */
    public function Destroy(Request $request,$cityId)
    {
		//Authentication
		$tokenAuthentication = new TokenAuthentication();
		$authenticationResult = $tokenAuthentication->authenticate($request->header());
		
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		if(strcmp($constantArray['success'],$authenticationResult)==0)
		{
			$this->request = $request;
			$processor = new CityProcessor();
			$cityPersistable = new CityPersistable();		
			$cityService= new CityService();			
			//get exception message
			$exception = new ExceptionMessage();
			$fileSizeArray = $exception->messageArrays();
			
			$cityModel = new CityModel();
			$result = $cityModel->getData($cityId);
			if(strcmp($result,$fileSizeArray['404'])==0)
			{
				return $result;
			}
			else
			{
				$cityPersistable = $processor->createPersistableChange($this->request,$cityId);
				$status = $cityService->delete($cityPersistable);
				return $status;
			}
		}
		else
		{
			return $authenticationResult;
		}
    }
}