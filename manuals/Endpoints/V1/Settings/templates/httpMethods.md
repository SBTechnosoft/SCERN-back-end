##### Creates Template

##### `POST /settings/templates/`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Template Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }
    
##### Gets Template           
            
##### `GET /settings/templates/{templateId}`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Template Object
			}

**NOTES:** List all the template as per given template_id 

##### `GET /settings/templates`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Template Object
			}

**NOTES:** List all the templates 

##### `GET /settings/templates/company/{companyId}`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			} 
+ Response

			{
				... Standard Template Object
			}

**NOTES:** List all the templates as per given company-id

##### Updates Template    
       
##### `PATCH /settings/templates/{templateId}`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Template Persistable Object
            }       
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }