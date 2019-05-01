##### Creates Journals

##### `POST /accounting/journals/`
+ Header
	- Authentication Token
	- 'type':'sales'
	- 'type':'payment'
	- 'type':'receipt'
	- 'type':'purchase'
+ Body

            {
				[
					{
						... Standard Journals Persistable Object,
						... Standard Inventory Persistable Object
						... Standard Document Persistable Object
					}
				]
			}

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `POST /accounting/journals/`
+ Header
	- Authentication Token
	- 'type':'purchase'
+ Body

            {
				[
					{
						... Standard Journals Purchase Persistable Object,
						... Standard Inventory Persistable Object
						... Standard Document Persistable Object
					}
				]
			}

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### Gets Journals

##### `GET /accounting/journals/company/{companyId}`
+ Header
	- Authentication Token
	- "fromDate":"date"
	- "toDate":"date"
	- "journalType":'special_journal',
	- "journalType":'payment',
	- "journalType":'receipt'
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Journal Object
            }
			
##### `GET /accounting/journals/company/{companyId}`
+ Header
	- Authentication Token
	- 'type':'sales'
	- 'jfId':'int'
	
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
				'journal':
				[
					{
						... Standard Journal Object
					},
					...
				]
				'productTransaction':
				[
					{
						... Standard Product Transaction Object
					},
					...
				]
				'document':
				[
					{
						... Standard Document Object
					},
				]
			}
**NOTES:** Provide details of the journal and product_trn based on the jornal_folio id

##### `GET /accounting/journals/company/{companyId}`
+ Header
	- Authentication Token
	- 'type':'purchase'
	- 'jfId':'int'
	
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
				'journal':
				[
					{
						... Standard Journal Object
					},
					...
				]
				'productTransaction':
				[
					{
						... Standard Product Transaction Object
					},
					...
				]
				'document':
				[
					{
						... Standard Document Object
					},
				]
				'clientName':string,
				'extraCharge':decimal,
				'advance':decimal,
				'balance':decimal
			}
**NOTES:** Provide details of the journal and product_trn based on the jornal_folio id

##### `GET /accounting/journals/{journalId}/`
+ Header
	- Authentication Token
	- 
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
				[
					{
						... Standard Journal Object
					}
					...
				]
				
            } 
##### `GET /accounting/journals/next/`
+ Header 
	- Authentication Token

+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard JournalFolioId Object
			}

**NOTES:** provide next increment journal folio id(jf_id)

##### update journals
##### `POST /accounting/journals/{jfId}`
+ Header
	- Authentication Token
	- 'type':'sales'
	- 'type':'payment'
	- 'type':'receipt'
+ Body

            {
				[
					{
						... Standard Journals Persistable Object,
						... Standard Inventory Persistable Object
						
					}
				]
			}

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `POST /accounting/journals/{jfId}`
+ Header
	- Authentication Token
	- 'type':'purchase'
+ Body

            {
				[
					{
						... Standard Journals Purchase Persistable Object,
						... Standard Inventory Persistable Object
						
					}
				]
			}

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }