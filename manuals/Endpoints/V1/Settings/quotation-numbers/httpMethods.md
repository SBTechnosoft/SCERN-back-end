##### Creates QuotationNumebrs

##### `POST /settings/quotation-numebrs/`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard QuotationNumber Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                ... HTTP_Status:200
            }
    

##### Gets QuotationNumber           
            
##### `GET /settings/quotation-numbers/{quotationId}/`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... Standard QuotationNumber Object
			}

**NOTES:** List the Quotation Number as per particular Quotation id 

##### `GET /settings/quotation-numbers/`
+ Header
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}  
+ Response 

			{
				... Standard QuotationNumber Object
			} 

**NOTES:** List all the QuotationNumber available in the system


##### `GET /settings/quotation-numbers/company/{companyId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}  
+ Response

            {
                ... Standard QuotationNumber Object
            }
            
**NOTES:** List all the QuotationNumber available in the system

##### `GET /settings/quotation-numbers/company/{companyId}/latest/`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}  
+ Response

            {
                ... Standard QuotationNumber Object
            }
            
**NOTES:** list the latest Quotation Numbers for particular company id

##### Updates quotation-number    
       
##### `PATCH /settings/quotation-numbers/{quotationId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard QuotationNumber Persistable Object
            }       
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }