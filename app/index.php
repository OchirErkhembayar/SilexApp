<?php
declare(strict_types=1);

use App\Classes\Car\CarRepository;
use App\Classes\Cart\CartRepository;
use App\Classes\Database\DatabaseConnection;
use App\Classes\Order\OrderRepository;
use App\Classes\User\UserRepository;
use App\Controllers\Cars\CarController;
use App\Controllers\Cart\CartController;
use App\Controllers\Orders\OrderController;
use App\Controllers\Users\UserController;
use App\Services\Authorization;
use App\Services\FormChecker;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/src/Views',
));

$app['session'] = new Symfony\Component\HttpFoundation\Session\Session();
$app['session']->start();
$app['app.database'] = fn($c) => new DatabaseConnection("silexCars");
$app['app.repository.car'] = fn($c) => new CarRepository($c['app.database']);
$app['app.controller.car'] = fn($c) => new CarController($c['app.repository.car']);
$app['app.repository.cart'] = fn($c) => new CartRepository($c['app.database']);
$app['app.controller.cart'] = fn($c) => new CartController($c['app.repository.cart']);
$app['app.repository.order'] = fn($c) => new OrderRepository($c['app.database']);
$app['app.controller.order'] = fn($c) => new OrderController($c['app.repository.order']);
$app['app.repository.user'] = fn($c) => new UserRepository($c['app.database']);
$app['app.controller.user'] = fn($c) => new UserController($c['app.repository.user']);
if (!$app['session']->isEmpty('user')) {
    $app['twig']->addGlobal('username', $app['session']->get('user')["username"]);
    $app['twig']->addGlobal('logged_in', true);
    $app['twig']->addGlobal('balance', $app['session']->get('user')['balance']);
}

$app->get('/', function () use ($app) {
    try {
        return $app['twig']->render('pages/index.html.twig', [
            'title' => "Cars",
            'content' => 'Just cars, that\'s it',
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/about', function () use ($app) {
    try {
        return $app['twig']->render('pages/about.html.twig', [
            'content' => 'Basically just cars.'
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/cars/new-car', function () use ($app) {
    try {
        return $app['twig']->render('cars/new.html.twig', []);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
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
    $formChecker = new FormChecker();
    if (!$formChecker->checkAddCarInputs($paramsArray)) {
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
    $cart = $app['app.controller.cart']->getCart($app['session']->get('user')["id"]);
    $cartItems = $app['app.controller.cart']->getCartItems($cart->cart_id);
    return $app['twig']->render('cart/cart.html.twig', [
        'cart_items' => $cartItems,
        'have_cars' => count($cartItems) > 0
    ]);
})->bind('cart');

$app->post('/cart/add-to-cart', function (Request $request) use ($app) {
    $params = $request->request;
    $cart = $app['app.controller.cart']->getCart($app['session']->get('user')["id"]);
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
    return $app['twig']->render('orders/orders.html.twig', [
        'orders' => $app['app.controller.order']->getOrders($app['session']->get('user')["id"])
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
    $app['app.controller.order']->save($app['session']->get('user')["id"]);
    return $app->redirect($app["url_generator"]->generate("orders"));
});

$app->get('/cart/quantity', function() use ($app) {
    return json_encode($app['app.controller.cart']->getCartQuantity($app['session']->get('user')["id"]));
});

$app->get('/users/details', function () use ($app) {
    $user = $app['app.controller.user']->getUserDetails($app['session']->get('user')["id"]);
    return json_encode($user);
});

$app->get('/auth/login', function () use ($app) {
   return $app['twig']->render('/pages/login.html.twig', []);
})->bind("login");

$app->get('/auth/logout', function () use ($app) {
   $app['session']->clear();
    return $app->redirect($app['url_generator']->generate("cars"));
});

$app->post('/auth/authenticate', function(Request $request) use ($app) {
   $params = $request->request;
   $authorization = new Authorization($app['app.repository.user']);
   $user = $authorization->login($params->get("username"), $params->get("password"));
   if (!$user) {
       return $app->redirect($app['url_generator']->generate("login"));
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