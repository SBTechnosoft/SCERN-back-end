<?php

namespace ERP\Api\V1_0\Crm\Conversations\Processors;



use ERP\Api\V1_0\Support\BaseProcessor;

use ERP\Core\Crm\Conversations\Persistables\ConversationPersistable;

use Illuminate\Http\Request;

use ERP\Http\Requests;

use Illuminate\Http\Response;

use ERP\Core\Crm\Conversations\Validations\ConversationValidate;

use ERP\Api\V1_0\Crm\Conversations\Transformers\ConversationTransformer;

use ERP\Exceptions\ExceptionMessage;

use ERP\Entities\Constants\ConstantClass;

use Illuminate\Container\Container;

use ERP\Api\V1_0\Documents\Controllers\DocumentController;

use ERP\Model\Clients\ClientModel;

use PHPMailer;

use SMTP;

use ERP\Model\Accounting\Bills\BillModel;

use ERP\Model\Accounting\Quotations\QuotationModel;

use ERP\Core\Settings\Templates\Entities\TemplateTypeEnum;

use ERP\Core\Settings\Templates\Services\TemplateService;

use Carbon;

use ERP\Model\Companies\CompanyModel;

/**

 * @author Reema Patel<reema.p@siliconbrain.in>

 */

class ConversationProcessor extends BaseProcessor

{

	/**

     * @var conversationPersistable

	 * @var request

     */

	private $conversationPersistable;

	private $request;    

	

    /**

     * get the form-data and set into the persistable object

     * $param Request object [Request $request]

     * @return Job-Form Persistable object

     */	

    public function createPersistable(Request $request,$conversationType,$mailMessage,$smsMessage)

	{	

		$this->request = $request;

		$data=0;		

		$docFlag=0;

		

		//get exception message

		$exception = new ExceptionMessage();

		$msgArray = $exception->messageArrays();

		//save documents in folder

		$file = $request->file();

		$processedData = array();

		if(in_array(true,$file))

		{

			$constantClass = new ConstantClass();

			$constantArray = $constantClass->constantVariable();
			$documentController =new DocumentController(new Container());

			$processedData = $documentController->insertUpdate($request,$constantArray['emailDocumentUrl']);

			if(is_array($processedData))

			{

				$docFlag=1;

			}

			else

			{

				return $processedData;

			}

		}

		if(count($request->input())==0)

		{

			return $msgArray['204'];

		}

		else

		{

			//trim an input 

			$conversationTransformer = new ConversationTransformer();

			$tRequest = $conversationTransformer->trimInsertData($this->request,$conversationType);

			//validation

			$conversationValidate = new ConversationValidate();

			$status = $conversationValidate->validate($tRequest);

			if($status=="Success")

			{

				$errorFlag=0;

				$successFlag=0;

				//mail/sms send
				$comment = "";
				$result = $this->mailOrSmsSend($tRequest,$processedData,$request->header(),$mailMessage,$smsMessage);

				if(!is_array($result))

				{

					return $result;

				}

				else

				{

					if(array_key_exists('error',$result))

					{

						if(count($result['error'])!=0)

						{

							$errorFlag=1;

						}

					}

					if(array_key_exists('success',$result))

					{

						if(count($result['success'])!=0)

						{

							$successFlag=1;

							$dataArray = array();

							if(array_key_exists('document',$result['success']))

							{

								$processedData = $result['success']['document'];

								$resultCount = count($result['success']);

								array_splice($result['success'],$resultCount-1,1);

								$docFlag=2;

							}

							foreach($result['success'] as $key=>$value)

							{

								$dataArray[$key] = $tRequest;

								$dataArray[$key]['comment'] = array_key_exists('comment',$result['success'][$key])?$result['success'][$key]['comment']:'';

								$dataArray[$key]['client_id'] = array_key_exists('client_id',$result['success'][$key])?$result['success'][$key]['client_id']:'';

								$dataArray[$key]['email_id'] = array_key_exists('email_id',$result['success'][$key])?$result['success'][$key]['email_id']:'';

								$dataArray[$key]['contact_no'] = array_key_exists('contact_no',$result['success'][$key])?$result['success'][$key]['contact_no']:'';

							}

						}

					}

				}

				if($successFlag==1)

				{

					$finalArray = array();

					$finalArray['clientSuccessData'] = array();

					$clientCount = count($dataArray);

					for($clientData=0;$clientData<$clientCount;$clientData++)

					{

						$data=0;

						$conversationValue = array();

						$keyName = array();

						$value = array();

						$conversationArray = array();

						foreach ($dataArray[$clientData] as $key => $value)

						{

							if(!is_numeric($value))

							{

								if (strpos($value, '\'') !== FALSE)

								{

									$conversationValue[$data]= str_replace("'","\'",$value);

									$keyName[$data] = $key;

								}

								else

								{

									$conversationValue[$data] = $value;

									$keyName[$data] = $key;

								}

							}

							else

							{

								$conversationValue[$data]= $value;

								$keyName[$data] = $key;

							}

							$data++;

						}

						$conversationValueCount = count($conversationValue);

						// set data to the persistable object

						for($data=0;$data<$conversationValueCount;$data++)

						{

							//set the data in persistable object

							$conversationPersistable = new ConversationPersistable();	

							$str = str_replace(' ', '', ucwords(str_replace('_', ' ', $keyName[$data])));

							

							//make function name dynamically

							$setFuncName = 'set'.$str;

							$getFuncName[$data] = 'get'.$str;

							$conversationPersistable->$setFuncName($conversationValue[$data]);

							$conversationPersistable->setName($getFuncName[$data]);

							$conversationPersistable->setKey($keyName[$data]);

							$conversationArray[$data] = array($conversationPersistable);

							if($data==(count($conversationValue)-1))

							{

								if($docFlag==1 || $docFlag==2)

								{

									$conversationArray['document']=$processedData;

								}

							}

						}

						$finalArray['clientSuccessData'][$clientData]=$conversationArray;

					}

					if($errorFlag==1)

					{

						$finalArray['clientFailData'] = $result['error'];

					}

					return $finalArray;

				}

			}

			else

			{

				return $status;

			}

		}

	}

	

