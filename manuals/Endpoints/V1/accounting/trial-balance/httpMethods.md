##### Gets Trial-Balance     
            
##### `GET /accounting/trial-balance/company/{companyId}/`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Trial-Balance Object
			}

**NOTES:** Provide details of the Trial-Balance based on the companyId

##### Gets Trial-Balance pdf-path    
            
##### `GET /accounting/trial-balance/company/{companyId}/export`
+ Header 
	- Authentication Token
	- "operation":'pdf',
	- "operation":'twoSidePdf',
	- "operation":'excel',
	- "operation":'twoSideExcel'
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''
			}

**NOTES:** get pdf path 