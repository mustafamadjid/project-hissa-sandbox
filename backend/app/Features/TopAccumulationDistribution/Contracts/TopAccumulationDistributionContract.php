<?php

namespace App\Features\TopAccumulationDistribution\Contracts;

interface TopAccumulationDistributionContract
{
    public function getTopAccumulationDistribution(string $startDate, string $endDate, int $limit): array;
}
