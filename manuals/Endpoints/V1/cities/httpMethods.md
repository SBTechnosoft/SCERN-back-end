##### Creates City

##### `POST /cities`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard City Persistable Object
            }
+ Error Message

			{
				... Error Message
			}              
+ Response

            {
                ... HTTP_Status:200
            }
    

##### Gets Cities           
            
##### `GET /cities/`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... Standard City Object
			}

**NOTES:** List all the city available in the system

##### `GET /cities/{cityId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}  
+ Response 

			{
				... Standard City Object
			} 

**NOTES:** Give only particular city at particular cityId 


##### `GET cities/states/{satateAbb}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}  
+ Response

            {
                ... Standard City Object
            }
            
**NOTES:** List the city at particular stateAbb[ISO_Code] 




##### Updates City    
       
##### `PATCH /cities/{cityId}`
+ Header
	- Authentication Token

+ Body
			{
				... Standard City Persistable Object
			}
+ Error Message

			{
				... Error Message
			}  
+ Response

            {
                ... HTTP_Status:200
            }
            
            
##### Deletes City    
       
##### `DELETE /cities/{cityId}`
+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... HTTP_Status:200
			}