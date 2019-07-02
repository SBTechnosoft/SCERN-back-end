##### Gets Cash-Flow     
            
##### `GET /accounting/cash-flow/company/{companyId}/`
+ Header 
	- Authentication Token
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard cash-flow Object
			}

**NOTES:** Provide details of the Cash-Flow based on the companyId 

##### Gets Cash-Flow pdf/excel-path    
            
##### `GET /accounting/cash-flow/company/{companyId}/export`
+ Header 
	- Authentication Token
	- "operation":'pdf',
	- "operation":'twoSidePdf',
	- "operation":'excel',
	- "operation":'twoSideExcel',
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''
			}

**NOTES:** get pdf/excel path 