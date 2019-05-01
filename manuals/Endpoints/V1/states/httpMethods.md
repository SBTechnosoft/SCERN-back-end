##### Creates State

##### `POST /states`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard State Persistable Object
            }
+ Error Message

			{
				... Error Message
			}               
+ Response

            {
                ... HTTP_Status:200
            }
    

##### Get State           
            
##### `GET /states/{stateAbb}`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}   
+ Response

			{
				... Standard State Object
			}

**NOTES:** List all the state in particular stateAbb(ISO_Code)

##### `GET /states`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}   
+ Response 

			{
				... Standard State Object
			} 

**NOTES:** List All the state available in the system



##### Updates State    
       
##### `PATCH /states/{stateAbb}`
+ Header
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}   
+ Body

            {
                ... Standard State Persistable Object
            }
+ Response

			{
				HTTP_Status:200
			}            
            
##### Deletes State    
       
##### `DELETE /states/{stateAbb}`
+ Error Message

			{
				... Error Message
			}   
+ Response

			{
				HTTP_Status:200
			}