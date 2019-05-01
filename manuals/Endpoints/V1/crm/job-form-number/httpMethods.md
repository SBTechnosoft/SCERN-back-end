##### Creates Job-Form-Number

##### `POST /crm/job-form-number`
- Authentication Token
+ Body

            {
                ... Standard Job-Form-Number Persistable Object
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                ... HTTP_Status:200
            }

##### `GET /crm/job-form-number/`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			} 
+ Response 

			{
				... Standard Job-Form-Number Object
			} 

**NOTES:** List all the Job-Form-Number available in the system

##### `GET /crm/job-form-number/company/{companyId}/latest/`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard Job-Form-Number Object
            }
            
**NOTES:** list the latest Job-Form-Number for particular company id