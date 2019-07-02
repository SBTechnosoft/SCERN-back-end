##### Standard Purchase-Bill Object
			{
				"purchaseId":int,
				"productArray":string,
				"billNumber":string,
				"total":decimal,
				"tax":decimal,
				"grandTotal":decimal,
				"transactionType":Enum,
				"billType":Enum,
				"paymentMode":Enum,
				"bankName":string,
				"checkNumber":string,
				"totalDiscountType":Enum,
				"totalDiscount":decimal,
				"advance":decimal,
				"extraCharge":decimal,
				"balance":decimal,
				"remark":string,
				"transactionDate":datetime,
				"entryDate":datetime,
				"createdAt":timestamp,
				"updatedAt":datetime,
				"jf_id":int,
				"vendor":{
					... standard ledger object
				}
				"company":{
					... standard company object
				},
				"file":
				{
					{
						{
							...Standard Document Object
							"purchaseId":int,
							"documentType":string,
							"createdAt":timestamp,
							"updatedAt":datetime
						}
						...
					}
					...
				}
            }
			
##### Standard Purchase-Bill Persistable Object
			{
				"companyId":int,
            	"vendorId":int,
				"productArray":string,
				"billNumber":string,
				"total":decimal,
				"tax":decimal,
				"grandTotal":decimal,
				"transactionType":Enum,
				"paymentMode":Enum,
				"bankName":string,
				"checkNumber":string,
				"totalDiscountType":Enum,
				"totalDiscount":decimal,
				"advance":decimal,
				"extraCharge":decimal,
				"balance":decimal,
				"remark":string,
				"transactionDate":datetime,
				"entryDate":datetime,
				"createdAt":timestamp,
				"updatedAt":datetime,
				"file":
				{
					{
						Image Object
					}
					...
				}
				"scanFile":
				{
					{
						Base64 String
					}
					...
				}
            }


##### Payment Mode Enum
			{
				"cashPayment":'cash',
				"bankPayment":'bank',
				"cardPayment":'card'
			}

##### Bill Type Enum
			{
				"purchaseType":'purchase',
				"purchaseBillType":'purchase_bill'
			}
##### Transaction Type Enum
			{
				"purchaseTaxType":"purchase_tax",
				"purchaseType":"purchase"
			}