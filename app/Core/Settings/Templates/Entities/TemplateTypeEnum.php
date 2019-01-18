<?php
namespace ERP\Core\Settings\Templates\Entities;

/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class TemplateTypeEnum
{
	public function enumArrays()
	{
		$enumArray = array();
		$enumArray['generalTemplate'] = "general";
		$enumArray['quotationTemplate'] = "quotation";
		$enumArray['invoiceTemplate'] = "invoice";
		$enumArray['paymentTemplate'] = "payment";
		$enumArray['receiptTemplate'] = "receipt";
		$enumArray['blankTemplate'] = "blank";
		$enumArray['jobCardTemplate'] = "job_card";
		
		$enumArray['emailNewOrderTemplate'] = "email_newOrder";
		$enumArray['emailDuePaymentTemplate'] = "email_duePayment";
		$enumArray['emailBirthDayTemplate'] = "email_birthDay";
		$enumArray['emailAnniTemplate'] = "email_anniversary";

		$enumArray['smsNewOrderTemplate'] = "sms_newOrder";
		$enumArray['smsDuePaymentTemplate'] = "sms_duePayment";
		$enumArray['smsBirthDayTemplate'] = "sms_birthDay";
		$enumArray['smsAnniTemplate'] = "sms_anniversary";

		$enumArray['thermalInvoice'] = "thermal_invoice";

		return $enumArray;
	}

	public function templateName()
	{
		$templateNameArray=array();
		$templateNameArray[0] = "Invoice";
		$templateNameArray[1] = "Payment";
		$templateNameArray[2] = "Blank";
		$templateNameArray[3] = "Quotation";
		$templateNameArray[4] = "Email_NewOrder";
		$templateNameArray[5] = "Email_DuePayment";
		$templateNameArray[6] = "Email_BirthDay";
		$templateNameArray[7] = "Email_AnniversaryDay";
		$templateNameArray[8] = "Sms_NewOrder";
		$templateNameArray[9] = "Sms_DuePayment";
		$templateNameArray[10] = "Sms_BirthDay";
		$templateNameArray[11] = "Sms_AnniversaryDay";

		$templateNameArray[12] = "Thermal_invoice";
		
		return $templateNameArray;
	}
}