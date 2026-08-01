<?php

require __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Create Slim App
$app = AppFactory::create();
$app->setBasePath('/filipino-cookbook-api/public');

// Enable JSON responses
$app->addBodyParsingMiddleware();

$config = require __DIR__ . '/../config.php';

// Database Connection
$host = $config['db_host'];
$dbname = $config['db_name'];
$user = $config['db_user'];
$pass = $config['db_pass'];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]));
}

// API Token
$validToken = $config['api_token'];

// Function to validate Bearer Token
function checkToken($request, $validToken)
{
    $authHeader = $request->getHeaderLine("Authorization");

    if ($authHeader !== "Bearer " . $validToken) {
        http_response_code(401);

        echo json_encode([
            "status" => "error",
            "message" => "Unauthorized access. Valid API token is required."
        ]);

        exit;
    }
}



// Welcome Route
$app->get('/', function (Request $request, Response $response) {

    $data = [
        "message" => "Welcome to the Secured Filipino Cookbook API",
        "note" => "Use a valid Bearer token to access /api endpoints."
    ];

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type', 'application/json');
});





// GET /api/foods
$app->get('/api/foods', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $sql = "
        SELECT
            f.food_id,
            f.food_name,
            c.category_name,
            o.origin_name,
            f.instructions
        FROM foods f
        JOIN categories c ON f.category_id = c.category_id
        JOIN origins o ON f.origin_id = o.origin_id
    ";

    $stmt = $pdo->query($sql);
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($foods as &$food) {

        $ingredientSql = "
            SELECT i.ingredient_name
            FROM ingredients i
            JOIN food_ingredients fi
            ON i.ingredient_id = fi.ingredient_id
            WHERE fi.food_id = ?
        ";

        $ingredientStmt = $pdo->prepare($ingredientSql);
        $ingredientStmt->execute([$food['food_id']]);

        $food['ingredients'] = $ingredientStmt->fetchAll(PDO::FETCH_COLUMN);
    }

    $response->getBody()->write(json_encode($foods));

    return $response->withHeader('Content-Type', 'application/json');
});






// POST /api/foods
$app->post('/api/foods', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $data = $request->getParsedBody();

    // Insert the food
    $stmt = $pdo->prepare("
        INSERT INTO foods
        (food_name, category_id, origin_id, instructions)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['food_name'],
        $data['category_id'],
        $data['origin_id'],
        $data['instructions']
    ]);

    // Get the new food_id
    $food_id = $pdo->lastInsertId();

    // Insert ingredients
    if (!empty($data['ingredient_ids'])) {

        $stmt2 = $pdo->prepare("
            INSERT INTO food_ingredients(food_id, ingredient_id)
            VALUES(?, ?)
        ");

        foreach ($data['ingredient_ids'] as $ingredient_id) {
            $stmt2->execute([$food_id, $ingredient_id]);
        }
    }

    $response->getBody()->write(json_encode([
        "status" => "success",
        "message" => "Food added successfully."
    ]));

    return $response
        ->withStatus(201)
        ->withHeader('Content-Type', 'application/json');
});




// GET /api/categories
$app->get('/api/categories', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $stmt = $pdo->query("SELECT * FROM categories");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type', 'application/json');
});




// POST /api/categories
$app->post('/api/categories', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $data = $request->getParsedBody();

    $stmt = $pdo->prepare("
        INSERT INTO categories(category_id, category_name)
        VALUES(?, ?)
    ");

    $stmt->execute([
        $data['category_id'],
        $data['category_name']
    ]);

    $response->getBody()->write(json_encode([
        "status"=>"success",
        "message"=>"Category added successfully."
    ]));

    return $response->withHeader('Content-Type','application/json');
});




// GET /api/origins
$app->get('/api/origins', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $stmt = $pdo->query("SELECT * FROM origins");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type','application/json');
});




