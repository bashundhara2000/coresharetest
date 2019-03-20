This is a simple poc for google drive sharing app.

To run the app follow the below steps :

	Install the dependencies :
			PHP 7,
			php-mcrypt,
			php-mysql,
			composer

	Run the "composer install" command to install all the dependencies,
	Copy the .env.local to .env file
	Create a mysql database and configure the .env file with the database vales.
	Create a project in developer.google.com for google authentication(select browser based application) and create API credentials and fill the .env file.
	Create new credentials in the same project for server to server communication and save the credentials.json file here.
	Run "php artisan migrate" to create all tables.
	Run "php artisan db:seed" to populate default values.
	Run "php artisan serve" to start the server . 
	Hit the localhost:8000 in your browser.
























f9E2TyGdKLW8x1iSyMuy3h9H
