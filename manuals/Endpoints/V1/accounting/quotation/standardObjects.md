##### Standard Quotation Object
		{
			"quotationBillId":int,
			"companyId":String,
			"entryDate":date,
			"contactNo":string,
			"emailId":string,
			"companyName":string,
			"clientName":string,
			"workno":string,
			"address1":String,
			"address2":String,
			"stateAbb":char,
			"cityId":int,
			"quotationNumber":string,
			"transactionDate":date,
			"total":decimal,
			"totalDiscounttype":Enum,
			"totalDiscount":decimal,
			"extraCharge":decimal,
			"tax":decimal,
			"color":string,
			"frameNo":string,
			"size":string,
			"grandTotal":decimal,
			"remark":string,
			"file":
				{
					{
						{
							"documentId":int,
							"quotationBillId":int,
							...Standard Document Object
							"documentType":string,
							"createdAt":timestamp,
							"updatedAt":datetime
						}
						...
					}
					...
				}
		}
		
##### Standard Bill Persistable Object
		{
			"companyId":String,
			"entryDate":date,
			"contactNo":string,
			"emailId":string,
			"companyName":string,
			"clientName":string,
			"workno":string,
			"address1":String,
			"address2":String,
			"stateAbb":char,
			"cityId":int,
			"quotationNumber":string,
			"transactionDate":date,
			"total":decimal,
			"totalDiscounttype":Enum,
			"totalDiscount":decimal,
			"extraCharge":decimal,
			"tax":decimal,
			"color":string,
			"frameNo":string,
			"size":string,
			"grandTotal":decimal,
			"remark":string,
			"file":
			{
				{
					Image Object
				}
				...
			}
		}

		
##### Standard Status Object
		{
			"statusId":int,
			"status":string,
			"statusType": ... statusType Enum
		}

##### Standard Dispatch Object
		{
			"dispatchInv":json_string,
			"remainingInv": json_string
		}
##### statusType Enum
		[
			'quotation',
			'salesorder',
			'sales',
			'delivery',
			'finalized'
		]