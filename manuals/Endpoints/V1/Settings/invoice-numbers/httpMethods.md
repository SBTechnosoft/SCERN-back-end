##### Creates InvoiceNumbers

##### `POST /settings/invoice-numbers/`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard InvoiceNumber Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }
    

##### Gets InvoiceNumber           
            
##### `GET /settings/invoice-numbers/{invoceId}/`
+ Header 
	- Authentication Token
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard InvoiceNumber Object
			}

**NOTES:** List the invoice number as per particular invoice id 

##### `GET /settings/invoice-numbers/`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			} 
+ Response 

			{
				... Standard InvoiceNumber Object
			} 

**NOTES:** List all the invoice-number available in the system


##### `GET /settings/invoice-numbers/company/{companyId}`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard InvoiceNumber Object
            }
            
**NOTES:** List all the invoice-number available in the system

##### `GET /settings/invoice-numbers/company/{companyId}/latest/`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard InvoiceNumber Object
            }
            
**NOTES:** list the latest invoice numbers for particular company id

##### Updates invoice-number    
       
##### `PATCH /settings/invoice-numbers/{invoiceId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Template Persistable Object
            }       
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }