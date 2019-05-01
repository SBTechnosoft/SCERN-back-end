##### Creates Quotation

##### `POST /accounting/quotations/`
+ Header
	- Authentication Token
+ Body

            {
                ... Standard Quotation Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                "documentPath":''
            }
			
##### `GET /accounting/quotations/`
+ Header
	- Authentication Token
	- "quotationNumber":"string",
	- "previousQuotationId":"int",
	- "nextQuotationId":"int",
	- "companyId":"int",
	- "operation":"first/last"
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Quotation Object
            }

##### `POST /accounting/quotations/{quotationBillId}`
+ Header
	- Authentication Token
+ Body

            {
                ... Standard Quotation Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                "documentPath":''
            }