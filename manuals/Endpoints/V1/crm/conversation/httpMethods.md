##### Send Email

##### `POST crm/conversastions/bulk-email`
+ Header
	- Authentication Token
	- "saleId":""
+ Body

            {
                ... Standard Email Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                ... HTTP_Status:200
            }
			
##### Send Sms

##### `POST crm/conversastions/bulk-sms`
+ Header
	- Authentication Token
	
+ Body

            {
                ... Standard Sms Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                ... HTTP_Status:200
            }