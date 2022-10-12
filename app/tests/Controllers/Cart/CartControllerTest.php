<?php
declare(strict_types=1);

namespace Test\Controllers\Cart;

use App\Classes\Cart\Cart;
use App\Classes\Cart\CartRepository;
use App\Classes\Database\DatabaseConnection;
use App\Controllers\Cart\CartController;
use PHPUnit\Framework\TestCase;

class CartControllerTest extends TestCase
{
    private CartController $cartController;
    private Cart $cart;

    public function setUp(): void
    {
        parent::setUp();
        $dbc = new DatabaseConnection("silexCarsTest");
        $this->cartController = new CartController(new CartRepository($dbc));
        $this->cart = $this->cartController->getCart();
    }

    /**
     * @test
     * @return void
     */
    public function it_can_get_the_cart(): void
    {
        $this->assertIsObject($this->cart);
    }

    /**
     * @test
     */
    public function it_can_add_car_to_cart(): void
    {
        $result = $this->cartController->addToCart(rand(0, 4), $this->cart->cart_id);
        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function it_can_get_cart_items(): void
    {
        $cartItems = $this->cartController->getCartItems($this->cart->cart_id);
        $this->assertIsArray($cartItems);
    }

    /**
     * @test
     */
    public function it_can_delete_from_cart(): void
    {
        $this->cartController->addToCart(rand(0, 4), $this->cart->cart_id);
        $cartItems = $this->cartController->getCartItems($this->cart->cart_id);
        $cartItemId = $cartItems[0]->cart_item_id;
        $result = $this->cartController->removeFromCart($cartItemId);
        $this->assertTrue($result);
    }
}