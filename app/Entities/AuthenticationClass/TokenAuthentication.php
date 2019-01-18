<?php
namespace ERP\Entities\AuthenticationClass;

use ERP\Core\Authenticate\Services\AuthenticateService;
use Carbon;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;
use ERP\Model\Authenticate\AuthenticateModel;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TokenAuthentication extends AuthenticateService
{
    public function authenticate($headerData)
	{
		$tokenFlag=0;
		$date="";
	
		//get constant array
		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		
		// get exception message
		$exception = new ExceptionMessage();
		$exceptionArray = $exception->messageArrays();
		if(!array_key_exists('authenticationtoken',$headerData))
		{
			//token not exists
			return $exceptionArray['NoExists'];
		}
		
		//get active-session data for token validation
		$authenticationModel = new AuthenticateModel();
		$authenticationData = $authenticationModel->checkAuthenticationToken($headerData['authenticationtoken'][0]);
		
		if(is_array($authenticationData))
		{
			
			$date = $authenticationData[0]->updated_at;
			$tokenFlag =1;
		}
		
		if($tokenFlag==0)
		{
			//token not matched
			return $exceptionArray['NoMatch'];
		}
		else
		{
			$mytime = Carbon::now();
			$currentDateTime = $mytime->toDateTimeString();
			$convertedDate= Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTime)->format('d-m-Y H:i:s');
			$convertedUpdateDate= Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d-m-Y H:i:s');

			$stringDate = strtotime($convertedUpdateDate);
			$stringConvertedDate = strtotime($convertedDate);
			if($stringConvertedDate>=$stringDate)
			{
				$seconds = $stringConvertedDate-$stringDate;
			}
			else
			{
				$seconds = $stringDate-$stringConvertedDate;
			}
			
			// extract hours
			$hours = floor($seconds / (60 * 60));
			
			// extract minutes
			$divisor_for_minutes = $seconds % (60 * 60);
			$minutes = floor($divisor_for_minutes / 60);
						
			// extract the remaining seconds
			$divisor_for_seconds = $divisor_for_minutes % 60;
			$seconds = ceil($divisor_for_seconds);
			$hourValue = 24;
			if($hours<$hourValue || $hours==$hourValue && $minutes==0 && $seconds==0)
			{
				// update updated_at time of token
				$authenticationModel = new AuthenticateModel();
				$authenticationResult = $authenticationModel->changeDate($headerData);
				return $constantArray['success'];
			}
			else
			{
				//expired log out
				return $exceptionArray['token'];
			}
		}
	}

	public function webAuthentication($tokenArray,$userArray)
	{
		if(is_array($tokenArray))
		{
			if (isset($tokenArray['token']) && isset($tokenArray['expireDate'])) 
			{
				//get exception message
				$exception = new ExceptionMessage();
				$exceptionArray = $exception->messageArrays();
				$session_id = $userArray['session_id'];

				if ($session_id !=''){
					//update ExpireDate of webtoken
					$authenticationModel = new AuthenticateModel();
					$authenticationResult = $authenticationModel->freshWebToken($tokenArray,$session_id);

					if(strcmp($authenticationResult, $exceptionArray['200']) == 0){
						return true;
					}
				}
			}
		}
		return false;
	}
}
