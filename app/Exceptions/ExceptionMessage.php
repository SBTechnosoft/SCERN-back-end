<?php
namespace ERP\Exceptions;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ExceptionMessage 
{
    public function messageArrays()
	{
		$msgArray = array();
		$msgArray['415'] = "415: Unsupported Media Type";
		$msgArray['fileSize'] = "FileNotFoundException: The file is too long";
		$msgArray['500'] = "500: Internal Server Error";
		$msgArray['200'] = "200: OK";
		$msgArray['204'] = "204: No Content";
		$msgArray['404'] = "404: Not Found";
		$msgArray['content'] = "content: not proper content"; //company-insert-isDisp&isDef-not proper
		$msgArray['equal'] = "equal: credit-debit amount is not an equal";
		$msgArray['stateAbb'] = "required: state-abb is required";
		$msgArray['stateMatch'] = "Exists: state is already exists";
		$msgArray['token'] = "Expired: Token Expired";
		$msgArray['NoMatch'] = "NoMatch: Token Not Matched";
		$msgArray['Exists'] = "Exists: Token Already Exists";
		$msgArray['NoExists'] = "NotExists: Token Not Exists";
		$msgArray['Email'] = "Message could not be sent";
		$msgArray['EmailFail'] = "Mail could not be sent";
		$msgArray['SmsFail'] = "Sms is not successfully sent";
		$msgArray['successSms'] = "Sms is successfully send";
		$msgArray['successEmail'] = "Email is successfully send";
		$msgArray['requiredEmail'] = "Email-Address is required";
		$msgArray['noAccess'] = "Don't Have Access Right";
		$msgArray['mapping'] = "mapping is not proper";
		$msgArray['missingField'] = "mapping field is missing";
		$msgArray['isDisplayEnum'] = "is display value is not proper";
		$msgArray['measurementUnitEnum'] = "measurement value is not proper";
		$msgArray['productMenu'] = "Product Menu value is not proper (ok/not)";
		$msgArray['productType'] = "Product Type value is not proper(product/accessories/service)";
		$msgArray['bestBeforeType'] = "Best Before Type value is not proper(day/month/year)";
		$msgArray['notForSale'] = "Not For Sale value is not proper(true/false)";
		$msgArray['taxInclusive'] = "Tax Inclusive value is not proper(inclusive/Exclusive)";
		$msgArray['invalidProductCode'] = "Enter Diffrenet product-name/color/size";
		$msgArray['invalidAmount'] = "Total amount is invalid";
		$msgArray['invalidCategoryName'] = "Brand-Name is invalid";
		$msgArray['invalidGroupName'] = "Category-Name is invalid";
		$msgArray['invalidBranchName'] = "Branch-Name is invalid";
		$msgArray['invalidCompanyName'] = "Company-Name is invalid";
		$msgArray['ExistCompanyName'] = "Company-Name is alredy exists";
		$msgArray['invalidClientName'] = "Client-Name is invalid";
		$msgArray['invalidEntryDate'] = "entry-date is invalid";
		$msgArray['invalidDeliveryDate'] = "delivery-date is invalid";
		$msgArray['contact'] = "contact already exists";
		$msgArray['emailExist'] = "email-address is not exist";
		$msgArray['userLogin'] = "user is not logged-in";
		$msgArray['paymentMode'] = "Enter the proper payment-mode";
		$msgArray['reminderData'] = "Enter the proper reminder-data";
		$msgArray['updateSetting'] = "Please,Update the setting data";
		return $msgArray;
	}
}
