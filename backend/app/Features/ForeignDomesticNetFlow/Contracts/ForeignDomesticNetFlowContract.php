<?php

namespace App\Features\ForeignDomesticNetFlow\Contracts;

use Illuminate\Support\Collection;

interface ForeignDomesticNetFlowContract
{
    public function getNetFlow(string $stockCode, string $startDate, string $endDate): Collection;
}
