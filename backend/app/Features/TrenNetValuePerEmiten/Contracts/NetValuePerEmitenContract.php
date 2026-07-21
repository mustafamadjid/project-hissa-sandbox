<?php
namespace App\Features\TrenNetValuePerEmiten\Contracts;

interface NetValuePerEmitenContract
{
    public function getNetValuePerEmiten(string $stockCode, string $startDate, string $endDate);
}
?>