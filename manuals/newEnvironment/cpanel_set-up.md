##### New Environment cPanel Setup
	
	- extract back-end and front-end folders
	- point front end URL to front-end folder
	- point back end API URL to back-end/public folder

##### Fix Basic Settings
	- add front-end url in front-end/app/js/controller/constants.js @hostFrontUrl constants if it doesn't exists
	- add back-end url in front-end/app/js/controller/constants.js @hostUrl constants if it doesn't exists
	- update Database credentials in back-end/config/database.php @mysql



##### reCaptcha Settings
	- create reCaptcha v2 from Google
	- add site key in front-end/app/js/controller/constants.js @googleSiteKey constants if it doesn't exists

##### Applying constants
	
	- change $rootScope.erpPath in front-end/app/app.js for Ex. hostUrl.localhost
	- change $rootScope.templateCompanyLogo in front-end/app/app.js too replace hostUrl.localhost with your given constants key

**NOTES:**
 
	- keep key name same for one environment in all constants