// POST /api/origins
$app->post('/api/origins', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $data = $request->getParsedBody();

    $stmt = $pdo->prepare("
        INSERT INTO origins(origin_id, origin_name)
        VALUES(?, ?)
    ");

    $stmt->execute([
        $data['origin_id'],
        $data['origin_name']
    ]);

    $response->getBody()->write(json_encode([
        "status"=>"success",
        "message"=>"Origin added successfully."
    ]));

    return $response->withHeader('Content-Type','application/json');
});




// GET /api/ingredients
$app->get('/api/ingredients', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $stmt = $pdo->query("SELECT * FROM ingredients");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type','application/json');
});




// POST /api/ingredients
$app->post('/api/ingredients', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $data = $request->getParsedBody();

    $stmt = $pdo->prepare("
        INSERT INTO ingredients(ingredient_id, ingredient_name)
        VALUES(?, ?)
    ");

    $stmt->execute([
        $data['ingredient_id'],
        $data['ingredient_name']
    ]);

    $response->getBody()->write(json_encode([
        "status"=>"success",
        "message"=>"Ingredient added successfully."
    ]));

    return $response->withHeader('Content-Type','application/json');
});




// GET /api/food_ingredients
$app->get('/api/food_ingredients', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $stmt = $pdo->query("SELECT * FROM food_ingredients");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response->getBody()->write(json_encode($data));

    return $response->withHeader('Content-Type','application/json');
});




// POST /api/food_ingredients
$app->post('/api/food_ingredients', function (Request $request, Response $response) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $data = $request->getParsedBody();

    $stmt = $pdo->prepare("
        INSERT INTO food_ingredients(food_id, ingredient_id)
        VALUES(?, ?)
    ");

    $stmt->execute([
        $data['food_id'],
        $data['ingredient_id']
    ]);

    $response->getBody()->write(json_encode([
        "status"=>"success",
        "message"=>"Food ingredient added successfully."
    ]));

    return $response->withHeader('Content-Type','application/json');
});



// GET /api/foods/{id}
$app->get('/api/foods/{id}', function (Request $request, Response $response, $args) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $id = $args['id'];

    $stmt = $pdo->prepare("
        SELECT
            f.food_id,
            f.food_name,
            c.category_name,
            o.origin_name,
            f.instructions
        FROM foods f
        JOIN categories c ON f.category_id = c.category_id
        JOIN origins o ON f.origin_id = o.origin_id
        WHERE f.food_id = ?
    ");

    $stmt->execute([$id]);
    $food = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$food) {
        $response->getBody()->write(json_encode([
            "status" => "error",
            "message" => "Food not found"
        ]));

        return $response
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json');
    }

    $stmt = $pdo->prepare("
        SELECT ingredient_name
        FROM ingredients i
        JOIN food_ingredients fi
        ON i.ingredient_id = fi.ingredient_id
        WHERE fi.food_id = ?
    ");

    $stmt->execute([$id]);
    $food['ingredients'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $response->getBody()->write(json_encode($food));

    return $response->withHeader('Content-Type', 'application/json');

});





// GET /api/foods/search/{name}
$app->get('/api/foods/search/{name}', function (Request $request, Response $response, $args) use ($pdo, $validToken) {

    checkToken($request, $validToken);

    $name = "%" . $args['name'] . "%";

    $stmt = $pdo->prepare("
        SELECT
            f.food_id,
            f.food_name,
            c.category_name,
            o.origin_name,
            f.instructions
        FROM foods f
        JOIN categories c ON f.category_id = c.category_id
        JOIN origins o ON f.origin_id = o.origin_id
        WHERE f.food_name LIKE ?
    ");

    $stmt->execute([$name]);
    $foods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($foods as &$food) {

        $stmt2 = $pdo->prepare("
            SELECT ingredient_name
            FROM ingredients i
            JOIN food_ingredients fi
            ON i.ingredient_id = fi.ingredient_id
            WHERE fi.food_id = ?
        ");

        $stmt2->execute([$food['food_id']]);
        $food['ingredients'] = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    }

    $response->getBody()->write(json_encode($foods));

    return $response->withHeader('Content-Type', 'application/json');

});



// Run the application (MUST BE LAST)
$app->run();