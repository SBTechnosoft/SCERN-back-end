##### Creates Product

##### `POST /products`
+ Header
	- Authentication Token
	
+ Body

            {
                ... Standard Products Persistable Object
            }
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### `POST /products/batch`
+ Header
	- Authentication Token
	
+ Body
			{
                "data":
				[	
					[0]
						{
							[0]=>
							[1]=>
							...
						}
					...
				]
				"mapping":
				[
					[0]=>
					[1]=>
					...
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
+ Response

            {
                [
					{
						Array of Error With Data
					}
					..
				]
            }

##### `POST /products/inward`
+ Header
	- Authentication Token


+ Body
			[
				{
					... Standard Products Transaction Persistable Object
				}
			]
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }			

##### `POST /products/outward`
+ Header
	- Authentication Token


+ Body

            {
                ... Standard Products Transaction Persistable Object
            }
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
			
##### Gets Products           
            
##### `GET products/company/{CompanyId?}/branch/{branchId?}`
+ Header 
	- Authentication Token
	
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... Standard Product Object
			}

**NOTES:** List all the product in particular company/&branch

##### `GET products/company/{companyId}`
+ Header 
	- Authentication Token
	- 'productName':'',
	- 'color':'',
	- 'size':'',
	- 'productCategoryId':'',
	- 'productGroupId':'',
	- 'salesType':'retail_sales/whole_sales',
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				{
					... Standard Product Object
				}
				...
			}

**NOTES:** List the product as per given parameter in header and companyId(searching and price-list)

##### `GET products/company/{companyId}/priceList`
+ Header 
	- Authentication Token
	- 'operation':'pdf/excel',
	- 'productCategoryId':''
	- 'productGroupId':''
	- 'salesType':'retail_sales/whole_sales'
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''			
			}

**NOTES:** List the product-trn data as per given parameter in header and companyId (price-list)

##### `GET products/company/{companyId}/transaction/details`
+ Header 
	- Authentication Token
	- 'operation':'pdf/excel'
	- 'fromDate':''
	- 'toDate':''
	- 'productId':''
	- 'productCategoryId':''
	- 'productGroupId':''
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				"documentPath":''			
			}

**NOTES:** List the product-trn data as per given parameter in header and companyId (stock-register)

##### `GET products/company/{companyId}/stock-summary`
+ Header 
	- 'authenticationToken':''
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				 ... Standard Product-Trnsaction-Summary Object		
			}

**NOTES:** List the product-trn-summary data as per given companyId (stock-register-summary)

##### `GET products/company/{companyId}/transaction`
+ Header 
	- 'authenticationToken':''
	- 'fromDate':''
	- 'toDate':''
	- 'productId':''
	- 'productGroupId':''
	- 'productCategoryId':''
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				[
					... Standard Product Transaction Object
				]
				...
			}

**NOTES:** List the product-transaction data as per given data

##### `GET /products/{productId}`
+ Header
	- 'authenticationToken':''

+ Error Message

			{
				... Error Message
			}
+ Response 

			{
				... Standard Product Object
			} 

**NOTES:** Give only particular product from productId  

##### `GET /products`
+ Header
	- 'authenticationToken':''
	- 'productCode':'String'
+ Error Message

			{
				... Error Message
			}
+ Response

            {
                ... Standard Products Object
            }
            
**NOTES:** List All the products available in the system

##### `GET /products`
+ Header
	- 'authenticationToken':''
	- 'jfId':''
+ Error Message

			{
				... Error Message
			}
+ Response

            {
                ... Standard Product Transaction Object
            }
            
**NOTES:** List All the product-transaction data available as per given jfId in the system

##### Updates Products    
       
##### `POST /products/{productId}`
+ Header
	- 'authenticationToken':''


+ Body

            {
                ... Standard Products Persistable Object
            }
+ Error Message

			{
				... Error Message
			}            
+ Response

			{
				... HTTP_Status:200
			}

##### Updates batch-Products 
##### `POST /products/batch-update`
+ Header
	- 'authenticationToken':''

+ Body

            {
                ... Standard Products-batch Persistable Object
            }
+ Error Message

			{
				... Error Message
			}            
+ Response

			{
				... HTTP_Status:200
			}       

##### Deletes Products    
       
##### `DELETE /products/{productId}`
+Header
	- 'authenticationToken':''
+ Error Message

			{
				... Error Message
			}
+ Response

			{
				... HTTP_Status:200
			}