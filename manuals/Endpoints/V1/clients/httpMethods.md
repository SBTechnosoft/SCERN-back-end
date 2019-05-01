##### Creates Client

##### `POST /clients`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Client Persistable Object
            }
            
+ Response

            {
                ... Standard Client Object
            }
    

##### Get Client
            
##### `GET /clients/{clientId}`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Client Object
			}

**NOTES:** List all the client in particular client-id

##### Get Client
            
##### `GET /clients`
+ Header 
	- Authentication Token
	- "contactNo":"",
	- "clientName":"",
	- "invoiceNumber":"",
	- "jobCardNumber":"",
	- "invoiceFromDate":"",
	- "invoiceToDate":"",
	- "jobCardFromDate":"",
	- "jobCardToDate":"",
	- "emailId":"",
	- "address":"",
	- "professionId":""
	- "operation":"birthDate"
	- "operation":"anniversaryDate"
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Client Object
			}

**NOTES:** List all the client
##### `POST /clients/{clientId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Client Persistable Object
            }
+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... HTTP_Status:200
            }
              