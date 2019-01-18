<?php
namespace ERP\Core\Settings\Entities;
/**
 *
 * @author Reema Patel<reema.p@siliconbrain.in>
 */
class ReminderEnum 
{
    public function reminderTypeEnumArrays()
	{
		$enumArray = array();
		$enumArray['beforeReminderType'] = "before";
		$enumArray['afterReminderType'] = "after";
		return $enumArray;
	}

	public function reminderTimeEnumArrays()
	{
		$enumArray = array();
		$enumArray['1Hour'] = "1 hour";
		$enumArray['2Hour'] = "2 hour";
		$enumArray['4Hour'] = "4 hour";
		$enumArray['6Hour'] = "6 hour";
		$enumArray['12Hour'] = "12 hour";
		$enumArray['24Hour'] = "24 hour";
		return $enumArray;
	}

	public function notifyByEnumArrays()
	{
		$enumArray = array();
		$enumArray['notifyBySms'] = "sms";
		$enumArray['notifyByEmail'] = "email";
		$enumArray['notifyByBoth'] = "both";
		$enumArray['notifyByNone'] = "none";
		return $enumArray;
	}
}