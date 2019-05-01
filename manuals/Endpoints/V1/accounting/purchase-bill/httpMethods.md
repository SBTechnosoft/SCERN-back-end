##### Creates Purchase-Bill

##### `POST /accounting/purchase-bills/`
+ Header
	- Authentication Token
+ Body

            {
                ... Standard Purchase-Bill Persistable Object
            }

+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `GET /accounting/purchase-bills/company/{companyId}`
+ Header
	- Authentication Token
	- "fromDate":"date"
	- "toDate":"date"
	- "billNumber":"string"
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Purchase-Bill Object
            }
			
##### `GET /accounting/purchase-bills`
+ Header
	- Authentication Token
	- "previousPurchaseId":"int",
	- "nextPurchaseId":"int",
	- "companyId":"int",
	- "operation":"first/last",
	- "purchaseBillId":"int"
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... Standard Purchase-Bill Object
            }	
##### `DELETE /accounting/purchase-bills/{purchaseBillId}`
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
			
