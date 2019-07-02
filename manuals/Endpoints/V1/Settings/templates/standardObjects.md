##### Standard Template Object

            {
                "templateId": int,
                "templateName":String,
                "templateBody":longtext,
                "templateType":Enum,
				"createdAt":TimeStamp,
				"updatedAt":datetime,
				"company":
				{
					... Standard Company Object
				}
            }
            
##### Standard Template Persistable Object
			{
            	"templateName":String,
                "templateBody":longtext,
                "templateType":Enum,
				"companyId":int
            }
##### Template type Enum
			{
				generalTemplate:'general', 
				invoiceTemplate:'invoice', 
				quotationTemplate:'quotation',
				blankTemplate:'blank',
				jobCardTemplate:'job_card',
				paymentTemplate:'payment',
				receiptTemplate:'receipt',
				emailNewOrderTemplate :'email_newOrder', 
				emailDuePaymentTemplate:'email_duePayment',
				emailBirthDayTemplate:'email_birthDay',
				emailAnniTemplate:'email_anniversary',
				smsNewOrderTemplate:'sms_newOrder',
				smsDuePaymentTemplate:'sms_duePayment',
				smsBirthDayTemplate:'sms_birthDay',
				smsAnniTemplate:'sms_anniversary',
			}
