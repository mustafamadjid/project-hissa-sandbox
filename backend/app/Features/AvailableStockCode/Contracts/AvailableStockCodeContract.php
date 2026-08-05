<?php

namespace App\Features\AvailableStockCode\Contracts;

interface AvailableStockCodeContract
{
    public function getStockCodes(): array;
}
