##### Creates Ledgers

##### `POST /accounting/ledgers/`
+ Header
	- Authentication Token

+ Body

            {
                ... Standard Ledger Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Ledger Object
            }
    


##### Gets Ledgers           
            
##### `GET /accounting/ledgers/{ledgerId}/`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Ledger Object
			}

**NOTES:** Provide details of the Ledger based on the Ledger Number

##### `GET /accounting/ledgers/`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Ledger Object
			}

**NOTES:** List all the ledger

##### `GET /accounting/ledgers/{ledgerId}/transactions`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Ledger Transaction Object
			}



##### `GET /accounting/ledgers/ledgerGrp/{ledgerGrpId}`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Ledger Object
			}

**NOTES:** List all the ledger as per the given ledger group id

##### `GET /accounting/ledgers/company/{companyId}`
+ Header 
	- Authentication Token
	- "fromDate":'date'
	- "toDate":'date'
	- 'type':'retail_sales'
	- 'type':'whole_sales'
	- 'type':'all'
	- 'type':'purchase'
	
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Ledger Transaction Object
			}

**NOTES:** List all the ledger as per the given company id

##### `GET /accounting/ledgers/company/{companyId}`
+ Header 
	- Authentication Token
	-'ledgerGroup':'[]'
	-'ledgerName':''
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				[
					{
						... Standard Ledger Object
					}
					...
				]
				...
			}

**NOTES:** List all the ledger as per the given company id and ledgerGroup-id

##### Updates Ledgers    
       
##### `PATCH /accounting/ledgers/{ledgerId}`
+ Header
	- Authentication Token

+ Body
			{
                ... Standard Ledger Persistable Object
            }

+ Error Message

			{
				... Error Message
			}
+  Response
			{
				... HTTP_Status:200
			}
##### Deletes Ledger    
       
##### `DELETE /accounting/ledgers/{ledgerId}`
+ Error Message

			{
				... Error Message
			}
+  Response
			{
				... HTTP_Status:200
			}