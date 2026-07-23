#Filipino Cookbook API


##Description

The Filipino Cookbook API is a RESTful web service developed using PHP, Slim Framework, and MySQL. It provides information about popular Filipino dishes, including its categories, origins, ingredients, and cooking instructions. The API is secured using Bearer Token Authentication to ensure that only authorized users can access its endpoints.



## Features

- View a list of Filipino dishes
- Retrieve food categories
- Retrieve food origins
- Retrieve ingredients
- Retrieve food and ingredient relationships
- Add new foods
- Add new categories
- Add new origins
- Add new ingredients
- Add food-ingredient records
- Bearer Token Authentication
- JSON response format


## Technologies Used

- PHP
- Slim Framework
- MySQL
- Composer
- Apache (XAMPP)
- SQLyog
- Thunder Client / Postman
- Git
- GitHub


## API Endpoints

### Welcome Route
http://localhost/filipino-cookbook-api/public/

### Foods
- GET /api/foods
- POST /api/foods

### Categories
- GET /api/categories
- POST /api/categories

### Origins
- GET /api/origins
- POST /api/origins

### Ingredients
- GET /api/ingredients
- POST /api/ingredients

### Food Ingredients
- GET /api/food_ingredients
- POST /api/food_ingredients

### Food ID
- GET /api/foods/{id}

### Food Name
- GET /api/foods/search/{name}


## Authentication

This API requires a Bearer Token.


Authorization: Bearer dmmmsu-cookbook-token-2026

User Agent: Thunder Client (https://www.thunderclient.com)



## How to Run the Project

1. Install XAMPP.
2. Start Apache and MySQL.
3. Import the database into SQLyog.
4. Place the project inside the `htdocs` folder.
5. Install project dependencies using Composer.
6. Open the project in your browser.
7. Test the API using Thunder Client or Postman.

Base URL:

http://localhost/filipino-cookbook-api/public


## Sample Request

GET

http://localhost/filipino-cookbook-api/public/api/foods

Required Header:

Authorization: Bearer dmmmsu-cookbook-token-2026



## Author

Jun Mar F. Borja - BS Information Technology 4-A - July 21, 2026



## License

This project was developed for educational purposes as part of the GitHub and REST API laboratory activities.


## Added HTML, JPG, CS, & JS