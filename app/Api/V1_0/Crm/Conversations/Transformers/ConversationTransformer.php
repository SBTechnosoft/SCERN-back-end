<?php
namespace ERP\Api\V1_0\Crm\Conversations\Transformers;

use Illuminate\Http\Request;
use ERP\Http\Requests;
/**
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ConversationTransformer
{
    /**
     * @param Request $request(object) and conversation-type 
	 * trim data
     * @return array
     */
    public function trimInsertData(Request $request,$conversationType)
    {
    	$data = array();
		// data get from body and trim an input
		$data['email_id'] = array_key_exists('emailId',$request->input())?trim($request->input('emailId')):'';
		$data['cc_email_id'] = array_key_exists('ccEmailId',$request->input())?trim($request->input('ccEmailId')):'';
		$data['bcc_email_id'] = array_key_exists('bccEmailId',$request->input())?trim($request->input('bccEmailId')):'';
		$data['subject'] = array_key_exists('subject',$request->input())?trim($request->input('subject')):'ERP';
		$data['conversation'] = array_key_exists('conversation',$request->input())?trim($request->input('conversation')):'';
		// $data['company_id'] = trim($request->input('companyId'));
		$data['branch_id'] = array_key_exists('branchId',$request->input()) ?trim($request->input('branchId')):'';
		$data['contact_no'] = array_key_exists('contactNo',$request->input()) ? trim($request->input('contactNo')):'';
		$data['conversation_type'] = trim($conversationType);
		$data['client_id'] = array();
		if(array_key_exists('client',$request->input()))
		{
			$countClientId = count($request->input()['client']);
			for($arrayData=0;$arrayData<$countClientId;$arrayData++)
			{
				$data['client_id'][$arrayData] = $request->input()['client'][$arrayData]['clientId'];
			}
		}
		return $data;
	}
}