<?php
declare(strict_types=1);

set_time_limit(5);
ini_set('memory_limit','16M');
phpinfo();
exit();

\error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

require_once __DIR__ . '/vendor/autoload.php';

use App\Classes\Car\CarRepository;
use App\Classes\Cart\CartRepository;
use App\Services\Database\DatabaseConnection;
use App\Classes\Order\OrderRepository;
use App\Classes\User\UserRepository;
use App\Controllers\Cars\CarController;
use App\Controllers\Cart\CartController;
use App\Controllers\Orders\OrderController;
use App\Controllers\Users\UserController;
use App\Services\Users\UserAuthorization;
use App\Services\FormChecker;
use Services\Transactions\Transaction;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/src/Views',
));
$app->register(new Silex\Provider\SessionServiceProvider());



$app['app.database'] = fn($c) => new DatabaseConnection("silexCars");
$app['app.repository.car'] = fn($c) => new CarRepository($c['app.database']);
$app['app.controller.car'] = fn($c) => new CarController($c['app.repository.car']);
$app['app.repository.cart'] = fn($c) => new CartRepository($c['app.database']);
$app['app.controller.cart'] = fn($c) => new CartController($c['app.repository.cart']);
$app['app.repository.order'] = fn($c) => new OrderRepository($c['app.database']);
$app['app.controller.order'] = fn($c) => new OrderController($c['app.repository.order']);
$app['app.repository.user'] = fn($c) => new UserRepository($c['app.database']);
$app['app.controller.user'] = fn($c) => new UserController($c['app.repository.user']);
$app['app.service.userAuthorization'] = fn($c) => new UserAuthorization($app['app.repository.user']);
$app->before(function (Request $request) use ($app) {
    if ($app['session']->get('user')) {
        $app['twig']->addGlobal('username', $app['session']->get('user')["username"]);
        $app['twig']->addGlobal('logged_in', true);
        $app['twig']->addGlobal('balance', $app['session']->get('user')['balance']);
        $app['twig']->addGlobal('id', $app['session']->get('user')["id"]);
    }
});

$app->get('/', function () use ($app) {
    return $app['twig']->render('pages/index.html.twig', [
        'title' => "Cars",
        'content' => 'Just cars, that\'s it',
    ]);
});

$app->get('/about', function () use ($app) {
        return $app['twig']->render('pages/about.html.twig', [
            'content' => 'Basically just cars.'
        ]);
});

$app->get('/cars/new-car', function () use ($app) {
    return $app['twig']->render('cars/new.html.twig', []);
})->bind("new-car");

$app->get('/cars', function () use ($app) {
    $id = $app['session']->get('user') ? $app['session']->get('user')["id"] : 0;
    return $app['twig']->render('cars/index.html.twig', [
        'cars' => $app['app.controller.car']->getAll($id)
    ]);
})->bind("cars");

$app->post('/cars/add-car', function (Request $request) use ($app) {
    $params = $request->request;
    $paramsArray = [
        "name" => $params->get("name"),
        "brand" => $params->get("brand"),
        "model" => $params->get("model"),
        "url" => $params->get("url"),
        "price" => $params->get("price"),
        "horsepower" => $params->get("horsepower")
    ];
    if (!FormChecker::checkAddCarInputs($paramsArray)) {
        return $app['twig']->render('cars/new.html.twig', [
            "error" => true
        ]);
    };
    $app['app.controller.car']->save($paramsArray, $app['session']->get('user')["id"]);
    return $app->redirect($app["url_generator"]->generate("cars"));
});

$app->post('/cars/delete-car', function (Request $request) use ($app) {
    $params = $request->request;
    $id = $params->get("id");
    $app['app.controller.car']->delete((int)$id);
    return $app->redirect($app["url_generator"]->generate("cars"));
});

$app->get('/cars/{id}', function (Request $request, $id) use ($app) {
    return $app['twig']->render('cars/show.html.twig', [
        'car' => $app['app.controller.car']->getOne((int)$id)
    ]);
});

$app->get('/cart', function () use ($app) {
    $cart = $app['app.controller.cart']->findByUserId($app['session']->get('user')["id"]);
    $cartItems = $app['app.controller.cart']->getCartItems($cart->cart_id);
    return $app['twig']->render('cart/cart.html.twig', [
        'cart_items' => $cartItems,
        'have_cars' => count($cartItems) > 0
    ]);
})->bind('cart');

$app->post('/cart/add-to-cart', function (Request $request) use ($app) {
    $params = $request->request;
    $cart = $app['app.controller.cart']->findByUserId($app['session']->get('user')["id"]);
    $app['app.controller.cart']->addToCart((int)$params->get("id"), (int)$cart->cart_id);
    return $app->redirect($app["url_generator"]->generate("cart"));
});

