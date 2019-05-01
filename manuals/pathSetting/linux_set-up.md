## composer installation
	
**NOTES:**
##### composer link : http://www.cyberciti.biz/faq/how-to-install-composer-on-debian-ubuntu-linux-server/
	
#####if you are using Ubuntu Linux 16.04 LTS or newer and want to use PHP 7.x, run: #####
	- $ sudo apt install curl php7.0-cli git 
	
####install composer on Debian or Ubuntu Linux in /usr/local/bin/ directory as follows####
	- $ curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
 
####Verify composer####
	- $ composer OR $ /usr/local/bin/composer
	
####install dependencies defined in composer.json file####
	- $ composer install
	
OR
**NOTES:** 
#####composer link : http://tecadmin.net/install-laravel-framework-on-ubuntu/#
	
	- $ curl -sS https://getcomposer.org/installer | php
	- $ sudo mv composer.phar /usr/local/bin/composer	
	- $ sudo chmod +x /usr/local/bin/composer

## laravel installation##
	
	- cd /var/www/html
	- sudo composer create-project laravel/laravel your-project --prefer-dist
	
**NOTES:** 
##### laravel link : https://www.howtoforge.com/tutorial/install-laravel-on-ubuntu-for-apache/

For Cron-Job(background running process)
Starting the Laravel Scheduler
To start the scheduler itself, we only need to add one cron job on the server (using the crontab -e command), which executes php /path/to/artisan schedule:runevery minute in the day:

* * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1


Start :
1 Step : login your server by ssh by this command => ssh username@ip_address
2 Step : Now first check your php version in your server. by this command => php -v
3 Step : command for set cron job in liver => crontab -e

NOTE : For Ex. your php version is 7.1 in your live server and your laravel project host in this directory /var/www/html. then you should be set like that valye in open cron file.
* * * * * /usr/bin/php7.1 /var/www/html/artisan schedule:run 1>> /dev/null 2>&1
	
	
	