	 /**

     * send email/sms

     * $param trim-request array

     * @return error-message/status

     */	

	public function mailOrSmsSend($trimRequest,$documentData=null,$headerData,$mailMessage,$smsMessage)

	{

		//get exception message

		$exception = new ExceptionMessage();

		$exceptionArray = $exception->messageArrays();

		$constantClass = new ConstantClass();

		$constantArray = $constantClass->constantVariable();

		$commentMessage = $constantClass->getCommentMessage();
		$smsSettingArray = $constantClass->setSmsPassword();

		$emailflag=0;

		if(strcmp($trimRequest['conversation_type'],'email')==0)

		{

			//get client-data

			$clientModel = new ClientModel();

			//check for sale_id

			if(array_key_exists('saleid',$headerData))

			{

				//get data from database as per given sale-id and send mail

				if($headerData['saleid'][0]!='')

				{

					//get sale-data

					$billModel = new BillModel();

					$saleBillData = $billModel->getSaleIdData($headerData['saleid'][0]);

					if(strcmp($saleBillData,$exceptionArray['404'])==0)

					{

						return $exceptionArray['404'];

					}

					$decodedBillData = json_decode($saleBillData);

					

					$clientData = $clientModel->getData($decodedBillData[0]->client_id);

					if(strcmp($clientData,$exceptionArray['404'])==0)

					{

						return $exceptionArray['404'];

					}

					$decodedClientData = json_decode($clientData);

					$mailArray = array();

					if($decodedClientData->clientData[0]->email_id!='')

					{

						$templateType = new TemplateTypeEnum();

						$templateArray = $templateType->enumArrays();

						//get email-template

						$templateService = new TemplateService();

						$emailTemplateData = $templateService->getSpecificData($decodedBillData[0]->company_id,$templateArray['emailNewOrderTemplate']);

						if(strcmp($emailTemplateData,$exceptionArray['404'])==0)

						{

							return $emailTemplateData;

						}

						$companyModel = new CompanyModel();

						$companyData = $companyModel->getData($decodedBillData[0]->company_id);

						$companyDecodedData = json_decode($companyData);

						//replace data in template

						$htmlBody = json_decode($emailTemplateData)[0]->templateBody;

						$emailArray = array();

						$emailArray['Company']=$companyDecodedData[0]->company_name;

						$emailArray['ClientName']=$decodedClientData->clientData[0]->client_name;

						foreach($emailArray as $key => $value)

						{

							$htmlBody = str_replace('['.$key.']', $value, $htmlBody);

						}

						$mailArray['email_id'] =  $decodedClientData->clientData[0]->email_id;

						$mailArray['conversation'] =  $htmlBody;

						$mailArray['bcc_email_id'] =  '';

						$mailArray['cc_email_id'] =  '';

						$documentFlag=0;

						if(count($decodedClientData->clientDocumentData)!=0)

						{

							$documentCount = count($decodedClientData->clientDocumentData);

							$documentSorting = array();

							for($documentArray=0;$documentArray<$documentCount;$documentArray++)

							{

								if(strcmp($decodedClientData->clientDocumentData[$documentArray]->document_format,'pdf')==0)

								{

									$documentSorting[$documentArray] = $decodedClientData->clientDocumentData[$documentArray]->document_id;

								}

							}

							$maxId = max(array_keys($documentSorting));

							$documentId = $documentSorting[$maxId];

							

							$documentFlag=1;

							$documentData[0][0] = $decodedClientData->clientDocumentData[$maxId]->document_name;

							$documentData[0][1] = $decodedClientData->clientDocumentData[$maxId]->document_size;

							$documentData[0][2] = $decodedClientData->clientDocumentData[$maxId]->document_format;

							$documentData[0][3] =  $constantArray['billUrl'];

						}

						$commentDataArray['success'] = array();

						$commentDataArray['success'][0]['client_id'] = $decodedBillData[0]->client_id;

						$commentDataArray['success'][0]['comment'] = $commentMessage->billMailSend;

						$commentDataArray['success'][0]['email_id'] = $decodedClientData->clientData[0]->email_id;

						$commentDataArray['success']['document'] = $documentData;

						$mailResult = $this->mailSend($mailArray,$documentData,$decodedClientData->clientData[0]->email_id);

						$commentData = $mailResult==1 ? $commentDataArray : $exceptionArray['EmailFail'];

						return $commentData;

					}

					else

					{

						return $exceptionArray['EmailFail'];

					}

				}

				else

				{

					return $exceptionArray['content'];

				}

			}

			else if(array_key_exists('quotationbillid',$headerData))

			{

				//get data from database as per given sale-id and send mail

				if($headerData['quotationbillid'][0]!='')

				{

					//get sale-data

					$quotationModel = new QuotationModel();

					$saleBillData = $quotationModel->getquotationIdData($headerData['quotationbillid'][0]);

					if(strcmp($saleBillData,$exceptionArray['404'])==0)

					{

						return $exceptionArray['404'];

					}

					$decodedBillData = json_decode($saleBillData);

					

					$clientData = $clientModel->getData($decodedBillData[0]->client_id);

					if(strcmp($clientData,$exceptionArray['404'])==0)

					{

						return $exceptionArray['404'];

					}

					$decodedClientData = json_decode($clientData);

					$mailArray = array();

					if($decodedClientData->clientData[0]->email_id!='')

					{

						$templateType = new TemplateTypeEnum();

						$templateArray = $templateType->enumArrays();

						//get email-template

						$templateService = new TemplateService();

						$emailTemplateData = $templateService->getSpecificData($decodedBillData[0]->company_id,$templateArray['emailNewOrderTemplate']);

						if(strcmp($emailTemplateData,$exceptionArray['404'])==0)

						{

							return $emailTemplateData;

						}

						$companyModel = new CompanyModel();

						$companyData = $companyModel->getData($decodedBillData[0]->company_id);

						$companyDecodedData = json_decode($companyData);

						//replace data in template

						$htmlBody = json_decode($emailTemplateData)[0]->templateBody;

						$emailArray = array();

						$emailArray['Company']=$companyDecodedData[0]->company_name;

						$emailArray['ClientName']=$decodedClientData->clientData[0]->client_name;

						foreach($emailArray as $key => $value)

						{

							$htmlBody = str_replace('['.$key.']', $value, $htmlBody);

						}

						$mailArray['email_id'] =  $decodedClientData->clientData[0]->email_id;

						$mailArray['conversation'] =  $htmlBody;

						$mailArray['bcc_email_id'] =  '';

						$mailArray['cc_email_id'] =  '';

						$documentFlag=0;

						$quotationModel = new QuotationModel();
						$quotationDocData = $quotationModel->getDocumentDataByClientId($decodedClientData->clientData[0]->client_id);
						
						if(strcmp($quotationDocData,$exceptionArray['204'])==0)
						{
							return $exceptionArray['204'];
						}

						$quotationDocData =  json_decode($quotationDocData);

						if(count($quotationDocData->clientDocumentData)!=0)

						{

							$documentCount = count($quotationDocData->clientDocumentData);

							$documentSorting = array();

							for($documentArray=0;$documentArray<$documentCount;$documentArray++)

							{

								if(strcmp($quotationDocData->clientDocumentData[$documentArray]->document_format,'pdf')==0)

								{

									$documentSorting[$documentArray] = $quotationDocData->clientDocumentData[$documentArray]->document_id;

								}

							}

							$maxId = max(array_keys($documentSorting));

							$documentId = $documentSorting[$maxId];

							

							$documentFlag=1;

							$documentData[0][0] = $quotationDocData->clientDocumentData[$maxId]->document_name;

							$documentData[0][1] = $quotationDocData->clientDocumentData[$maxId]->document_size;

							$documentData[0][2] = $quotationDocData->clientDocumentData[$maxId]->document_format;

							$documentData[0][3] =  $constantArray['quotationDocUrl'];

						}

						$commentDataArray['success'] = array();

						$commentDataArray['success'][0]['client_id'] = $decodedBillData[0]->client_id;

						$commentDataArray['success'][0]['comment'] = $commentMessage->quotationMailSend;

						$commentDataArray['success'][0]['email_id'] = $decodedClientData->clientData[0]->email_id;

						$commentDataArray['success']['document'] = $documentData;

						$mailResult = $this->mailSend($mailArray,$documentData,$decodedClientData->clientData[0]->email_id);

						$commentData = $mailResult==1 ? $commentDataArray : $exceptionArray['EmailFail'];

						return $commentData;

					}

					else

					{

						return $exceptionArray['EmailFail'];

					}

				}

				else

				{

					return $exceptionArray['content'];

				}

			}

			else if(count($trimRequest['client_id'])!=0)

			{

				$errorArray = array();

				$commentIndex = 0;

				$errorIndex = 0;

				$commentDataArray = array();

				$clientIdCount = count($trimRequest['client_id']);

				// send multiple mail

				for($clientArray=0;$clientArray<$clientIdCount;$clientArray++)

				{

					$clientData[$clientArray] = $clientModel->getData($trimRequest['client_id'][$clientArray]);

					$decodedClientData[$clientArray] = json_decode($clientData[$clientArray]);

					$mailResult = $this->mailSend($trimRequest,$documentData,$decodedClientData[$clientArray]->clientData[0]->email_id);

					if($mailResult!=1)

					{

						$errorArray[$errorIndex]['client_id'] = $trimRequest['client_id'][$clientArray];

						$errorArray[$errorIndex]['message'] = $exceptionArray['EmailFail'];

						$errorIndex++;

					}

					else

					{

						$commentDataArray[$commentIndex] = array();

						$commentDataArray[$commentIndex]['client_id'] = $trimRequest['client_id'][$clientArray];

						$commentDataArray[$commentIndex]['comment'] = $mailMessage;

						$commentDataArray[$commentIndex]['email_id'] = $decodedClientData[$clientArray]->clientData[0]->email_id;

						$commentIndex++;

					}

				}

				$commentData = array();

				$commentData['error'] = $errorArray;

				$commentData['success'] = $commentDataArray;

				return $commentData;

			}

			else

			{

				return $exceptionArray['content'];

			}

		}

		else

		{
			$finalArray = array();

			$errrorMessage = array();

			$contactDataArray = array();

			$errorFlag=0;

			for($clientArray=0;$clientArray<count($trimRequest['client_id']);$clientArray++)

			{

				//send an sms

				if($trimRequest['contact_no']=='')

				{

					// get email from client-id

					$clientModel = new ClientModel();

					$clientDataResult = $clientModel->getData($trimRequest['client_id'][$clientArray]);

					if(strcmp($clientDataResult,$exceptionArray['404'])==0)

					{

						$errorFlag=1;

						$errrorMessage[$clientArray]['client_id'] =  $trimRequest['client_id'][$clientArray];

						$errrorMessage[$clientArray]['comment'] =  $exceptionArray['SmsFail'];

						continue;

					}

					$decodedClientData = json_decode($clientDataResult);

					$contactNo = $decodedClientData->clientData[0]->contact_no;

				}

				else

				{

					$contactNo = $trimRequest['contact_no'];

				}

				$trimRequest['conversation'] = str_replace('&nbsp;','',strip_tags($trimRequest['conversation']));
				
				//send sms

				$data = array(

					'user' => $smsSettingArray['user'],

					'password' => $smsSettingArray['password'],

					'msisdn' => $contactNo,

					'sid' => $smsSettingArray['sid'],

					'msg' => $trimRequest['conversation'],

					'fl' =>"0",

					'gwid'=>"2"

				);
				list($header,$content) = $this->postRequest("http://login.arihantsms.com//vendorsms/pushsms.aspx",$data);

				$contactDataArray[$clientArray]['client_id'] = $trimRequest['client_id'][$clientArray]; 

				$contactDataArray[$clientArray]['comment'] = $smsMessage; 

				$contactDataArray[$clientArray]['contact_no'] = $contactNo;

				$contactDataArray[$clientArray]['company_id'] ='';   

				// $url = "http://login.arihantsms.com/vendorsms/pushsms.aspx?user=siliconbrain&password=demo54321&msisdn=9558080695&sid=ERPAKC&msg=hi&fl=0&gwid=2";

			}

			if($errorFlag==1)

			{

				$finalArray['error'] = $errrorMessage;

			}

			$finalArray['success'] = $contactDataArray;

			//sms successfully send

			return $finalArray;
		}

		

	}

	

