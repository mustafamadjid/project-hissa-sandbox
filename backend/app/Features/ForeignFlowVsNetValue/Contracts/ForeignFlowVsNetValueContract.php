<?php

namespace App\Features\ForeignFlowVsNetValue\Contracts;

use Illuminate\Support\Collection;

interface ForeignFlowVsNetValueContract
{
    public function getScatterData(string $startDate, string $endDate, ?array $stockCodes): Collection;
}
