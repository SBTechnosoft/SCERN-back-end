##### Creates Company

##### `POST /companies`

+ Body

            {
                ... Standard Company Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                ... HTTP_Status:200
            }
    
##### Gets Company           
            
##### `GET /companies/{companyId}`

+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard Company Object
            }
**NOTES:** List the company of particular companyId

##### `GET /companies`

+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard Company Object
            }
            

**NOTES:** List All the company available in the system         

##### Updates Company    
       
##### `POST /companies/{companyId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Company Persistable Object
            }
+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... HTTP_Status:200
            }
                
            
##### Deletes Company    
       
##### `DELETE /companies/{companyId}`

+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... HTTP_Status:200
			}