	public function mailSend($trimRequest,$documentData=null,$emailId)

	{

		$emailflag=0;

		if($trimRequest['conversation']=='')

		{

			$emailflag=2;

			return $emailflag;

		}

		

		//get exception message

		$exception = new ExceptionMessage();

		$exceptionArray = $exception->messageArrays();

		$constantClass = new ConstantClass();
		$constantArray = $constantClass->constantVariable();
		$constantIdPassword = $constantClass->setEmailPassword();

		$mail = new PHPMailer; // create a new object

		$email = $emailId;
		// $mail->isSMTP(); // enable SMTP

		$mail->Host = "smtp.gmail.com";

		$mail->Port = 587; // or 465

		$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only

		$mail->SMTPAuth = true; // authentication enabled

		$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for Gmail

		$mail->Username = $constantIdPassword['emailId'];

		$mail->Password = $constantIdPassword['password'];

		// $mail->SetFrom($constantIdPassword['emailId']);
		$mail->From = $constantIdPassword['emailId'];
		$mail->FromName = $constantIdPassword['emailId'];

		$mail->Subject = array_key_exists('subject',$trimRequest) ? $trimRequest['subject']:$constantArray['emailSubject'];
		$mail->AddAddress($email);


		$mail->Body = $trimRequest['conversation'];

		$mail->AltBody = $trimRequest['conversation'];

		if($trimRequest['bcc_email_id']!='')

		{

			$mail->AddBCC($trimRequest['bcc_email_id'], 'Sample');

		}

		if($trimRequest['cc_email_id']!='')

		{

			$mail->AddCC($trimRequest['cc_email_id'], 'Sample');

		}

		if(count($documentData)!=0)

		{

			$mytime = Carbon\Carbon::now();

			$splitedTime = explode(' ',$mytime);

			// Add a recipient

			$name = "Your Document(".$splitedTime[0].").".$documentData[0][2];

			$documentPath = $documentData[0][3].$documentData[0][0];

			$mail->AddAttachment($documentPath,$name,'base64','application/octet-stream');

		}

		$mail->isHTML(true);

		if(!$mail->Send()) {

			$emailflag=0;

			return $emailflag;

		} 

		else 

		{
			$emailflag=1;

			return $emailflag;

		}

	}

	

