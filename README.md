# rest_api

Change the Database configuration inside of the .env file line 16.

Create the database by executing the following commats in a console.
  
php bin/console doctrine:database:create  
php bin/console doctrine:migrations:migrate
  
After that start the server with the following command
php bin/console server:run
  
Open the website with
  localhost:8000
