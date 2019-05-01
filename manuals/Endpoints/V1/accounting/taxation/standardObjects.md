##### Standard Sale-Tax Object
			{
				"saleId":int,
				"invoiceNumber":string,
				"salesType":string,
				"total":decimal,
				"totalDiscounttype":Enum,
				"totalDiscount":decimal,
				"client":{
					... standard client object
				},
				"company":{
					... standard company object
				},
				"product":{
					{
						"color": string,
						"size": string,
						"frameNo": string,
						"qty": decimal,
						"price": decimal,
						"amount": decimal,
						"discount":decimal,
						"discountType":Enum,
						"productId":int,
						"productName":string,
						"product":{
							... standard product object
						},
					}
				},
				"entryDate":date,
				"advance":decimal,
				"balance":decimal,
				"extraCharge":decimal,
				"refund":decimal,
				"tax":decimal,
				"grandTotal":decimal,
				"additionalTax":decimal
            }
##### Standard Purchase-Tax Object
			{
				"billNumber":string,
				"entryDate":date,
				"transactionType":string,
				"total":decimal,
				"transactionDate":date,
				"tax":decimal,
				"grandTotal":decimal,
				"additionalTax":decimal,
				"ledger":{
					... standard ledger object
				},
				"company":{
					... standard company object
				},
				"product":{
					{
						"color": string,
						"size": string,
						"frameNo": string,
						"qty": decimal,
						"price": decimal,
						"amount": decimal,
						"discount":decimal,
						"discountType":Enum,
						"productId":int,
						"productName":string,
						"product":{
							... standard product object
						},
					}
				},
            }

##### Standard Purchase-Detail Object
			{
				"billNumber":string,
				"transactionType":string,
				"total":decimal,
				"clientName":string,
				"transactionDate":date,
				"tax":decimal,
				"grandTotal":decimal,
				"additionalTax":decimal
            }
			
##### Standard Stock-Detail Object
			{
				"productId":int,
				"productName":string,
				"hsn":string,
				"openingStockQty":int,
				"openingStockAmount":decimal,
				"purchaseQty":int,
				"purchaseAmount":decimal,
				"totalQty":int,
				"totalAmount":decimal,
				"salesQty":int,
				"salesAmount":decimal,
				"closingStockQty":int,
				"closingStockAmount":decimal
            }

##### Standard Income-Expense Object
			{
				"tradingAmount":decimal,
				"profitLossAmount":decimal,
				"balancesheetAmount":decimal,
				
            }