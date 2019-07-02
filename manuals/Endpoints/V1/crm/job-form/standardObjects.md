##### Standard Job-Form Object
		{
			"jobCardId":int,
			"clientName":string,
            "contactNo": string,
			"emailId": string,
			"address":string,
			"jobCardNumber": string,
			"product":
			[
				{
					"productId": int,
					"productName":string
					"productInformation": string,
					"Qty": decimal,
					"discountType":enum,
					"discount": decimal,
					"price":decimal,
				}
				...
			]		
			"clientId":int,
			"tax":decimal,
			"bankName":string,
			"chequeNo":string,
			"labourCharge":decimal,
			"serviceType": enum,
			"entryDate": date,
			"deliveryDate": date,
			"advance":decimal,
			"total":decimal,
			"paymentMode":enum,
			"state":{
				standard state object
			}
			"city": {
				standard city object
			}
			"company":{
				standard company object
			}
		}

##### Standard Job-Form Persistable Object

         {
			"clientName":string,
            "contactNo": string,
			"emailId": string,
			"address":string,
			"jobCardNumber": string,
			"product":
			[
				{
					"productId": int,
					"productName":string
					"productInformation": string,
					"Qty": decimal,
					"discountType":enum,
					"discount": decimal,
					"price":decimal,
				}
				...
			]		
			"tax":decimal,
			"bankName":string,
			"chequeNo":string,
			"labourCharge":decimal,
			"serviceType": enum,
			"entryDate": date,
			"deliveryDate": date,
			"advance":decimal,
			"total":decimal,
			"paymentMode":enum,
			"stateAbb": int,
			"cityId": int,
			"companyId":int
		}

##### Service Type Enum
			{
				paidType:'paid',
				freeType:'free' 
			}
