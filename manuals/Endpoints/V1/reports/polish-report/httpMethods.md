##### Gets Polish-Report    
            
##### `GET /reports/polish-report/company/{companyId}/`
+ Header 
	- "authenticationToken":''
	- "fromDate":'',
	- "toDate":''
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Bill Object
			}

**NOTES:** Provide details of the sale-bill based on the companyId,fromDate and toDate

##### Gets Polish-Report Document  
            
##### `GET /reports/polish-report/company/{companyId}/`
+ Header 
	- "authenticationToken":''
	- "fromDate":'',
	- "toDate":'',
	- "operation":'pdf'
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''
			}

**NOTES:** Provide the pdf document path

