##### Creates Users

##### `POST /users`
+ Header
	- 'authenticationToken':string


+ Body

            {
                ... Standard User Persistable Object
            }
+ Error Message

			{
				... Error Message
			}              
+ Response

            {
                ... HTTP_Status:200
            }
    

##### Gets Users           
            
##### `GET /users/`
+ Header 
	- 'authenticationToken':string
	- 'companyId':''
	- 'branchId':''
+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... Standard User Object
			}

**NOTES:** List all the User available in the system

##### `GET /users/{userId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}  
+ Response 

			{
				... Standard User Object
			} 

**NOTES:** Give only particular user at particular userId 

##### Updates User    
       
##### `PATCH /users/{userId}`
+ Header
	- Authentication Token

+ Body
			{
				... Standard User Persistable Object
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
       
##### `DELETE /users/{userId}`
+ Error Message

			{
				... Error Message
			}  
+ Response

			{
				... HTTP_Status:200
			}