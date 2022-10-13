<?php

namespace Test\Services;

use App\Services\FormChecker;
use PHPUnit\Framework\TestCase;

class FormCheckerTest extends TestCase
{
    private FormChecker $formChecker;
    /**
     * @var array<string,string|float|int> $params
     */
    private array $params;

    public function setUp(): void
    {
        parent::setUp();
        $this->formChecker = new FormChecker();
        $this->params = [
            "name" => "name",
            "brand" => "brand",
            "model" => "model",
            "url" => "url",
            "price" => 2500.50,
            "horsepower" => 1500
        ];
    }

    /**
     * @test
     */
    public function it_will_return_false_for_empty_field(): void
    {
        $params = $this->params;
        $params["name"] = "";
        $this->assertFalse($this->formChecker->checkAddCarInputs($params));
    }

    /**
     * @test
     */
    public function it_will_return_false_for_price_as_non_float_or_int(): void
    {
        $params = $this->params;
        $params["price"] = "";
        $this->assertFalse($this->formChecker->checkAddCarInputs($params));
    }
}