	public function postRequest($url,$_data) 

	{

		// convert variables array to string:

		$data = array();

		while(list($n,$v) = each($_data))

		{

			$data[] = "$n=$v";

		}



		$data = implode('&', $data);

		$url = parse_url($url);

		if ($url['scheme'] != 'http') {

		die('Only HTTP request are supported !');

		}

		// extract host and path:

		$host = $url['host'];

		$path = $url['path'];

		// open a socket connection on port 80

		$fp = fsockopen($host, 80);



		// send the request headers:

		fputs($fp, "POST $path HTTP/1.1\r\n");

		fputs($fp, "Host: $host\r\n");

		//fputs($fp, "Referer: $referer\r\n");

		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");

		fputs($fp, "Content-length: ". strlen($data)."\r\n");

		fputs($fp, "Connection: close\r\n\r\n");

		fputs($fp, $data);

		$result = '';

		while(!feof($fp)) {

		// receive the results of the request

		$result .= fgets($fp, 128);

		}



		// close the socket connection:

		fclose($fp);

		// split the result header from the content

		$result = explode("\r\n\r\n", $result, 2);



		$header = isset($result[0]) ? $result[0] : '';



		$content = isset($result[1]) ? $result[1] : '';

		// return as array:

		return array($header, $content);

	}

}