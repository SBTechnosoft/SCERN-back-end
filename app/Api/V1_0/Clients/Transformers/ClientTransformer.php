<?php
namespace ERP\Api\V1_0\Clients\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
use ERP\Entities\EnumClasses\IsDisplayEnum;
use ERP\Entities\Constants\ConstantClass;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ClientTransformer
{
    /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		$clientDataFlag=0;
		//data get from body
		$clientName = $request->input('clientName'); 
		$companyName = $request->input('companyName'); 
		$contactNo = $request->input('contactNo'); 
		$contactNo1 = $request->input('contactNo1'); 
		$emailId = $request->input('emailId'); 
		$address1 = $request->input('address1'); 
		$professionId = $request->input('professionId'); 
		$isDisplay = $request->input('isDisplay'); 			
		$stateAbb = $request->input('stateAbb'); 			
		$cityId = $request->input('cityId'); 	
		$gst = $request->input('gst'); 	
		$birthDate = array_key_exists('birthDate',$request->input())?$request->input('birthDate'):'0000-00-00'; 			
		$anniversaryDate=array_key_exists('anniversaryDate',$request->input())
		? $request->input('anniversaryDate'):'0000-00-00';
		$otherDate=array_key_exists('otherDate',$request->input())?$request->input('otherDate'):'0000-00-00'; 			
		$creditLimit=array_key_exists('creditLimit',$request->input())?$request->input('creditLimit'):''; 			
		$creditDays=array_key_exists('creditDays',$request->input())?$request->input('creditDays'):''; 			
		//birth-date conversion
		if(strcmp($birthDate,'0000-00-00')!=0)
		{
			$splitedDate = explode("-",trim($birthDate));
			$birthDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		}
		//anniversary-date conversion
		if(strcmp($anniversaryDate,'0000-00-00')!=0)
		{
			$splitedDate = explode("-",trim($anniversaryDate));
			$anniversaryDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		}
		// other-date conversion
		if(strcmp($anniversaryDate,'0000-00-00')!=0)
		{
			$splitedDate = explode("-",trim($otherDate));
			$otherDate = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
		}
		//trim an input
		$tClientName = trim($clientName);
		$tCompanyName = trim($companyName);
		$tContactNo = trim($contactNo);
		$tContactNo1 = trim($contactNo1);
		$tEmailId = trim($emailId);
		$tAddress1 = trim($address1);
		$tGst = trim($gst);
		$tProfessionId = trim($professionId);
		$tIsDisplay = trim($isDisplay);
		$tStateAbb = trim($stateAbb);
		$tCityId = trim($cityId);
		$tCreditLimit = trim($creditLimit);
		$tCreditDays = trim($creditDays);
		//check is_display is exist or not
		for($clientData=0;$clientData<count($request->input());$clientData++)
		{
			if(strcmp(array_keys($request->input())[$clientData],"isDisplay")==0)
			{
				$clientDataFlag=1;
				break;
			}
		}
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if($clientDataFlag==1)
		{
			if($tIsDisplay=="")
			{
				$tIsDisplay=$enumIsDispArray['display'];
			}
			else
			{
				foreach ($enumIsDispArray as $key => $value)
				{
					if(strcmp($value,$tIsDisplay)==0)
					{
						$isDisplayFlag=1;
						break;
					}
					else
					{
						$isDisplayFlag=2;
					}
				}
			}
		}
		else
		{
			$tIsDisplay=$enumIsDispArray['display'];
		}
		if($isDisplayFlag==2)
		{
			return "1";
		}
		else
		{
			//make an array
			$data = array();
			$data['client_name'] = $tClientName;
			$data['company_name'] = $tCompanyName;
			$data['contact_no'] = $tContactNo;
			$data['contact_no1'] = $tContactNo1;
			$data['email_id'] = $tEmailId;
			$data['address1'] = $tAddress1;
			$data['gst'] = $tGst;
			$data['profession_id'] = $tProfessionId;
			$data['is_display'] = $tIsDisplay;
			$data['state_abb'] = $tStateAbb;
			$data['city_id'] = $tCityId;
			$data['birth_date'] = $birthDate;
			$data['anniversary_date'] = $anniversaryDate;
			$data['other_date'] = $otherDate;
			$data['credit_limit'] = $tCreditLimit;
			$data['credit_days'] = $tCreditDays;
			return $data;
		}
	}
	
	/**
     * @param key and value of request data
     * @return array/error message
     */
	public function trimUpdateData()
	{
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$tClientArray = array();
		$clientValue;
		$keyValue = func_get_arg(0);
		$convertedValue="";
		$isDisplayFlag=0;
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
		$clientValue = func_get_arg(1);
		for($data=0;$data<count($clientValue);$data++)
		{
			if(strcmp($convertedValue,"birth_date")==0)
			{
				$transformEntryDate=trim($clientValue);
				$splitedDate = explode("-",$transformEntryDate);
				$clientValue = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
			}
			if(strcmp($convertedValue,"anniversary_date")==0)
			{
				$transformAniDate=trim($clientValue);
				$splitedDate = explode("-",$transformAniDate);
				$clientValue = $splitedDate[2]."-".$splitedDate[1]."-".$splitedDate[0];
			}
			$tClientArray[$data]= array($convertedValue=> trim($clientValue));
			$clientEnumArray = array_keys($tClientArray[$data])[0];
		}
		$enumIsDispArray = array();
		$isDispEnum = new IsDisplayEnum();
		$enumIsDispArray = $isDispEnum->enumArrays();
		if(strcmp($clientEnumArray,'is_display')==0)
		{
			foreach ($enumIsDispArray as $key => $value)
			{
				if(strcmp($tClientArray[0]['is_display'],$value)==0)
				{
					$isDisplayFlag=1;
					break;
				}
				else
				{
					$isDisplayFlag=2;
				}
			}
		}
		
		if($isDisplayFlag==2)
		{
			return "1";
		}
		else
		{
			return $tClientArray;
		}
	}
}