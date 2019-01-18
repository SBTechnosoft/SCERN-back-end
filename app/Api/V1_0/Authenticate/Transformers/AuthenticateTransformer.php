<?php
namespace ERP\Api\V1_0\Authenticate\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class AuthenticateTransformer
{
   /**
     * @param Request $request
     * @return array
     */
    public function trimInsertData(Request $request)
    {
		$isDisplayFlag=0;
		$emailId = $request->input('emailId'); 
		$password = $request->input('password'); 
		
		//trim an input
		$tEmailId= trim($emailId);
		$tPassword = trim($password);
		
		//convert password into base64_encode
		$encodedPassword = base64_encode($tPassword);
		
		//make an array
		$data = array();
		$data['email_id'] = $tEmailId;
		$data['password'] = $encodedPassword;
		return $data;
	}
}