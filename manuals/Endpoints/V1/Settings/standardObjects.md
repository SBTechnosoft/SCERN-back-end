##### Standard Setting Object
			{
				"settingId":int,
				"settingType":string,
				"barcodeWidth": decimal,
                "barcodeHeight": decimal,
				"chequeno": Enum,
				"servicedateNoOfDays": int,
				"paymentdateNoOfDays": int,
				"birthreminderType": Enum,
				"birthreminderTime": Enum,
				"birthreminderNotifyBy": Enum,
				"annireminderType": Enum,
				"annireminderTime": Enum,
				"annireminderNotifyBy": Enum,
				"productColorStatus":Enum,
				"productSizeStatus":Enum,
				"productBestBeforeStatus":Enum,
				"status":Enum,
				"createdAt":timestamp,
				"updatedAt":datetime
			}
##### Standard Setting Persistable Object
			{
                "barcodeWidth": decimal,
                "barcodeHeight": decimal,
				"servicedateNoOfDays": int,
				"paymentdateNoOfDays": int,
				"birthreminderType": Enum,
				"birthreminderTime": Enum,
				"birthreminderNotifyBy": Enum,
				"annireminderType": Enum,
				"annireminderTime": Enum,
				"annireminderNotifyBy": Enum,
				"chequeno": Enum
            }
##### Standard Setting Object
			{
				...standard ledger object
				"remainingAmount":decimal,
				"remainingAmountType":Enum
			}	
##### chequeno Enum
			{
				chequeNoEnable:'enable',
				chequeNoDisable:'disable'
			}
##### reminder type Enum(birthreminderType/annireminderType)
			{
				beforeReminderType : 'before',
				afterReminderType : 'after'
			}

##### reminder time Enum(birthreminderTime/annireminderTime)
			{
				1Hour : '1Hour',
				2Hour : '2Hour',
				4Hour : '4Hour',
				6Hour : '6Hour',
				12Hour : '12Hour',
				24Hour : '24Hour'
			}

##### notify by Enum(birthreminderNotifyBy/annireminderNotifyBy)
			{
				notifyBySms : 'sms',
				notifyByEmail : 'email',
				notifyByBoth : 'both',
				notifyByNone : 'none'
			}

##### status/ProductBestBeforeStatus/ProductColor/ProductSize Enum
			{
				onStatus:'on',
				offStatus:'off'
			}