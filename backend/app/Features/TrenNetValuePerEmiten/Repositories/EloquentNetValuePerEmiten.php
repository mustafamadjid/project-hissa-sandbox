<?php
namespace App\Features\TrenNetValuePerEmiten\Repositories;

use App\Features\TrenNetValuePerEmiten\Contracts\NetValuePerEmitenContract;
use App\Features\TrenNetValuePerEmiten\Models\NetValuePerEmiten;
use Override;

final class EloquentNetValuePerEmiten implements NetValuePerEmitenContract
{
    #[Override]
    public function getNetValuePerEmiten(string $stockCode, string $startDate, string $endDate)
    {
        return NetValuePerEmiten::query()
        ->select([
            'netbs_date as date',
            'netbs_stock_code as stock_code',
            'netval as net_value',
            'stock_acc/dist as classification',
        ])
        ->whereBetween('netbs_date', [$startDate, $endDate])
        ->where('netbs_stock_code', $stockCode)
        ->orderBy('netbs_date')
        ->get();
    }
}
?>