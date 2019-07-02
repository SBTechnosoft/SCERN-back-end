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
		
##### `GET /accounting/quotations/status`
+ Header
	- Authentication Token
	- 
+ Error Message

			{
				... Error Message
			} 
+ Response

            {
                ... Standard Status Object
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



##### `POST /accounting/quotations/convert/{quotationBillId}`
+ Header
	- Authentication Token
+ Body

            {
                companyId: int
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                200: OK
            }

##### `POST /accounting/quotations/dispatch/{saleId}`
+ Header
	- Authentication Token
+ Body

            {
                dispatchStatus: string,
				dispatchInv: json,
				remainingInv: json,
				statusId: int
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                200: OK
            }


##### `GET /accounting/quotations/dispatch/{saleId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Dispatch Object
            }


##### `DELETE /accounting/quotations/{Id: quotationId/SalesOrderId}/{dataType: 'quotation'/'order'}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... 200: OK
            }