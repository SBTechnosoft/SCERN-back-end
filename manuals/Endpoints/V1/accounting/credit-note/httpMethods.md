##### Credit Note

##### `POST /accounting/credit-notes/{saleId}`
+ Header
	- Authentication Token
+ Body

			{
				"entryDate": String,
				"transactionDate": String,
				"inventory": [
					... Credit Table Object,
					...
				],
				"saleId": Int,
				"invoiceNumber": String,
				"total": Decimal,
				"remark": String,
				"companyId": Int
			}
+ Error Message

			{
				... Error Message
			}            
+ Response

            {
				... HTTP Status 200
			}