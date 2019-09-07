##### Standard Commission Persistable Object
			{
				"userId": Int,
				"commissionStatus": String,
				"commissionType": String,
				"commissionRateType": String,
				"commissionCalcOn": String,
				"commissionRate": Decimal,
				"commissionFor": ... Stringified Commission ID Object
			}

##### Commission ID Object
			{
				Int: Boolean
			}

##### Standard Commission Object
			{
				"commissionId": Int,
				"commissionRate": Decimal,
				"commissionRateType": String,
				"commissionStatus": String,
				"commissionType": String,
				"commissionCalcOn": String,
				"commissionFor": ... Stringified Commission ID Object 
				"createdAt": Date,
				"updatedAt": Date,
				"userId": Int
			}

##### Standard ItemWise Commission Object

			{
				"productCommissionId": Int,
				"productId": Int,
				"companyId": Int,
				"productName": String,
				"mrp": Decimal,
				"commissionRate": String,
				"commissionFromQty": Int,
				"commissionToQty": Int,
				"commissionRateType": String,
				"commissionCalcOn": String,
				"createdAt": Date,
				"updatedAt": Date
			}

##### Itemwise Commission Persistable Object

			{
				"productId": Int,
				"companyId": Int,
				"commisssionFromQty": Decimal,
				"commissionToQty": Decimal,
				"commissionRate": Decimal,
				"commissionRateType": String,
				"commissionCalcOn": String
			}