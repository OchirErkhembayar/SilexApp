<?php
declare(strict_types=1);

namespace App\Services;

class FormChecker
{
    /**
     * @param array<string,string|int|float> $params
     * @return bool
     */
    public function checkAddCarInputs(array $params): bool
    {
        foreach ($params as $value)
        {
            if (empty(trim(strval($value)))) {
                return false;
            }
        }
        return true;
    }
}