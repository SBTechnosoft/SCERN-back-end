##### Gets Trial-Balance     
            
##### `GET /accounting/balance-sheet/company/{companyId}/`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Balance-Sheet Object
			}

**NOTES:** Provide details of the Balance-Sheet based on the companyId

##### Gets Balance-Sheet pdf-path    
            
##### `GET /accounting/balance-sheet/company/{companyId}/export`
+ Header 
	- Authentication Token
	- "operation" : "pdf",
	- "operation" : "twoSidePdf",
	- "operation" : "excel",
	- "operation" : "twoSideExcel"
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''
			}

**NOTES:** get pdf path 