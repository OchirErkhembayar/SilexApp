<?php
declare(strict_types=1);

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
        $carController = new CarController();
        $cars = $carController->getAll();
        return $app['twig']->render('cars/index.html.twig', [
            'cars' => $cars
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
        $carController = new CarController();
        $carController->save($params);
        return $app->redirect($app["url_generator"]->generate("cars"));
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->post('/cars/delete-car', function (Request $request) use ($app) {
    try {
        $params = $request->request;
        $carController = new CarController();
        $carController->delete($params);
        return $app->redirect($app["url_generator"]->generate("cars"));
    } catch (Exception $e) {
        echo $e->getMessage();
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/cars/{id}', function (Request $request, $id) use ($app) {
    try {
        $carController = new CarController();
        $car = $carController->getOne($id);
        return $app['twig']->render('cars/show.html.twig', [
            'car' => $car
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/cart', function () use ($app) {
    try {
        $cartController = new CartController();
        $cart = $cartController->getCart();
        $cartItems = $cartController->getCartItems($cart->cart_id);
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
        $cartController = new CartController();
        $cart = $cartController->getCart();
        $cartController->addToCart($params->get("id"), $cart->cart_id);
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
        $cartController = new CartController();
        $cartController->removeFromCart($params->get("id"));
        return $app->redirect($app["url_generator"]->generate("cart"));
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
});

$app->get('/orders', function () use ($app) {
    try {
        $orderController = new OrderController();
        $orders = $orderController->getOrders();
        return $app['twig']->render('orders/orders.html.twig', [
            'orders' => $orders
        ]);
    } catch (Exception $e) {
        $subRequest = Request::create('/500');
        return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }
})->bind('orders');

$app->get('/orders/{id}', function (Request $request, $id) use ($app) {
    try {
        $orderController = new OrderController();
        $orderDetails = $orderController->getOrder($id);
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
        $orderController = new OrderController();
        $orderController->save();
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