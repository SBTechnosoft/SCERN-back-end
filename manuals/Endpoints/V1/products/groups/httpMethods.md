##### Creates Products Group

##### `POST /product-groups`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Product Group Persistable Object
            }
+ Error Message

			{
				... Error Message
			}              
+ Response

            {
                ... HTTP_Status:200
            }
    
##### `POST /product-groups/batch`
+ Header
	- Authentication Token


+ Body

            {
                "data":
				[	
					[0]
						{
							[0]=>
							[1]=>
							...
						}
					...
				]
				"mapping":
				[
					[0]=>
					[1]=>
					...
				]
				
            }
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
+ Response

            {
                [
					{
						Array of Error With Data
					}
					..
				]
            }			
##### Get Products Group           
            
##### `GET /product-groups/{productGroupId}/`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}  
+ Response

            {
                ... Standard Product Group Object
            }
            
##### `GET /product-groups`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}  
+ Response

            {
                ... Standard Product Group Object
            }
            
**NOTES:** List all the product group available in the system

##### Updates Products Group  
       
##### `PATCH /product-groups/{productGroupId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Product Group Persistable Object
            }
+ Error Message

			{
				... Error Message
			}              
 + Response

			{
				... HTTP_Status:200
			}
           
##### Deletes Products Group
       
##### `DELETE /product-groups/{productGroupId}`
+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... HTTP_Status:200
			}

