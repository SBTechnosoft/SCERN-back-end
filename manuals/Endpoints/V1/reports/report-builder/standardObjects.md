##### Standard ReportGroup Object
			{
				"rbGroupCategory": String,
				"rbGroupId": Int,
				"rbGroupName": String
			}
			
##### Standard Table Object
			{
				"id": Int,
				"label": String,,
				"children: ... Stringified Standard Field Object
			}

##### Standard Field Object
			{
				"id": Int,
				"label": String,
				"type": String
			}

#### Preview Header Object
			{
				"reportGroup": ... Standard ReportGroup Object,
				"reportType": String
			}

#### Preview Column Object
			{
				"id": Int,
				"label": String,
				"type": String,
				"table": String
			}

#### Preview Filter Object
			{
				"field": ... Column Object,
				"conditionType": String,
				"filterValue": String
			}

#### Standard Header Object
			{
				"position": String,
				"reportGroupId": Int,
				"reportName": String,
				"reportTitle": String,
				"reportType": String
			}
#### Standard Column Object
			{
				"position": Int,
				"id": Int,
				"label": String
			}

#### Minified Report Object
			{
				"reportId": Int,
				"reportName": String,
				"reportTitle": String,
				"titlePosition": String
			}

#### 