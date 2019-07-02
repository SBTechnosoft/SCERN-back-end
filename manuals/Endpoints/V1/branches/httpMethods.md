##### Creates Branch

##### `POST /branches`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Branch Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                ... HTTP_Status:200
            }
    

##### Gets Branch           
            
##### `GET branches/company/{CompanyId}/`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Branch Object
			}

**NOTES:** List all the branch in particular company 

##### `GET branches/{branchId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response 

			{
				... Standard Branch Object
			} 

**NOTES:** Give only particular branch as per branchId  


##### `GET /branches`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard Branch Object
            }
            
**NOTES:** List all the branch available in the system


##### Updates Branch    
       
##### `PATCH branches/{branchId}`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Branch Persistable Object
            }
+ Error Message

			{
				... Error Message
			} 
            
+ Response

			{
				... HTTP_Status:200
			}            
##### Deletes Branch    
       
##### `DELETE branches/{branchId}`
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... HTTP_Status:200
			}