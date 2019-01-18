<?php
namespace ERP\Api\V1_0\Users\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Core\Users\Entities\UserTypeEnum;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class UserTransformer
{
   /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
    	$userTypeFlag=0;
		$userName = $request->input('userName'); 
		$userType = $request->input('userType'); 
		$emailId = $request->input('emailId'); 
		$password = $request->input('password'); 
		$contactNo = $request->input('contactNo'); 
		$address = $request->input('address'); 
		$pincode = $request->input('pincode'); 
		$stateAbb = $request->input('stateAbb'); 
		$cityId = $request->input('cityId'); 
		$companyId = $request->input('companyId'); 
		$branchId = $request->input('branchId'); 
		$permissionArray = $request->input('permissionArray'); 
		$defaultCompanyId = $request->input('defaultCompanyId'); 
		
		//trim an input
		$tUserName = trim($userName);
		$tUserType = trim($userType);
		$tEmailId = trim($emailId);
		$tPassword = trim($password);
		$tContactNo = trim($contactNo);
		$tAddress = trim($address);
		$tPincode = trim($pincode);
		$tStateAbb = trim($stateAbb);
		$tCityId = trim($cityId);
		$tCompanyId = trim($companyId);
		$tBranchId = trim($branchId);
		$tPermissionArray = trim($permissionArray);
		$tDefaultCompanyId = trim($defaultCompanyId);
		
		//check enum user type
		$userType = new UserTypeEnum();
		$userArray = $userType->enumArrays();
		foreach ($userArray as $key => $value)
		{
			if(strcmp($value,$tUserType)==0)
			{
				$userTypeFlag=1;
				break;
			}
		}
		if($userTypeFlag==1)
		{
			//convert password into base64_encode
			$encodedPassword = base64_encode($tPassword);
			//make an array
			$data = array();
			$data['user_name'] = $tUserName;
			$data['user_type'] = $tUserType;
			$data['email_id'] = $tEmailId;
			$data['password'] = $encodedPassword;
			$data['contact_no'] = $tContactNo;
			$data['address'] = $tAddress;
			$data['pincode'] = $tPincode;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
			$data['company_id'] = $tCompanyId;
			$data['branch_id'] = $tBranchId;
			$data['permission_array'] = $tPermissionArray;
			$data['default_company_id'] = $tDefaultCompanyId;
			return $data;
		}
		else
		{
			return 1;
		}
	}
	
	/**
     * @param key and value
     * @return array
     */
	public function trimUpdateData()
	{
		$userTypeFlag=0;
		$tUserArray = array();
		$userValue;
		$convertedValue="";
		$keyValue = func_get_arg(0);
		$userEnumArray = array();
		for($asciiChar=0;$asciiChar<strlen($keyValue);$asciiChar++)
		{
			if(ord($keyValue[$asciiChar])<=90 && ord($keyValue[$asciiChar])>=65) 
			{
				$convertedValue1 = "_".chr(ord($keyValue[$asciiChar])+32);
				$convertedValue=$convertedValue.$convertedValue1;
			}
			else
			{
				$convertedValue=$convertedValue.$keyValue[$asciiChar];
			}
		}
		$userValue = func_get_arg(1);
		for($data=0;$data<count($userValue);$data++)
		{
			$tUserArray[$data]= array($convertedValue=> trim($userValue));
			$userEnumArray = array_keys($tUserArray[$data])[0];
		}
		//check enum user type
		$userType = new UserTypeEnum();
		$userArray = $userType->enumArrays();
		if(strcmp($userEnumArray,'user_type')==0)
		{
			foreach ($userArray as $key => $value)
			{
				if(strcmp($tUserArray[0]['user_type'],$value)==0)
				{
					$userTypeFlag=1;
					break;
				}
				else
				{
					$userTypeFlag=2;
				}
			}
		}
		if($userTypeFlag==2)
		{
			return 1;
		}
		else
		{
			if(array_key_exists("password",$tUserArray[0]))
			{
				//convert password into base64_encode
				$tUserArray[0]['password'] = base64_encode($tUserArray[0]['password']);
			}
			// echo "vvv";
			// print_r($tUserArray);
			if(array_key_exists("permission",$tUserArray[0]))
			{
				//convert password into base64_encode
				$tUserArray[0]['permission_array'] = json_encode($tUserArray[0]['permission']);
			}
			return $tUserArray;
		}
	}
}