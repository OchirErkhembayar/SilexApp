<?php
declare(strict_types=1);

use App\Classes\Car\CarRepository;
use App\Classes\Cart\CartRepository;
use App\Classes\Database\DatabaseConnection;
use App\Classes\Order\OrderRepository;
use App\Controllers\Cars\CarController;
use App\Controllers\Cart\CartController;
use App\Controllers\Orders\OrderController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

require_once __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/src/Views',
));

$app['app.database'] = fn($c) => new DatabaseConnection("silexCars");
$app['app.repository.car'] = fn($c) => new CarRepository($c['app.database']);
$app['app.controller.car'] = fn($c) => new CarController($c['app.repository.car']);
$app['app.repository.cart'] = fn($c) => new CartRepository($c['app.database']);
$app['app.controller.cart'] = fn($c) => new CartController($c['app.repository.cart']);
$app['app.repository.order'] = fn($c) => new OrderRepository($c['app.database']);
$app['app.controller.order'] = fn($c) => new OrderController($c['app.repository.order']);

$app->get('/', function () use ($app) {
    try {
        return $app['twig']->render('pages/index.html.twig', [
            'title' => 'Cars',
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
});

$app->get('/cars', function () use ($app) {
    try {
        return $app['twig']->render('cars/index.html.twig', [
            'cars' => $app['app.controller.car']->getAll()
        ]);
    } catch (Exception $e) {
        echo $e->getMessage();
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
})->bind("cars");

$app->post('/cars/add-car', function (Request $request) use ($app) {
    try {
        $params = $request->request;
        $paramsArray = [
            "name" => $params->get("name"),
            "brand" => $params->get("brand"),
            "model" => $params->get("model"),
            "url" => $params->get("url"),
            "price" =>$params->get("price"),
            "horsepower" =>$params->get("horsepower")
        ];
        $app['app.controller.car']->save($paramsArray);
        return $app->redirect($app["url_generator"]->generate("cars"));
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->post('/cars/delete-car', function (Request $request) use ($app) {
    try {
        $params = $request->request;
        $id = $params->get("id");
        $app['app.controller.car']->delete((int)$id);
        return $app->redirect($app["url_generator"]->generate("cars"));
    } catch (Exception $e) {
        echo $e->getMessage();
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/cars/{id}', function (Request $request, $id) use ($app) {
    try {
        return $app['twig']->render('cars/show.html.twig', [
            'car' => $app['app.controller.car']->getOne((int)$id)
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/cart', function () use ($app) {
    try {
        $cart = $app['app.controller.cart']->getCart();
        $cartItems = $app['app.controller.cart']->getCartItems($cart->cart_id);
        return $app['twig']->render('cart/cart.html.twig', [
            'cart_items' => $cartItems,
            'have_cars' => count($cartItems) > 0
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
})->bind('cart');

$app->post('/cart/add-to-cart', function (Request $request) use ($app) {
    try {
        $params = $request->request;
        $cart = $app['app.controller.cart']->getCart();
        $app['app.controller.cart']->addToCart((int)$params->get("id"), (int)$cart->cart_id);
        return $app->redirect($app["url_generator"]->generate("cart"));
    } catch (Exception $e) {
        echo $e->getMessage();
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->post('/cart/delete-from-cart', function (Request $request) use ($app) {
    try {
        $params = $request->request;
        $app['app.controller.cart']->removeFromCart((int)$params->get("id"));
        return $app->redirect($app["url_generator"]->generate("cart"));
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/orders', function () use ($app) {
    try {
        return $app['twig']->render('orders/orders.html.twig', [
            'orders' => $app['app.controller.order']->getOrders()
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
})->bind('orders');

$app->get('/orders/{id}', function (Request $request, $id) use ($app) {
    try {
        $orderDetails = $app['app.controller.order']->getOrder((int)$id);
        $order_items = $orderDetails["order_items"];
        $order = $orderDetails["order"];
        return $app['twig']->render('orders/order.html.twig', [
            'order_items' => $order_items,
            'order' => $order
        ]);
    } catch (Exception $e) {
        echo $e->getMessage();
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->post('/orders/create-order', function () use ($app) {
    try {
        $app['app.controller.order']->save();
        return $app->redirect($app["url_generator"]->generate("orders"));
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/500', function() use ($app) {
    $app['twig']->render('/pages/500.html.twig', []);
})->bind('500');

$app->run();