##### Standard Email Persistable Object
		{
			"conversationId":int,
			"emailId":string,
			"ccEmailId":string,
			"bccEmailId":string,
			"contactNo":string,
			"subject":string,
			"conversation":text,
			"file":
				{
					... File Object
				}
			"client":
				{
					{
						"clientId":int,
					}
					...
				}
			"companyId":int,
			"branchId":int,
		}
##### Standard Sms Persistable Object
		{
			"conversationId":int,
			"contactNo":string,
			"subject":string,
			"conversation":text,
			"file":
				{
					... File Object
				}
			"client":
				{
					{
						"clientId":int,
					}
					...
				}
			"companyId":int,
			"branchId":int,
		}
		