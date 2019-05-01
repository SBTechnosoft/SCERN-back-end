##### Standard Products Object

        {
            "productId": int,
            "productName": string,
            "productType": Enum,
            "productMenu": Enum,
            "measurementUnit": Enum,
			"color":string,
			"size":string,
			"purchasePrice": decimal,
			"wholesaleMargin": decimal,
			"wholesaleMarginFlat": decimal, 
			"semiWholesaleMargin": decimal,
			"vat": decimal,
			"purchaseCgst":decimal,
			"purchaseSgst":decimal,
			"purchaseIgst":decimal,
			"margin": decimal,
			"marginFlat":decimal,
			"mrp": decimal,
			"igst":decimal,
			"hsn":string,
			"maxSaleQty":int,
			"notForSale":Enum,
			"bestBeforeTime":int,
			"bestBeforeType":Enum,
			"cessFlat":decimal,
			"cessPercentage":decimal,
			"coverImage":{
				... Standard Document Object
			},
			"file":{
				{
					... Standard Document Object
				}
				...
			},
			"createdBy":int,
			"updatedBy":int,
			"deletedBy":int,
			"additionalTax":decimal,
			"minimumStockLevel":int,
			"documentName":string,
			"documentFormat":string,
			"documentPath":string,
			"productDescription":string,
            "productCategory": {
                ... Standard Products Category Object
            },
            "productGroup": {
                ... Standard Products Group Object
            }
            "company": {
                ... Standard Company Object
            },
            "branch": {
                ... Standard Branch Object
            }
			"isDisplay": Enum,
			"createdAt" timestamp,
			"updatedAt": datetime
         }

##### Standard Products Persistable Object

        {
            "productName": string,
            "productType": Enum,
            "productMenu": Enum,
            "measurementUnit": Enum,
			"color":string,
			"size":string,
            "productCategoryId": int,
            "productGroupId": int,
			"purchasePrice": decimal,
			"wholesaleMargin": decimal,
			"wholesaleMarginFlat": decimal,
			"semiWholesaleMargin": decimal,
			"vat": decimal,
			"purchaseCgst":decimal,
			"purchaseSgst":decimal,
			"purchaseIgst":decimal,
			"margin": decimal,
			"marginFlat":decimal,
			"mrp": decimal,
			"igst":decimal,
			"hsn":string,
			"maxSaleQty":int,
			"notForSale":Enum,
			"bestBeforeTime":int,
			"bestBeforeType":Enum,
			"cessFlat":decimal,
			"cessPercentage":decimal,
			"additionalTax":decimal,
			"minimumStockLevel":int,
			"coverImage":{
				... single image file
			},
			"file":{
				{
					... single image file
				}
				...
			},
			"productDescription":string,
            "companyId": int,
            "branchId" : int,
			"isDisplay": Enum,
		}
##### Standard Products-batch Persistable Object
		{
			"productType": Enum,
            "productMenu": Enum,
			"measurementUnit": Enum,
			"purchasePrice": decimal,
			"wholesaleMargin": decimal,
			"wholesaleMarginFlat": decimal,
			"semiWholesaleMargin": decimal,
			"vat": decimal,
			"purchaseCgst":decimal,
			"purchaseSgst":decimal,
			"purchaseIgst":decimal,
			"margin": decimal,
			"marginFlat":decimal,
			"mrp": decimal,
			"igst":decimal,
			"hsn":string,
			"maxSaleQty":int,
			"notForSale":Enum,
			"bestBeforeTime":int,
			"bestBeforeType":Enum,
			"cessFlat":decimal,
			"cessPercentage":decimal,
			"additionalTax":decimal,
			"minimumStockLevel":int,
			"productId":array
		}
##### Standard Product-Trnsaction-Summary Object
		{
			"productTrnSummaryId":int,
			"qty":int,
			 "company": {
                ... Standard Company Object
            },
            "branch": {
                ... Standard Branch Object
            }
			"product":{
				... Standard Products Object
			}
			"createdAt":timestamp,
			"updatedAt":datetime
			
		}		
##### Standard Product Transaction Persistable Object
			{
            	"inventory":
				[
					{
						"qty": decimal,
						"price": decimal,
						"discount":decimal,
						"discountType":Enum,
						"productId":int,
					},
					...
				],
				"transactionDate": date,
				"transactionType":Enum,
				"companyId": int,
				"invoiceNumber":string,
				"billNumber":string,
				"tax":decimal,
				"branchId":int,
				"jfId":int
			}
			
##### Standard Product Transaction Object
			{
            	"inventory":
				[
					{
						"qty": decimal,
						"price": decimal,
						"discount":decimal,
						"discountValue":decimal,
						"discountType":Enum,
						"productId":int,
						"transactionDate": date,
						"transactionType":Enum,
						"companyId": int,
						"invoiceNumber":string,
						"billNumber":string,
						"tax":decimal,
						"branchId":int,
						"jfId":int
					},
					...
				],
				
			}
#####  Is Display Enum
			{
				... Is Display Enum
			}
#####  Discount Type Enum
			{
				"flatType":"flat",
				"perCentageType":"percentage"
			}
##### Standard Products Category Object

        {
            "productCategoryId": int,
            "productCategoryName": string,
            "productCategoryDescription": string,
            "productParentCategoryId": int,
			"isDisplay": Enum,
			"createdAt" datetime,
			"updatedAt": datetime
        }

##### Standard Products Category Persistable Object

        {
            "productCategoryName": string,
            "productCategoryDescription": string,
            "productParentCategoryId": int,
			"isDisplay": Enum,
        }

##### Standard Products Group Object

        {
			"productGroupId": int,
            "productGroupName": string,
            "productGroupDescription": string,
            "productParentGroupId": int,
			"isDisplay":Enum,
			"createdAt" datetime,
			"updatedAt": datetime
        }

##### Standard Products Group Persistable Object

        {
            "productGroupName": string,
            "productGroupDesciption": string,
            "productGroupParentGroupId": int,
			"isDisplay": Enum
        }
		
#####  Is Display Enum
			{
				... Is Display Enum
			}
#####  Measurement Unit Enum
			{
				"type1":"piece",
				"type2":"pair"
			}
##### Transaction Type Enum
			{
				"purchaseTaxType":"'purchase_tax",
				"purchaseType":"purchase"
			}
##### Not For Sale Enum
			{
				"onSale":"on",
				"offSale":"off"
			}
##### Best Before Type Enum
			{
				"beforeDay":"day",
				"beforeMonth":"month"
				"beforeYear":"year"
			}
##### productType Enum
			{
				"productType": "product",
				"acceType": "accessories",
				"serviceType": "service",
			}
##### productMenu Enum
			{
				"okMenu":"ok",
				"notMenu":"not"
			}