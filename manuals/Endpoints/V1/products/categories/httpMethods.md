##### Creates Products Category

##### `POST /product-categories`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Product Category Persistable Object
            }
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `POST /product-categories/batch`
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

##### Get Products Category           
            
##### `GET /product-categories/{productCategoryId}/`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}
+ Response

            {
                ... Standard ProductCategory Object
            }
            

##### `GET /product-categories`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}
+ Response

            {
                ... Standard ProductCategory Object
            }
            
**NOTES:** List All the Product Category available in the system

##### Updates a Products Category  
       
##### `POST /product-categories/{productCategoryId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard ProductCategory Persistable Object
            }
            
+ Error Message

			{
				... Error Message
			}     
+ Response

			{
				... HTTP_Status:200
			}
			
##### Deletes Products Category 
       
##### `DELETE /product-categories/{productCategoryId}`
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... HTTP_Status:200
			}