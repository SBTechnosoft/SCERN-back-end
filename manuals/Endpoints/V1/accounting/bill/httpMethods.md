##### Creates Bill

##### `POST /accounting/bills/`
+ Header
	- Authentication Token
	- 'salesType':'retail_sales/whole_sales'
	- 'operation':'preprint'
	- 'isSalesOrder':'ok'
+ Body

            {
                ... Standard Bill Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                "documentPath":''
            }

##### `POST /accounting/bills/draft-bill`
+ Header
	- Authentication Token
	- 'saleId':int
+ Body

            {
                ... Standard Bill Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `GET /accounting/bills/company/{companyId}`
+ Header
	- Authentication Token
	- 'salesType':'retail_sales/whole_sales'
	- "fromDate":"date"
	- "toDate":"date"
	- "invoiceNumber":"string"
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Bill Object
            }
			
##### `GET /accounting/bills`
+ Header
	- Authentication Token
	- 'salesType':'retail_sales/whole_sales',
	- "previousSaleId":"int",
	- "nextSaleId":"int",
	- "companyId":"int",
	- "operation":"first/last"
	- "isSalesOrder":"ok"
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Bill Object
            }
			
##### `GET /accounting/bills/draft-bill/{companyId}`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Bill Object
            }
			
##### `POST /accounting/bills/{saleId}/payment`
+ Header
	- Authentication Token
+ Body

            {
                ... Standard Payment Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `POST /accounting/bills/{saleId}`
+ Header
	- Authentication Token
	- "isSalesOrderUpdate":"ok"
+ Body

            {
                ... Standard Bill Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                "documentPath":''
            }
			
##### `DELETE /accounting/bills/{saleId}`
+ Header
	- Authentication Token

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
##### `DELETE /accounting/bills/draft-bill/{saleId}`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
##### `DELETE /accounting/bills/sales-order/{saleId}`
+ Header
	- Authentication Token
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }			
