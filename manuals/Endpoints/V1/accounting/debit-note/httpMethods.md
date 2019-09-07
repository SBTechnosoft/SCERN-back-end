##### Debit Note

##### `POST /accounting/debit-notes/{purchaseId}`
+ Header
	- Authentication Token
+ Body

			{
				"entryDate": String,
				"transactionDate": String,
				"inventory": [
					... Debit Table Object,
					...
				],
				"purchaseId": Int,
				"billNumber": String,
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