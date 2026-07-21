<?php
namespace App\Features\TrenNetValuePerEmiten\Services;

use App\Features\TrenNetValuePerEmiten\Contracts\NetValuePerEmitenContract;
use App\Features\TrenNetValuePerEmiten\Exceptions\NetValuePerEmitenException;
use Illuminate\Support\Facades\Log;

final class NetValuePerEmitenService
{
    public function __construct(
        private readonly NetValuePerEmitenContract $netValuePerEmitenRepository)
    {}

    public function getNetValuePerEmiten(string $stockCode, string $startDate, string $endDate)
    {
        try {
            return $this->netValuePerEmitenRepository->getNetValuePerEmiten($stockCode, $startDate, $endDate);
        } catch (\Throwable $exception) {
            Log::error('Failed to get net value per emiten: ',[
                'exception' => $exception
            ]
            );
            throw  new NetValuePerEmitenException('Failed to get net value per emiten', 0, $exception);
        }
    }
}

?>