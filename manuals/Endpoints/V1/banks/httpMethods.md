##### Gets Bank           
            
##### `GET /banks/{bankId}`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Bank Object
			}

**NOTES:** List the bank as per given bank_id 

##### `GET /banks`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Bank Object
			}

**NOTES:** List all the banks

##### `GET /banks/branches/{bankId}`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Bank-Branch Object
			}

**NOTES:** List all the branches of banks
##### `GET /banks/branches`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Bank-Branch Object
			}

**NOTES:** List all the branches