$app->post('/cart/delete-from-cart', function (Request $request) use ($app) {
    $params = $request->request;
    $app['app.controller.cart']->removeFromCart((int)$params->get("id"));
    return $app->redirect($app["url_generator"]->generate("cart"));
});

$app->post('/cart/edit-quantity', function (Request $request) use ($app) {
    $body = json_decode($request->getContent());
    $id = $body->id;
    $quantity = $body->quantity;
    $result = $app['app.controller.cart']->editCartQuantity((int)$id, (int)$quantity);
    return json_encode($result);
});

$app->get('/orders', function () use ($app) {
    $orders = $app['app.controller.order']->getOrdersById($app['session']->get('user')["id"]);
    return $app['twig']->render('orders/orders.html.twig', [
        'orders' => $orders
    ]);
})->bind('orders');

$app->get('/orders/{id}', function (Request $request, $id) use ($app) {
    $orderDetails = $app['app.controller.order']->getOrder((int)$id);
    $order_items = $orderDetails["order_items"];
    $order = $orderDetails["order"];
    return $app['twig']->render('orders/order.html.twig', [
        'order_items' => $order_items,
        'order' => $order
    ]);
});

$app->post('/orders/create-order', function () use ($app) {
//    $transaction = new Transaction($app['app.repository.cart'], $app['app.repository.order'],
//        $app['app.repository.car']);

    $app['app.controller.order']->save($app['session']->get('user')["id"]);
    return $app->redirect($app["url_generator"]->generate("orders"));
});

$app->get('/cart/quantity', function () use ($app) {
    return json_encode($app['app.controller.cart']->getCartQuantity($app['session']->get('user')["id"]));
});

$app->get('/users/add-balance', function () use ($app) {
    return $app['twig']->render('users/add-balance.html.twig', []);
});

$app->post('/users/add-money', function (Request $request) use ($app) {
    $params = $request->request;
    $amount = $params->get('amount');
    $app['app.controller.user']->addBalance($app['session']->get('user')["id"], floatval($amount));
    return $app->redirect($app["url_generator"]->generate("cars"));
});


$app->get('/users/details', function () use ($app) {
    $user = $app['app.controller.user']->getUserDetails($app['session']->get('user')["id"]);
    return json_encode($user);
});

$app->get('/login-page', function () use ($app) {
    return $app['twig']->render('/users/login.html.twig', []);
});

$app->get('/signup-form', function () use ($app) {
    return $app['twig']->render('/users/signup.html.twig', []);
});

$app->post('/auth/create-user', function (Request $request) use ($app) {
    $params = $request->request;
    $username = $params->get('username');
    $email = $params->get('email');
    $password = $params->get('password');
    $formErrors = FormChecker::checkSignupCredentials($username, $email, $password);
    if ($formErrors["hasErrors"]) {
        return $app['twig']->render('users/signup.html.twig', [
            'usernameError' => $formErrors["usernameError"],
            'passwordError' => $formErrors["passwordError"],
            'emailError' => $formErrors["emailError"],
            'username' => $username,
            'email' => $email
        ]);
    }
    $takenFields = $app['app.service.userAuthorization']->checkIfUserExists($username, $email);
    if ($takenFields["taken"]) {
        return $app['twig']->render('users/signup.html.twig', [
            'usernameError' => $takenFields["username"],
            'emailError' => $takenFields["email"],
            'username' => $username,
            'email' => $email
        ]);
    }
    $user = $app['app.repository.user']->createUser($username, $email, $password, $app['app.repository.cart']);
    if (!$user) {
        throw new \http\Exception\RuntimeException("Failed to create user. Either something is wrong with docker or this coder is crap.");
    }
    $app['session']->set('user', [
        "id" => $user->user_id,
        "username" => $user->username,
        "balance" => $user->balance
    ]);
    return $app->redirect($app['url_generator']->generate("cars"));
})->bind("signup");

$app->get('/auth/logout', function () use ($app) {
    $app['session']->clear();
    return $app->redirect($app['url_generator']->generate("cars"));
});

$app->post('/auth/authenticate', function (Request $request) use ($app) {
    $params = $request->request;
    $username = $params->get('username');
    $password = $params->get('password');
    $user = $app['app.repository.user']->authenticateAndFindUser($username, $password);
    if (!$user) {
        return $app['twig']->render('users/login.html.twig', [
            'errors' => true
        ]);
    }
    $app['session']->set('user', [
        "id" => $user->user_id,
        "username" => $user->username,
        "balance" => $user->balance
    ]);
    return $app->redirect($app['url_generator']->generate("cars"));
});

$app->error(function ($e) use ($app) {
    return $app['twig']->render('pages/500.html.twig', [
        'error' => $e->getMessage()
    ]);
});

$app->run();