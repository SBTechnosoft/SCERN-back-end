##### Create Settings

##### `POST /settings/`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Setting Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }
**NOTES:** Insert settings Data    

##### Get Settings

##### `GET /settings/`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}             
+ Response

            {
              ... Standard Setting Object
			}
**NOTES:** Insert settings Data 

##### update Settings           
            
##### `PATCH /settings/`
+ Header 
	- Authentication Token
	
+ Body

            {
                ... Standard Setting Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... HTTP_Status:200
            }
**NOTES:** Update settings Data

##### get remaining-payment Settings           
            
##### `GET /settings/payment`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
               ... Standard Payment-Data Object
            }
**NOTES:** get remaining-payment Data 
