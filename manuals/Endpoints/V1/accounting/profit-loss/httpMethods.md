##### Gets Profit-Loss     
            
##### `GET /accounting/profit-loss/company/{companyId}/`
+ Header 
	- Authentication Token
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Profit-Loss Object
			}

**NOTES:** Provide details of the Profit-Loss based on the companyId 

##### Gets Profit-Loss pdf-path    
            
##### `GET /accounting/profit-loss/company/{companyId}/export`
+ Header 
	- Authentication Token
	- "operation":'excel',
	- "operation":'twoSideExcel',
	- "operation":'twoSidePdf',
	- "operation":'pdf'
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''
			}

**NOTES:** get pdf path 