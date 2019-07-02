##### Creates Documents (generate pdf)

##### `POST /documents/bill`
+ Header
	- Authentication Token
	

+ Body

            {
                "saleId":''
            }
+ Error Message

			{
				... Error Message
			}             
+ Response

            {
                "documentPath":''
            }
			
##### Creates Documents (generate pdf)

##### `DELETE /documents/{documentId}`
+ Header
	- Authentication Token
    - type: 'sale-bill/purchase-bill'
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
                ... HTTP_Status:200
            }
