<?php
declare(strict_types=1);

namespace Test\Controllers\Cart;

use App\Classes\Cart\CartRepository;
use App\Classes\Database\DatabaseConnection;
use App\Controllers\Cart\CartController;
use PHPUnit\Framework\TestCase;

class CartControllerTest extends TestCase
{
    private CartController $cartController;

    public function setUp(): void
    {
        parent::setUp();
        $dbc = new DatabaseConnection("silexCarsTest");
        $this->cartController = new CartController(new CartRepository($dbc));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_get_the_cart(): void
    {
        $cart = $this->cartController->getCart(1);
        $this->assertIsObject($cart);
    }

    /**
     * @test
     */
    public function it_can_add_car_to_cart(): void
    {
        $cart = $this->cartController->getCart(1);
        $result = $this->cartController->addToCart(rand(1, 5), $cart->cart_id);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_can_get_cart_items(): void
    {
        $cart = $this->cartController->getCart(1);
        $cartItems = $this->cartController->getCartItems($cart->cart_id);
        $this->assertIsArray($cartItems);
    }

    /**
     * @test
     */
    public function it_can_delete_from_cart(): void
    {
        $cart = $this->cartController->getCart(1);
        $this->cartController->addToCart(rand(0, 4), $cart->cart_id);
        $cartItems = $this->cartController->getCartItems($cart->cart_id);
        $cartItemId = $cartItems[0]->cart_item_id;
        $result = $this->cartController->removeFromCart($cartItemId);
        $this->assertTrue($result);
    }
}