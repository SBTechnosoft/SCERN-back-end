<?php
namespace ERP\Console\Commands;

use Illuminate\Console\Command;
use DB;
use ERP\Entities\Constants\ConstantClass;
use ERP\Exceptions\ExceptionMessage;
use DateTime;
use ERP\Api\V1_0\Crm\Conversations\Controllers\ConversationController;
use Illuminate\Http\Request;
use Illuminate\Container\Container;
use ERP\Model\Accounting\Journals\JournalModel;
use ERP\Model\Accounting\Ledgers\LedgerModel;
use ERP\Model\Settings\Templates\TemplateModel;
use ERP\Core\Settings\Templates\Entities\TemplateTypeEnum;
use ERP\Model\Companies\CompanyModel;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //database selection
        $database = "";
        $constantDatabase = new ConstantClass();
        $databaseName = $constantDatabase->constantDatabaseForCron();
        $constantArray = $constantDatabase->constantVariable();
        
        date_default_timezone_set("Asia/Calcutta");
        DB::beginTransaction();
        $settingData = DB::connection($databaseName)->select("select
        setting_id,
        setting_type,
        setting_data,
        created_at,
        updated_at
        from setting_mst
        where deleted_at='0000-00-00 00:00:00'");
        DB::commit();
        //if we get settings-data
        if(count($settingData)!=0)
        {
            $resultArray = array();
            //check birthdate/anniversary/paymentDate setting status setting is available?
            $settingDataCount = count($settingData);
            for($settingArrayData=0;$settingArrayData<$settingDataCount;$settingArrayData++)
            {
                if(strcmp($settingData[$settingArrayData]->setting_type,$constantArray['birthDateReminderSetting'])==0)
                {
                    $decodedSettingData = json_decode($settingData[$settingArrayData]->setting_data);
                    if(strcmp($decodedSettingData->birthreminder_status,"on")==0)
                    {
                        $birthString = "birth";
                        $result = $this->allReminder($decodedSettingData,$databaseName,$birthString);
                        $resultArray['birthdate'] = $result;
                        // return $result;
                    }
                }
                else if(strcmp($settingData[$settingArrayData]->setting_type,$constantArray['anniDateReminderSetting'])==0)
                {
                    $decodedSettingData = json_decode($settingData[$settingArrayData]->setting_data);
                    if(strcmp($decodedSettingData->annireminder_status,"on")==0)
                    {
                        $anniString = "anni";
                        $result = $this->allReminder($decodedSettingData,$databaseName,$anniString);
                        $resultArray['anniversary'] = $result;
                        // return $result;
                    }
                }
                else if(strcmp($settingData[$settingArrayData]->setting_type,$constantArray['paymentDateSetting'])==0)
                {
                    $decodedSettingData = json_decode($settingData[$settingArrayData]->setting_data);
                    if(strcmp($decodedSettingData->paymentdate_status,"on")==0)
                    {
                        // $result = $this->paymentReminder($decodedSettingData,$databaseName);
                        // $resultArray['payment'] = $result;
                        // return $result;
                    }
                }
            }
            return $resultArray;
        }
    }

    /*
     * get next date from birth-date and add settings time and match it with current time & date and then sms/email send
    */
    public function allReminder($decodedSettingData,$databaseName,$birthAnni)
    {
        $constantDatabase = new ConstantClass();
        $constantArray = $constantDatabase->constantVariable();
        //add days in date
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $next_date = date('Y-m-d', strtotime($date .' +1 day'));
        //check this next-date in database of birth-date
        //database selection
        $database = "";
        $constantDatabase = new ConstantClass();
        $databaseName = $constantDatabase->constantDatabaseForCron();
        $fieldName = strcmp($birthAnni,"anni")==0 ? "anniversary" : $birthAnni;
        DB::beginTransaction();
        $clientData = DB::connection($databaseName)->select("select
        client_id,
        client_name,
        email_id,
        contact_no,
        contact_no1,
        address1,
        is_display,
        state_abb,
        city_id,
        company_name,
        profession_id,
        birth_date,
        anniversary_date,
        other_date,
        created_at,
        updated_at
        from client_mst
        where deleted_at='0000-00-00 00:00:00' and 
        DATE_FORMAT(".$fieldName."_date, '%m-%d') = DATE_FORMAT('".$next_date."', '%m-%d')");
        DB::commit();
        if(count($clientData)!=0)
        {
            $finalDateTime="";
            $newDateTime = date($next_date." 00:00:00");
            if(strcmp($birthAnni,'birth')==0)
            {
                $type = $decodedSettingData->birthreminder_type;
                $time = $decodedSettingData->birthreminder_time;
                $notifyBy = $decodedSettingData->birthreminder_notify_by;
            }
            else if(strcmp($birthAnni,'anni')==0)
            {
                $type = $decodedSettingData->annireminder_type;
                $time = $decodedSettingData->annireminder_time;
                $notifyBy = $decodedSettingData->annireminder_notify_by;
            }
            //add setting-time
            if(strcmp($type,"after")==0)
            {
                //add hours in next-date
                $date1 = new DateTime($newDateTime);
                $date1->modify("+".$time ."s");
                $finalDateTime = $date1->format("Y-m-d H:i:s");
            }
            else
            {
                //minus hours from next-date
                $date1 = new DateTime($newDateTime);
                $date1->modify("-".$time."s");
                $finalDateTime = $date1->format("Y-m-d H:i:s");
            }
            $finalDate = new DateTime($finalDateTime);
            $currentDateTime = new DateTime(date("Y-m-d h:i:s"));
            $diff   = date_diff($finalDate,$currentDateTime);
            if($diff->y==0 && $diff->m==0 && $diff->d==0) //&& $diff->h==0
            {
                //get exception message
                $exception = new ExceptionMessage();
                $exceptionArray = $exception->messageArrays();
                $templateType = new TemplateTypeEnum();
                $templateArray = $templateType->enumArrays();

                $ledgerModel = new LedgerModel();
                $templateModel = new TemplateModel();
                //send sms and mail as per selection of settings
                if(strcmp($notifyBy,"sms")==0 || strcmp($notifyBy,"both")==0)
                {
                    $method=$constantArray['postMethod'];
                    $path=$constantArray['clientUrl'];
                    
                    $clientCount = count($clientData);

                    for($clientArrayData=0;$clientArrayData<$clientCount;$clientArrayData++)
                    {
                        $htmlBody="";
                        $ledgerData = $ledgerModel->getDataAsPerClientId($clientData[$clientArrayData]->client_id);
                        $decodedLedgerData = json_decode($ledgerData);
                        if(strcmp($ledgerData,$exceptionArray['404'])!=0)
                        {
                            $setType = strcmp(ucfirst($birthAnni),"Birth")==0 ? ucfirst($birthAnni)."Day" : ucfirst($birthAnni);
                            $templateType = 'sms'.$setType.'Template';
                            $templateData = $templateModel->getAllTemplateData($decodedLedgerData[0]->company_id,$templateArray[$templateType]);
                            $decodedTemplateData = json_decode($templateData);
                            if(strcmp($templateData,$exceptionArray['404'])!=0)
                            {
                                $smsArray = array();
                                $smsArray['ClientName']=$clientData[$clientArrayData]->client_name;
                                $htmlBody = $decodedTemplateData[0]->template_body;
                                foreach($smsArray as $key => $value)
                                {
                                    $htmlBody = str_replace('['.$key.']', $value, $htmlBody);
                                }
                            }
                        }
                        //send sms
                        $data = array();
                        $data['client'] = array();
                        $data['contact_no'] = '';
                        $data['conversation'] = $htmlBody;
                        $data['client'][0]['clientId'] = $clientData[$clientArrayData]->client_id;
                        $clientRequest = Request::create($path,$method,$data);
                        //send sms
                        $conversationController = new ConversationController(new Container());
                        $result = $conversationController->storeSmsForReminder($clientRequest);
                        if(strcmp($result,$exceptionArray['200'])==0)
                        {
                            print_r($exceptionArray['successSms']);
                            echo " \n";
                        }
                        else
                        {
                            print_r($exceptionArray['SmsFail']);
                            echo " \n";
                        }
                    }
                }
                if(strcmp($notifyBy,"email")==0 || strcmp($notifyBy,"both")==0)
                {
                    $method=$constantArray['postMethod'];
                    $path=$constantArray['clientUrl'];
                    $clientCount = count($clientData);
                    for($clientArrayData=0;$clientArrayData<$clientCount;$clientArrayData++)
                    {
                        $htmlBody="";
                        $ledgerData = $ledgerModel->getDataAsPerClientId($clientData[$clientArrayData]->client_id);
                        $decodedLedgerData = json_decode($ledgerData);
                       
                        if(strcmp($ledgerData,$exceptionArray['404'])!=0)
                        {
                            $setType = strcmp(ucfirst($birthAnni),"Birth")==0 ? ucfirst($birthAnni)."Day" : ucfirst($birthAnni);
                            $templateType = 'sms'.$setType.'Template';
                            $templateData = $templateModel->getAllTemplateData($decodedLedgerData[0]->company_id,$templateArray[$templateType]);
                            $decodedTemplateData = json_decode($templateData);
                            if(strcmp($templateData,$exceptionArray['404'])!=0)
                            {
                                $emailArray = array();
                                $emailArray['ClientName']=$clientData[$clientArrayData]->client_name;
                                $htmlBody = $decodedTemplateData[0]->template_body;
                                foreach($emailArray as $key => $value)
                                {
                                    $htmlBody = str_replace('['.$key.']', $value, $htmlBody);
                                }
                            }
                        }
                        $data = array();
                        $data['emailId'] = '';
                        $data['conversation'] = $htmlBody;
                        $data['client'] = array();
                        $data['client'][0]['clientId'] = $clientData[$clientArrayData]->client_id;
                        $clientRequest = Request::create($path,$method,$data);
                        //send email
                        $conversationController = new ConversationController(new Container());
                        $result = $conversationController->storeEmailFromReminder($clientRequest);
                        if(strcmp($result,$exceptionArray['200'])==0)
                        {
                            print_r($exceptionArray['successEmail']);
                            echo " \n";
                        }
                        else
                        {
                            print_r($exceptionArray['EmailFail']);
                            echo " \n";
                        }
                    }
                 }
            }
        }
        else
        {
            echo "data not found";
        }
    }

    /*
     * send sms-email for payment
    */
    public function paymentReminder($settingData,$databaseName)
    {
        $constantDatabase = new ConstantClass();
        $constantArray = $constantDatabase->constantVariable();
        //get exception message
        $exception = new ExceptionMessage();
        $exceptionArray = $exception->messageArrays();

        $noOfDays = $settingData->paymentdate_no_of_days;
        //get remining amount of clients
        $journalModel = new JournalModel();
        $journalData = $journalModel->getReminingPayment();
        $decodedJsonData = json_decode($journalData);
        $journalCount = count($decodedJsonData);
        if($journalCount!=0)
        {
            for($arrayData=0;$arrayData<count($decodedJsonData);$arrayData++)
            {
                if($arrayData<$journalCount-1)
                {
                    if($decodedJsonData[$arrayData]->ledger_id==$decodedJsonData[$arrayData+1]->ledger_id)
                    {
                        if($decodedJsonData[$arrayData]->amount>=$decodedJsonData[$arrayData+1]->amount)
                        {
                            $decodedJsonData[$arrayData]->amount = 
                                        $decodedJsonData[$arrayData]->amount-$decodedJsonData[$arrayData+1]->amount;
                        }
                        else
                        {
                            $decodedJsonData[$arrayData]->amount = 
                                        $decodedJsonData[$arrayData+1]->amount-$decodedJsonData[$arrayData]->amount;
                            $decodedJsonData[$arrayData]->amount_type = $decodedJsonData[$arrayData+1]->amount_type;         
                        }
                        unset($decodedJsonData[$arrayData+1]);
                        $decodedJsonData = array_values($decodedJsonData);
                    }
                }
            }
        }
        if(count($decodedJsonData)!=0)
        {
            //send email/sms to client data
            //get ledger-data
            $ledgerModel = new LedgerModel();
            $ledgerCountData = count($decodedJsonData);
            
            for($ledgerArrayData=0;$ledgerArrayData<$ledgerCountData;$ledgerArrayData++)
            {
                $ledgerData = $ledgerModel->getData($decodedJsonData[$ledgerArrayData]->ledger_id);
                $decodedLedgerData = json_decode($ledgerData);
                if($decodedLedgerData->client_id!='' && $decodedLedgerData->client_id!='null' && $decodedLedgerData->client_id!=null && $decodedLedgerData->client_id!=0)
                {
                    $templateModel = new TemplateModel();
                    $companyModel = new CompanyModel();
                    //get company-name as per given company-id
                    $companyData = $companyModel->getData($decodedLedgerData->company_id);
                    $decodedCompanyData = json_decode($companyData);
                    $companyName = strcmp($companyData,$exceptionArray['404'])!=0 ? $decodedCompanyData[0]->company_name :"";
                        
                    $templateType = new TemplateTypeEnum();
                    $templateArray = $templateType->enumArrays();
                    if($decodedLedgerData->email_id!='')
                    {
                        $templateData = $templateModel->getAllTemplateData($decodedLedgerData->company_id,$templateArray['emailDuePaymentTemplate']);
                        $decodedTemplateData = json_decode($templateData);
                        if(strcmp($templateData,$exceptionArray['404'])!=0)
                        {
                            $paymentArray = array();
                            $paymentArray['ClientName']=$decodedLedgerData->client_name;
                            $paymentArray['Company']=$companyName;
                            $paymentArray['RemainingPayment']=$decodedJsonData[$ledgerArrayData]->amount;
                            $htmlBody = $decodedTemplateData[0]->template_body;
                            foreach($paymentArray as $key => $value)
                            {
                                $htmlBody = str_replace('['.$key.']', $value, $htmlBody);
                            }
                        }
                        //send an email  
                        $data = array();
                        $data['emailId'] = $decodedLedgerData->email_id;
                        $data['conversation'] = $htmlBody;
                        $data['client'] = array();
                        $data['client'][0]['clientId'] = $decodedLedgerData->client_id;
                        $method=$constantArray['postMethod'];
                        $path=$constantArray['clientUrl'];
                        $clientRequest = Request::create($path,$method,$data);
                        //send email
                        $conversationController = new ConversationController(new Container());
                        $result = $conversationController->storeEmailFromReminder($clientRequest);
                        if(strcmp($result,$exceptionArray['200'])==0)
                        {
                            print_r($exceptionArray['successEmail']);
                            echo " \n";
                        }
                        else
                        {
                            print_r($exceptionArray['EmailFail']);
                            echo " \n";
                        }  
                    }
                    if($decodedLedgerData->contact_no!='')
                    {
                        $templateData = $templateModel->getAllTemplateData($decodedLedgerData->company_id,$templateArray['smsDuePaymentTemplate']);
                        $decodedTemplateData = json_decode($templateData);
                        
                        if(strcmp($templateData,$exceptionArray['404'])!=0)
                        {
                            $paymentArray = array();
                            $paymentArray['ClientName']=$decodedLedgerData->client_name;
                            $paymentArray['Company']=$companyName;
                            $paymentArray['RemainingPayment']=$decodedJsonData[$ledgerArrayData]->amount;
                            $htmlBody = $decodedTemplateData[0]->template_body;
                            foreach($paymentArray as $key => $value)
                            {
                                $htmlBody = str_replace('['.$key.']', $value, $htmlBody);
                            }
                        }
                        //send sms
                        $data = array();
                        $data['contact_no'] = $decodedLedgerData->contact_no;
                        $data['conversation'] = $htmlBody;
                        $data['client'] = array();
                        $data['client'][0]['clientId'] = $decodedLedgerData->client_id;
                        $method=$constantArray['postMethod'];
                        $path=$constantArray['clientUrl'];
                        $clientRequest = Request::create($path,$method,$data);
                        //send sms
                        $conversationController = new ConversationController(new Container());
                        $result = $conversationController->storeSmsForReminder($clientRequest);
                        if(strcmp($result,$exceptionArray['200'])==0)
                        {
                            print_r($exceptionArray['successSms']);
                            echo " \n";
                        }
                        else
                        {
                            print_r($exceptionArray['SmsFail']);
                            echo " \n";
                        }
                    }
                }
            }
        }
    }
}
